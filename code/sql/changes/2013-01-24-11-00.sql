ALTER TABLE pwt.document_revisions ALTER COLUMN id TYPE bigint;

CREATE TABLE pwt.document_revision_types
(
  id serial NOT NULL PRIMARY KEY,
  "name" character varying
)
WITH (
  OIDS=FALSE
);
GRANT ALL ON TABLE pwt.document_revision_types TO pensoft;
GRANT ALL ON TABLE pwt.document_revision_types TO iusrpmt;

INSERT INTO pwt.document_revision_types(name) VALUES ('Regular revision'), ('PJS revision');

ALTER TABLE pwt.document_revisions ADD COLUMN revision_type int REFERENCES pwt.document_revision_types(id) DEFAULT 1;
ALTER TABLE pwt.document_revisions ALTER COLUMN revision_type SET NOT NULL;


DROP TYPE ret_spCreateDocumentFromPwtDocument CASCADE;
CREATE TYPE ret_spCreateDocumentFromPwtDocument AS (
	document_id bigint,
	event_id bigint,
	state_id int
);

CREATE OR REPLACE FUNCTION spCreateDocumentFromPwtDocument(
	pPwtDocumentId bigint,
	pJournalId int,
	pDocumentXml xml,
	pUid int,
	pEditor_Round_Type int
)
  RETURNS ret_spCreateDocumentFromPwtDocument AS
$BODY$
	DECLARE
		lRes ret_spCreateDocumentFromPwtDocument;	
		lOtherDocumentId bigint;
		lOtherDocumentState int;
		
		lOtherDocumentUid int;
		
		lVersionId bigint;
		lRoundId bigint;
		
		lSubmissionEventType int = 1;		
		lNewDocumentState int = 1;
		lWaitingAuthorVersionAfterReviewRoundDocumentState int = 9;		
		lInReviewDocumentState int = 3;		
		lPwtDocumentType int = 1;
		lAuthorSubmittedVersionType int = 1;	
		lSERoleId int = 3;
		lSEVersionTypeId int = 3;
				
		lRecord record;
		lSEVersionId bigint;
		
	BEGIN								
		
		SELECT INTO lOtherDocumentId, lOtherDocumentState, lOtherDocumentUid
			d.id, d.state_id, d.submitting_author_id
		FROM pjs.pwt_documents pd
		JOIN  pjs.documents d ON d.id = pd.document_id
		WHERE pd.pwt_id = pPwtDocumentId;
		
		lOtherDocumentState = coalesce(lOtherDocumentState, 0);
		IF lOtherDocumentId IS NOT NULL THEN
			lRes.document_id = lOtherDocumentId;				
			IF coalesce(lOtherDocumentUid, 0) <> pUid THEN
				RAISE EXCEPTION 'pjs.aDocumentWithTheSpecifiedPwtIdHasAlreadyBeenCreatedByAnotherUser';
			END IF;
			IF lOtherDocumentState <> lNewDocumentState AND lOtherDocumentState <> lWaitingAuthorVersionAfterReviewRoundDocumentState THEN				
				RAISE EXCEPTION 'pjs.aDocumentWithTheSpecifiedPwtIdAlreadyExistsAndItCannotBeUpdated';
			ELSEIF lOtherDocumentState = lNewDocumentState THEN
				lRes.state_id = lOtherDocumentState;
				RETURN lRes;
			END IF;
			
		END IF;
		
		IF lOtherDocumentId IS NULL THEN
			-- New document
			INSERT INTO pjs.documents(submitting_author_id, document_source_id, journal_id, document_type_id, document_review_type_id)
			VALUES (pUid, lPwtDocumentType, pJournalId, 1, 2);

			lRes.document_id = currval('pjs.documents_id_seq');
			
			INSERT INTO pjs.pwt_documents(document_id, pwt_id, createuid, journal_id) VALUES (lRes.document_id, pPwtDocumentId, pUid, pJournalId);
		ELSE
			-- New author version after review round
			UPDATE pjs.documents SET
				state_id = lInReviewDocumentState
			WHERE id = lRes.document_id;
		END IF;
		
		INSERT INTO pjs.document_versions(uid, version_num, version_type_id, document_id) VALUES (pUid, 1, lAuthorSubmittedVersionType, lRes.document_id);
		lVersionId = currval('pjs.document_versions_id_seq');
		
		INSERT INTO pjs.pwt_document_versions(version_id, "xml") VALUES (lVersionId, pDocumentXml);
		
		PERFORM spFetchPwtDocumentMetadata(lRes.document_id, pDocumentXml);
			
		-- creating new round (round_type (4) - Editor)
		SELECT INTO lRoundId id FROM spCreateDocumentRound(lRes.document_id, pEditor_Round_Type);	
		UPDATE pjs.document_review_rounds SET create_from_version_id = lVersionId WHERE id = lRoundId;
		
		-- Create editor versions for the round
		FOR lRecord IN
			SELECT * 
			FROM pjs.document_users
			WHERE document_id = lRes.document_id AND role_id = lSERoleId
		LOOP
			SELECT INTO lSEVersionId id
			FROM spCreateDocumentVersion(lRes.document_id, lRecord.uid, lSEVersionTypeId, lVersionId);
			
			INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id)
				VALUES (lRoundId, lRecord.id, lSEVersionId);
		END LOOP;
		
		UPDATE pjs.documents SET
			current_round_id = lRoundId
		WHERE id = lRes.document_id;
		
		-- creating submission event
		SELECT INTO lRes.event_id event_id FROM spCreateEvent(lSubmissionEventType, lRes.document_id, pUid, pJournalId, null, null);
		
		SELECT INTO lRes.state_id state_id 
		FROM pjs.documents 
		WHERE id = lRes.document_id;
		
		-- SET due dates for current round and users
		PERFORM pjs.spUpdateDueDates(1, lRes.document_id, lSubmissionEventType, lRoundId, NULL);
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateDocumentFromPwtDocument(
	pPwtDocumentId bigint,
	pJournalId int,
	pDocumentXml xml,
	pUid int,
	pEditor_Round_Type int
) TO iusrpmt;

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
BEGIN
	lRes.is_allowed = true;	
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCheckIfUserCanImportPjsDocument(
	pDocumentId int,
	pUid int
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
	
	--Update the document xml
	UPDATE pwt.documents SET
		doc_xml = pXml, 
		state = lReturnedFromPjsDocumentState
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


CREATE TYPE ret_spImportPjsDocumentInstance AS (
	result int
);

CREATE OR REPLACE FUNCTION spImportPjsDocumentInstance(
	pDocumentId int,
	pInstanceXml xml,	
	pUid int
)
  RETURNS ret_spImportPjsDocumentInstance AS
$BODY$
DECLARE
	lRes ret_spImportPjsDocumentInstance;
		
	lSubInstances xml[];
	lFields xml[];
	
	lIterInstances int;
	lIterFields int;
	
	lInstanceId bigint;
	lFieldId bigint;
	
	lCurrentInstance xml;
	lCurrentField xml;
	lTemp xml[];
	
	lPJSActionImportMode int = 3;
BEGIN
	
	--Update all the fields of the current object
	lTemp = xpath('@instance_id', pInstanceXml);
	lInstanceId = lTemp[1]::text::int;		
	
	lFields = xpath('./fields/*[@id > 0]', pInstanceXml);
	FOR lIterFields IN 
		1 .. coalesce(array_upper(lFields, 1), 0) 
	LOOP
		lCurrentField = lFields[lIterFields];	
		lTemp = xpath('@id', lCurrentField);
		lFieldId = lTemp[1]::text::int;	
		--RAISE NOTICE 'Instance % Field % Value %', lInstanceId, lFieldId, lCurrentField;
		PERFORM spSaveInstanceFieldFromXml(lInstanceId, lFieldId, lCurrentField, pUid);
		
	END LOOP;	
	-- After save actions
	PERFORM spPerformInstancesSqlSaveActions(pUid, ARRAY[lInstanceId]::int[], lPJSActionImportMode);
	
	--Update all the Sub instances
	lSubInstances = xpath('./*[@instance_id > 0]', pInstanceXml);	
	FOR lIterInstances IN 
		1 .. coalesce(array_upper(lSubInstances, 1), 0) 
	LOOP
		lCurrentInstance = lSubInstances[lIterInstances];	
		PERFORM spImportPjsDocumentInstance(pDocumentId, lCurrentInstance, pUid);
	END LOOP;
	
	-- After save actions which are to be executed after all the subobjects have been updated. With/without propagation
	PERFORM spPerformInstancesSqlSaveActionsAfterSubobjWithProp(pUid, ARRAY[lInstanceId]::int[], lPJSActionImportMode);
	PERFORM spPerformInstancesSqlSaveActionsAfterSubobjWithoutProp(pUid, ARRAY[lInstanceId]::int[], lPJSActionImportMode);
	
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spImportPjsDocumentInstance(
	pDocumentId int,
	pInstanceXml xml,
	pUid int
) TO iusrpmt;




CREATE TABLE pwt.document_states(
	id serial PRIMARY KEY, 
	name varchar,
	is_readonly boolean DEFAULT false NOT NULL
);
GRANT ALL ON TABLE pwt.document_states TO iusrpmt;

INSERT INTO pwt.document_states(name, is_readonly) VALUES ('New document', false), ('Submitted document', true), ('Returned from PJS', false);

ALTER TABLE pwt.documents ADD CONSTRAINT document_state_fk FOREIGN KEY (state) REFERENCES pwt.document_states(id);


DROP TYPE ret_spGetDocumentDataByInstance CASCADE;
CREATE TYPE ret_spGetDocumentDataByInstance AS (
	document_id bigint,
	display_instance_in_tree int,
	root_instance_id int,
	document_name varchar,
	lock_usr_id bigint,
	is_locked int,
	xsl_dir_name varchar,
	document_is_readonly int
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
		WHERE document_id = lRes.document_id AND char_length(pos) = 2 ORDER BY pos ASC LIMIT 1;
		
		SELECT INTO lRes.document_is_readonly s.is_readonly::int
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
