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
		lTempXmlValText text;
		lAuthorFirstNameXPathVal xml[];
		lAuthorMiddleNameXPathVal xml[];
		lAuthorLastNameXPathVal xml[];
		lAffiliationXPathVal xml[];
		lCityXPathVal xml[];
		lCountryXPathVal xml[];
		cEditorialPaperType CONSTANT int := 8;
		
		lInactiveDocumentUserStateId int = 2;
		lActiveDocumentUserStateId int = 1;
		lOrd int;
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
			SELECT INTO lTempXml regexp_replace(lTempXml::text, E'<(?!b|/b|i|/i|u|/u|sup|/sup|sub|/sub|p|/p)[^>]*?>', '', 'g');
			UPDATE pjs.documents SET
				abstract = lTempXml::text
			WHERE id = pDocumentId;
		END IF;
		
		-- Fetch keywords
		lTempNodes := xpath('/document/objects//*[@object_id="15"]/fields/*[@id="19"]/value', pDocumentXml);
		IF array_upper(lTempNodes, 1) > 0 THEN
			lTempXml := spXmlConcatArr(xpath('/value/*|text()', lTempNodes[1]));
			SELECT INTO lTempXmlValText regexp_replace(lTempXml::text, E'</p>', ',', 'g');
			SELECT INTO lTempXmlValText regexp_replace(lTempXmlValText, E'<(?!b|/b|i|/i|u|/u|sup|/sup|sub|/sub)[^>]*?>', '', 'g');
			UPDATE pjs.documents SET
				keywords = lTempXmlValText
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
		
		lTempNodes := xpath('/document/objects//*[@object_id="9" or @object_id="153"]/*[@object_id="8"]', pDocumentXml);
		lOrd = 1;
		
		FOR lIter IN 
			1 .. coalesce(array_upper(lTempNodes, 1), 0) 
		LOOP
			lAuthorXPathVal := xpath('/*[@object_id="8"]/fields/*[@id="13"]/value/text()', lTempNodes[lIter]);
			lCorrespondingAuthorXPathVal = xpath('/*[@object_id="8"]/fields/*[@id="15"]/value/@value_id', lTempNodes[lIter]);
			
			lAuthorFirstNameXPathVal := xpath('/*[@object_id="8"]/fields/*[@id="6"]/value/text()', lTempNodes[lIter]);
			lAuthorMiddleNameXPathVal := xpath('/*[@object_id="8"]/fields/*[@id="7"]/value/text()', lTempNodes[lIter]);
			lAuthorLastNameXPathVal := xpath('/*[@object_id="8"]/fields/*[@id="8"]/value/text()', lTempNodes[lIter]);
			
			lAffiliationXPathVal := xpath('/*[@object_id="8"]/*[@object_id="5"]/fields/*[@id="9"]/value/text()', lTempNodes[lIter]);
			lCityXPathVal := xpath('/*[@object_id="8"]/*[@object_id="5"]/fields/*[@id="10"]/value/text()', lTempNodes[lIter]);
			lCountryXPathVal := xpath('/*[@object_id="8"]/*[@object_id="5"]/fields/*[@id="11"]/value/text()', lTempNodes[lIter]);
			
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
						state_id = lActiveDocumentUserStateId,
						first_name = lAuthorFirstNameXPathVal[1]::text,
						middle_name = lAuthorMiddleNameXPathVal[1]::text,
						last_name = lAuthorLastNameXPathVal[1]::text,
						affiliation = lAffiliationXPathVal[1]::text,
						city = lCityXPathVal[1]::text,
						country = lCountryXPathVal[1]::text,
						ord = lOrd
					WHERE document_id = pDocumentId AND role_id = lAuthorRoleId AND uid = lAuthorId;
				ELSE
					INSERT INTO pjs.document_users(document_id, role_id, uid, co_author, first_name, middle_name, last_name, affiliation, city, country, ord) 
						VALUES(pDocumentId, lAuthorRoleId, lAuthorId, lCorrespondingAuthorFlag, lAuthorFirstNameXPathVal[1]::text, lAuthorMiddleNameXPathVal[1]::text, lAuthorLastNameXPathVal[1]::text, lAffiliationXPathVal[1]::text, lCityXPathVal[1]::text, lCountryXPathVal[1]::text, lOrd);
				END IF;
				lOrd = lOrd + 1;
			END IF;
			
		END LOOP;
		
		/*Document categories metadata*/
		-- Fetch all Taxon classifications
		IF(lPWTPaperTypeId = cEditorialPaperType) THEN
			lTempNodes := xpath('/document/objects//*[@object_id="162"]/fields/*[@id="244"]/value/@value_id', pDocumentXml);
		ELSE
			lTempNodes := xpath('/document/objects//*[@object_id="82"]/fields/*[@id="244"]/value/@value_id', pDocumentXml);
		END IF;
		FOR lIter IN 1 .. coalesce(array_upper(lTempNodes, 1), 0)  LOOP
			lTaxonClassId = spConvertAnyToInt(lTempNodes[lIter]);
			lTaxonClassifications = lTaxonClassifications || lTaxonClassId;
		END LOOP;
		UPDATE pjs.documents SET taxon_categories = lTaxonClassifications WHERE id = pDocumentId;
		
		-- Fetch all Chronological classifications
		IF(lPWTPaperTypeId = cEditorialPaperType) THEN
			lTempNodes := xpath('/document/objects//*[@object_id="162"]/fields/*[@id="246"]/value/@value_id', pDocumentXml);
		ELSE
			lTempNodes := xpath('/document/objects//*[@object_id="82"]/fields/*[@id="246"]/value/@value_id', pDocumentXml);
		END IF;
		FOR lIter IN 1 .. coalesce(array_upper(lTempNodes, 1), 0)  LOOP
			lChronologicalClassId = spConvertAnyToInt(lTempNodes[lIter]);
			lChronologicalClassifications = lChronologicalClassifications || lChronologicalClassId;
		END LOOP;
		UPDATE pjs.documents SET chronological_categories = lChronologicalClassifications WHERE id = pDocumentId;
		
		-- Fetch all Subject classifications
		IF(lPWTPaperTypeId = cEditorialPaperType) THEN
			lTempNodes := xpath('/document/objects//*[@object_id="162"]/fields/*[@id="245"]/value/@value_id', pDocumentXml);
		ELSE
			lTempNodes := xpath('/document/objects//*[@object_id="82"]/fields/*[@id="245"]/value/@value_id', pDocumentXml);
		END IF;
		FOR lIter IN 1 .. coalesce(array_upper(lTempNodes, 1), 0)  LOOP
			lSubjectClassId = spConvertAnyToInt(lTempNodes[lIter]);
			lSubjectClassifications = lSubjectClassifications || lSubjectClassId;
		END LOOP;
		UPDATE pjs.documents SET subject_categories = lSubjectClassifications WHERE id = pDocumentId;
	
		-- Fetch all Geographical classifications
		IF(lPWTPaperTypeId = cEditorialPaperType) THEN
			lTempNodes := xpath('/document/objects//*[@object_id="162"]/fields/*[@id="247"]/value/@value_id', pDocumentXml);
		ELSE
			lTempNodes := xpath('/document/objects//*[@object_id="82"]/fields/*[@id="247"]/value/@value_id', pDocumentXml);
		END IF;
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
