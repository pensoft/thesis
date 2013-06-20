DROP TYPE IF EXISTS ret_spSaveZookeysDocumentFourthStep CASCADE;
CREATE TYPE ret_spSaveZookeysDocumentFourthStep AS (
	result int,
	event_id bigint
);

CREATE OR REPLACE FUNCTION spsavezookeysdocumentfourthstep(pdocumentid bigint, previewtype integer, puid bigint)
  RETURNS ret_spsavezookeysdocumentfourthstep AS
$BODY$
	DECLARE
		lRes ret_spSaveZookeysDocumentFourthStep;			
		lSuccessfullySubmittedDocState int;
		
		cSubmissionEventType CONSTANT int := 1;
		lJournalId int;
		
		cPublicReviewerVersionType CONSTANT int := 10;
		cDocumentPublicReviewType CONSTANT int := 4;
		
		lCurrentAuthorVersionId bigint;
		cAuthorVersionTypeId CONSTANT int := 1;
		
		lAuthorUserId int;
		lPublicReviewerVersionId bigint;
		lVersionToDelete bigint;
	BEGIN
		
		IF pReviewType = 1 THEN
			lSuccessfullySubmittedDocState = 2;
			
			-- creating submission event
			SELECT INTO lJournalId journal_id FROM pjs.documents WHERE id = pdocumentid;
			SELECT INTO lRes.event_id event_id FROM spCreateEvent(cSubmissionEventType, pdocumentid, puid, lJournalId, null, null);
		ELSE 
			lSuccessfullySubmittedDocState = 1;
		END IF;
		
		IF(pReviewType = cDocumentPublicReviewType) THEN
			IF NOT EXISTS (SELECT id FROM pjs.document_versions WHERE version_type_id = cPublicReviewerVersionType AND document_id = pdocumentid) THEN
				-- Get author version
				SELECT INTO 
					lCurrentAuthorVersionId, 
					lAuthorUserId 
					
					id, 
					uid
				FROM pjs.document_versions
				WHERE document_id = pdocumentid AND version_type_id = cAuthorVersionTypeId 
				ORDER BY id DESC 
				LIMIT 1;
				
				SELECT INTO lPublicReviewerVersionId id FROM spCreateDocumentVersion(pdocumentid, lAuthorUserId, cPublicReviewerVersionType, lCurrentAuthorVersionId);
			END IF;
		ELSE
			SELECT INTO lVersionToDelete id FROM pjs.document_versions WHERE version_type_id = cPublicReviewerVersionType AND document_id = pdocumentid;
			
			IF(lVersionToDelete IS NOT NULL) THEN
				DELETE FROM pjs.pwt_document_versions WHERE version_id = lVersionToDelete;
				DELETE FROM pjs.document_versions WHERE id = lVersionToDelete;
			END IF;
			
		END IF;
		
		UPDATE pjs.documents d SET 
			state_id = lSuccessfullySubmittedDocState,
			creation_step = 5,
			document_review_type_id = pReviewType
		WHERE d.id = pDocumentId AND d.submitting_author_id = pUid AND d.state_id = 1;
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spsavezookeysdocumentfourthstep(bigint, integer, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spsavezookeysdocumentfourthstep(bigint, integer, bigint) TO public;
GRANT EXECUTE ON FUNCTION spsavezookeysdocumentfourthstep(bigint, integer, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION spsavezookeysdocumentfourthstep(bigint, integer, bigint) TO iusrpmt;
