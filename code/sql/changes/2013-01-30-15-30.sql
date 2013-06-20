CREATE TABLE pjs.document_user_states(
	id serial PRIMARY KEY,
	name varchar
);
GRANT ALL ON pjs.document_user_states TO iusrpmt;
INSERT INTO pjs.document_user_states(name) VALUES ('Active'), ('Inactive');

ALTER TABLE pjs.document_users ADD COLUMN state_id int REFERENCES pjs.document_user_states(id) DEFAULT 1;
ALTER TABLE pjs.document_users ALTER COLUMN state_id SET NOT NULL;


CREATE TABLE pwt.pjs_revision_details(
	id bigserial PRIMARY KEY,
	document_id bigint REFERENCES pwt.documents(id),
	revision_id bigint REFERENCES pwt.document_revisions(id),
	change_user_ids integer[]	
);

GRANT ALL ON pwt.pjs_revision_details TO iusrpmt;
 
 ALTER TABLE pwt.documents ADD COLUMN has_unprocessed_changes boolean DEFAULT false;
 ALTER TABLE pwt.documents ALTER COLUMN has_unprocessed_changes SET NOT NULL;
 
 DROP TYPE ret_spCheckIfUserCanImportPjsDocument CASCADE;
CREATE TYPE ret_spCheckIfUserCanImportPjsDocument AS (
	is_allowed boolean
);

CREATE OR REPLACE FUNCTION spCheckIfUserCanImportPjsDocument(
	pDocumentId int,
	pUid int
)
  RETURNS ret_spCheckIfUserCanImportPjsDocument AS
$BODY$
DECLARE
	lRes ret_spCheckIfUserCanImportPjsDocument;	
	lSubmittedToPjsDocumentState int = 2;	
BEGIN
	lRes.is_allowed = true;	
	IF NOT EXISTS (
		SELECT * 
		FROM pwt.documents
		WHERE id = pDocumentId AND state = lSubmittedToPjsDocumentState
	) THEN 
		lRes.is_allowed = false;
	END IF;
	
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCheckIfUserCanImportPjsDocument(
	pDocumentId int,
	pUid int
) TO iusrpmt;

DROP TYPE ret_spGetDocumentDataByInstance CASCADE;
CREATE TYPE ret_spGetDocumentDataByInstance AS (
	document_id bigint,
	display_instance_in_tree int,
	root_instance_id int,
	document_name varchar,
	lock_usr_id bigint,
	is_locked int,
	xsl_dir_name varchar,
	document_is_readonly int,
	document_has_unprocessed_changes int,
	document_xml xml
);

CREATE OR REPLACE FUNCTION spGetDocumentDataByInstance(
	pInstanceId bigint
)
  RETURNS ret_spGetDocumentDataByInstance AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentDataByInstance;		
	BEGIN
		SELECT INTO lRes i.document_id, CASE WHEN char_length(i.pos) > 2 THEN i.display_in_tree::int ELSE 1 END, null, d.name, d.lock_usr_id, d.is_locked::int, tem.xsl_dir_name
		FROM pwt.document_object_instances i
		JOIN pwt.documents d ON d.id = i.document_id
		LEFT JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		LEFT JOIN pwt.templates tem ON tem.id = dto.template_id
		WHERE i.id = pInstanceId;
		
		SELECT INTO lRes.root_instance_id id 
		FROM pwt.document_object_instances
		WHERE document_id = lRes.document_id AND char_length(pos) = 2 
		ORDER BY pos ASC LIMIT 1;
		
		SELECT INTO lRes.document_is_readonly, lRes.document_has_unprocessed_changes, lRes.document_xml
			s.is_readonly::int, d.has_unprocessed_changes::int, d.doc_xml
		FROM pwt.document_states s
		JOIN pwt.documents d ON d.state = s.id
		WHERE d.id = lRes.document_id;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentDataByInstance(
	pInstanceId bigint
) TO iusrpmt;


DROP TYPE ret_spImportPjsDocumentVersion CASCADE;
CREATE TYPE ret_spImportPjsDocumentVersion AS (
	result int
);

CREATE OR REPLACE FUNCTION spImportPjsDocumentVersion(
	pDocumentId int,
	pXml xml,	
	pUid int
)
  RETURNS ret_spImportPjsDocumentVersion AS
$BODY$
DECLARE
	lRes ret_spImportPjsDocumentVersion;
	
	lNewRevisionId bigint;
	lPjsRevisionTypeId int = 2;
	lReturnedFromPjsDocumentState int = 3;
	lPjsAuthorVersionType int = 1;
	
	lRevisionId bigint;
	lPjsAuthorVersionId bigint;
	lInstances xml[];
	lFields xml[];
	
	lIterObjects int;
	
	lCurrentInstance xml;	
	lTemp xml[];
	lImportIsAllowed boolean;
BEGIN
	SELECT INTO lImportIsAllowed is_allowed FROM spCheckIfUserCanImportPjsDocument(pDocumentId, pUid);
	
	IF coalesce(lImportIsAllowed, false) = false THEN
		RAISE EXCEPTION 'pwt.pjsImportThisUserCannotImportDocumentAtThisPoint';
	END IF;
	
	-- Insert a new pjs version
	INSERT INTO pwt.document_revisions(document_id, "name", template_id, createuid, cached_authors, state, papertype_id, journal_id, doc_xml, revision_type)
	SELECT pDocumentId, d.name, d.template_id, pUid, cached_authors, d.state, d.papertype_id, d.journal_id, pXml, lPjsRevisionTypeId
	FROM pwt.documents d 
	WHERE d.id = pDocumentId;
	
	lRevisionId = currval('pwt.document_revisions_id_seq');
	
	-- Get the version usr_ids from pjs
	SELECT INTO lPjsAuthorVersionId dv.id
	FROM pjs.document_versions dv
	JOIN pjs.pwt_documents pd ON pd.document_id = dv.document_id
	WHERE dv.version_type_id = lPjsAuthorVersionType AND pd.pwt_id = pDocumentId 
	ORDER BY dv.id DESC LIMIT 1;
	
	INSERT INTO pwt.pjs_revision_details(document_id, revision_id, change_user_ids)
	SELECT pDocumentId, lRevisionId, pdv.change_user_ids
	FROM pjs.pwt_document_versions pdv
	WHERE pdv.version_id = lPjsAuthorVersionId;
	
	
	--Update the document xml
	UPDATE pwt.documents SET
		doc_xml = pXml, 
		state = lReturnedFromPjsDocumentState,
		has_unprocessed_changes = true
	WHERE id = pDocumentId;
	
	--Update all the instances of the first lvl (the other are updated recursively)
	lInstances = xpath('//objects/*[@instance_id > 0]', pXml);	
	FOR lIterObjects IN 
		1 .. coalesce(array_upper(lInstances, 1), 0) 
	LOOP
		lCurrentInstance = lInstances[lIterObjects];	
		PERFORM spImportPjsDocumentInstance(pDocumentId, lCurrentInstance, pUid);
	END LOOP;
	
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spImportPjsDocumentVersion(
	pDocumentId int,
	pXml xml,
	pUid int
) TO iusrpmt;


DROP TYPE IF EXISTS ret_spFetchPwtDocumentMetadata CASCADE;
CREATE TYPE ret_spFetchPwtDocumentMetadata AS (
	result int
);

CREATE OR REPLACE FUNCTION spFetchPwtDocumentMetadata(
	pDocumentId bigint,
	pDocumentXml xml
)
  RETURNS ret_spFetchPwtDocumentMetadata AS
$BODY$
	DECLARE
		lTempNodes xml[];
		lAuthorXPathVal xml[];
		lCorrespondingAuthorXPathVal xml[];
		lCorrespondingAuthorFlag int;
		lTempXml xml;
		lTaxon int[];
		lRes ret_spFetchPwtDocumentMetadata;	
		lIter int;		
		lAuthorId int;
		lAuthorRoleId int;
		lStripedTitle text;
		lTaxonClassifications int[];
		lChronologicalClassifications int[];
		lSubjectClassifications int[];
		lGeographicalClassifications int[];
		lSupportingAgenciesIds int[];
		lTaxonClassId int;
		lSubjectClassId int;
		lChronologicalClassId int;
		lGeographicalClassId int;
		lSupportingAgenciesId int;
		lSupportingAgenciesTxt varchar;
		lSupportingAgenciesTxtXpath xml[];
		lMediaTitleXpath xml[];
		lMediaAuthorsXpath xml[];
		lMediaTypeXpath xml[];
		lMediaDescriptionXpath xml;
		lMediaIdXpath xml[];
		lMediaTitle varchar;
		lMediaAuthors varchar;
		lMediaType varchar;
		lMediaId int;
		lPWTPaperTypeId int;
		
		lInactiveDocumentUserStateId int = 2;
		lActiveDocumentUserStateId int = 1;
	BEGIN		
		lAuthorRoleId = 11;
		
		--Fetch document paper type
		lTempNodes := xpath('/document/document_info/document_type/@id', pDocumentXml);
		IF array_upper(lTempNodes, 1) > 0 THEN
			lPWTPaperTypeId = spConvertAnyToInt(lTempNodes[1]);
			UPDATE pjs.documents SET
				pwt_paper_type_id = lPWTPaperTypeId
			WHERE id = pDocumentId;
		END IF;
		
		--Set journal_sections id
		UPDATE pjs.documents d
				SET journal_section_id = s.id
		FROM pjs.journal_sections s
		WHERE d.pwt_paper_type_id = s.pwt_paper_type_id AND d.id = pDocumentId;
		
		
		-- Fetch title
		lTempNodes := xpath('/document/objects//*[@object_id="9" or @object_id="153"]/fields/*[@id="3"]/value', pDocumentXml);
		IF array_upper(lTempNodes, 1) > 0 THEN
			lTempXml := spXmlConcatArr(xpath('/value/*|text()', lTempNodes[1]));
			SELECT INTO lStripedTitle regexp_replace(lTempXml::text, E'<(?!b|/b|i|/i|u|/u|sup|/sup|sub|/sub)[^>]*?>', '', 'g');
			UPDATE pjs.documents SET
				name = lStripedTitle
			WHERE id = pDocumentId;
		END IF;
		
		-- Fetch abstract
		lTempNodes := xpath('/document/objects//*[@object_id="15"]/fields/*[@id="18"]/value', pDocumentXml);
		IF array_upper(lTempNodes, 1) > 0 THEN
			lTempXml := spXmlConcatArr(xpath('/value/*|text()', lTempNodes[1]));
			SELECT INTO lTempXml regexp_replace(lTempXml::text, E'<(?!b|/b|i|/i|u|/u|sup|/sup|sub|/sub)[^>]*?>', '', 'g');
			UPDATE pjs.documents SET
				abstract = lTempXml::text
			WHERE id = pDocumentId;
		END IF;
		
		-- Fetch keywords
		lTempNodes := xpath('/document/objects//*[@object_id="15"]/fields/*[@id="19"]/value', pDocumentXml);
		IF array_upper(lTempNodes, 1) > 0 THEN
			lTempXml := spXmlConcatArr(xpath('/value/*|text()', lTempNodes[1]));
			SELECT INTO lTempXml regexp_replace(lTempXml::text, E'<(?!b|/b|i|/i|u|/u|sup|/sup|sub|/sub)[^>]*?>', '', 'g');
			UPDATE pjs.documents SET
				keywords = lTempXml::text
			WHERE id = pDocumentId;
		END IF;
		
		-- Fetch authors and corresponding author flag
		/*
		DELETE FROM pjs.document_users 
		WHERE document_id = pDocumentId AND role_id = lAuthorRoleId;
		*/
		
		UPDATE pjs.document_users SET
			state_id = lInactiveDocumentUserStateId
		WHERE document_id = pDocumentId AND role_id = lAuthorRoleId;
		
		
		lTempNodes := xpath('/document/objects//*[@object_id="9" or @object_id="153"]/*[@object_id="8"]/fields', pDocumentXml);
		FOR lIter IN 
			1 .. coalesce(array_upper(lTempNodes, 1), 0) 
		LOOP
			lAuthorXPathVal := xpath('/fields/*[@id="13"]/value/text()', lTempNodes[lIter]);
			lCorrespondingAuthorXPathVal = xpath('/fields/*[@id="15"]/value/@value_id', lTempNodes[lIter]);
			
			lAuthorId = spConvertAnyToInt(lAuthorXPathVal[1]);
			lCorrespondingAuthorFlag = spConvertAnyToInt(lCorrespondingAuthorXPathVal[1]);
			
			IF lAuthorId IS NOT NULL THEN
				IF lCorrespondingAuthorFlag IS NULL THEN 
					lCorrespondingAuthorFlag = 0;
				END IF;
				IF EXISTS (
					SELECT * 
					FROM pjs.document_users
					WHERE document_id = pDocumentId AND role_id = lAuthorRoleId AND uid = lAuthorId
				) THEN
					UPDATE pjs.document_users SET
						state_id = lActiveDocumentUserStateId
					WHERE document_id = pDocumentId AND role_id = lAuthorRoleId AND uid = lAuthorId;
				ELSE
					INSERT INTO pjs.document_users(document_id, role_id, uid, co_author) VALUES(pDocumentId, lAuthorRoleId, lAuthorId, lCorrespondingAuthorFlag);
				END IF;
			END IF;
		END LOOP;
	
		/*Document categories metadata*/
		-- Fetch all Taxon classifications
		lTempNodes := xpath('/document/objects//*[@object_id="82"]/fields/*[@id="244"]/value/@value_id', pDocumentXml);
		FOR lIter IN 1 .. coalesce(array_upper(lTempNodes, 1), 0)  LOOP
			lTaxonClassId = spConvertAnyToInt(lTempNodes[lIter]);
			lTaxonClassifications = lTaxonClassifications || lTaxonClassId;
		END LOOP;
		UPDATE pjs.documents SET taxon_categories = lTaxonClassifications WHERE id = pDocumentId;
		
		-- Fetch all Chronological classifications
		lTempNodes := xpath('/document/objects//*[@object_id="82"]/fields/*[@id="246"]/value/@value_id', pDocumentXml);
		FOR lIter IN 1 .. coalesce(array_upper(lTempNodes, 1), 0)  LOOP
			lChronologicalClassId = spConvertAnyToInt(lTempNodes[lIter]);
			lChronologicalClassifications = lChronologicalClassifications || lChronologicalClassId;
		END LOOP;
		UPDATE pjs.documents SET chronological_categories = lChronologicalClassifications WHERE id = pDocumentId;
		
		-- Fetch all Subject classifications
		lTempNodes := xpath('/document/objects//*[@object_id="82"]/fields/*[@id="245"]/value/@value_id', pDocumentXml);
		FOR lIter IN 1 .. coalesce(array_upper(lTempNodes, 1), 0)  LOOP
			lSubjectClassId = spConvertAnyToInt(lTempNodes[lIter]);
			lSubjectClassifications = lSubjectClassifications || lSubjectClassId;
		END LOOP;
		UPDATE pjs.documents SET subject_categories = lSubjectClassifications WHERE id = pDocumentId;
	
		-- Fetch all Geographical classifications
		lTempNodes := xpath('/document/objects//*[@object_id="82"]/fields/*[@id="247"]/value/@value_id', pDocumentXml);
		FOR lIter IN 1 .. coalesce(array_upper(lTempNodes, 1), 0)  LOOP
			lGeographicalClassId = spConvertAnyToInt(lTempNodes[lIter]);
			lGeographicalClassifications = lGeographicalClassifications || lGeographicalClassId;
		END LOOP;
		UPDATE pjs.documents SET geographical_categories = lGeographicalClassifications WHERE id = pDocumentId;
		
		-- Fetch Funding agencies ids
		lTempNodes := xpath('/document/objects//*[@object_id="143"]/fields/*[@id="406"]/value/@value_id', pDocumentXml);
		FOR lIter IN 
			1 .. coalesce(array_upper(lTempNodes, 1), 0) 
		LOOP
			lSupportingAgenciesId = spConvertAnyToInt(lTempNodes[lIter]);
			lSupportingAgenciesIds = lSupportingAgenciesIds || lSupportingAgenciesId;
		END LOOP;
		UPDATE pjs.documents SET supporting_agencies_ids = lSupportingAgenciesIds WHERE id = pDocumentId;
		
		-- Fetch Funding agencies texts
		lTempNodes := xpath('/document/objects//*[@object_id="143"]/fields/*[@id="407"]/value', pDocumentXml);
		FOR lIter IN 
			1 .. coalesce(array_upper(lTempNodes, 1), 0) 
		LOOP
			lSupportingAgenciesTxtXpath = xpath('/value/text()', lTempNodes[lIter]);
		END LOOP;
		UPDATE pjs.documents SET supporting_agencies_texts = lSupportingAgenciesTxtXpath[1]::text WHERE id = pDocumentId;
		/*Document categories metadata*/
			
		
		/*Document media*/
		lTempNodes := xpath('/document/objects//*[@object_id="56"]//*[@object_id="55"]/fields', pDocumentXml);
		FOR lIter IN 1 .. coalesce(array_upper(lTempNodes, 1), 0)  LOOP
			lMediaTitleXpath = xpath('/fields/*[@id="214"]/value/text()', lTempNodes[lIter]);
			lMediaAuthorsXpath = xpath('/fields/*[@id="215"]/value/text()', lTempNodes[lIter]);
			lMediaTypeXpath = xpath('/fields/*[@id="216"]/value/text()', lTempNodes[lIter]);
			lMediaDescriptionXpath = spXmlConcatArr(xpath('/fields/*[@id="217"]/value/*|text()', lTempNodes[lIter]));
			lMediaIdXpath = xpath('/fields/*[@id="222"]/value/text()', lTempNodes[lIter]);
			lMediaId := spConvertAnyToInt(lMediaIdXpath[1]);
			lMediaTitle := lMediaTitleXpath[1]::text;
			lMediaAuthors := lMediaAuthorsXpath[1]::text;
			lMediaType := lMediaTypeXpath[1]::text;
			
			INSERT INTO pjs.document_media(document_id, title, authors, type, description, file_id, filename) 
				VALUES(pDocumentId, lMediaTitle, lMediaAuthors, lMediaType, lMediaDescriptionXpath::text, lMediaId::int, 'oo_' || lMediaId::text);
		END LOOP;
		/*Document media*/
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spFetchPwtDocumentMetadata(
	pDocumentId bigint,
	pDocumentXml xml
) TO iusrpmt;
