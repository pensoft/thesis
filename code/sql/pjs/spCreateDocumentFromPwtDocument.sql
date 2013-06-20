DROP TYPE IF EXISTS ret_spCreateDocumentFromPwtDocument CASCADE;
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
	pEditor_Round_Type int,
	pCommentsXml xml
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
		lInReviewDocumentState int = 3;		
		lPwtDocumentType int = 1;
		lAuthorSubmittedVersionType int = 1;	
		lSERoleId int = 3;
		lSEVersionTypeId int = 3;
				
		lRecord record;
		lSEVersionId bigint;
		
		lCurrentAuthorVersionId bigint;
		lPublicReviewerVersionId bigint;
		
		lDocumentType int;
		
		-- waiting author states
		cWaitingAuthorVersionAfterReviewRoundDocumentState CONSTANT int = 9;		
		cWaitingAuthorVersionAfterLayoutRound CONSTANT int := 10;
		cWaitingAuthorToProceedToLayout CONSTANT int := 12;
		cWaitingAuthorToProceedToCopyEditing CONSTANT int := 14;
		cWaitingAuthorToProceedToLayoutAfterCopyEditing CONSTANT int := 17;
		
		cAuthorVersionTypeId CONSTANT int := 1;
		
	BEGIN								
		
		SELECT INTO lOtherDocumentId, lOtherDocumentState, lOtherDocumentUid, lDocumentType
			d.id, d.state_id, d.submitting_author_id, d.document_review_type_id
		FROM pjs.pwt_documents pd
		JOIN  pjs.documents d ON d.id = pd.document_id
		WHERE pd.pwt_id = pPwtDocumentId;
		
		lOtherDocumentState = coalesce(lOtherDocumentState, 0);
		IF lOtherDocumentId IS NOT NULL THEN
			lRes.document_id = lOtherDocumentId;
			IF coalesce(lOtherDocumentUid, 0) <> pUid THEN
				RAISE EXCEPTION 'pjs.aDocumentWithTheSpecifiedPwtIdHasAlreadyBeenCreatedByAnotherUser';
			END IF;
			IF (
				lOtherDocumentState <> lNewDocumentState AND 
				lOtherDocumentState <> cWaitingAuthorVersionAfterReviewRoundDocumentState AND 
				lOtherDocumentState <> cWaitingAuthorToProceedToLayout AND
				lOtherDocumentState <> cWaitingAuthorToProceedToCopyEditing AND
				lOtherDocumentState <> cWaitingAuthorToProceedToLayoutAfterCopyEditing
			) THEN				
				RAISE EXCEPTION 'pjs.aDocumentWithTheSpecifiedPwtIdAlreadyExistsAndItCannotBeUpdated';
			ELSEIF lOtherDocumentState = lNewDocumentState THEN
				lRes.state_id = lOtherDocumentState;
				RETURN lRes;
			END IF;
			
		END IF;
		
		IF lOtherDocumentId IS NULL THEN
			-- Create new document
			INSERT INTO pjs.documents(submitting_author_id, document_source_id, journal_id, document_type_id, document_review_type_id)
			VALUES (pUid, lPwtDocumentType, pJournalId, 1, 2);
			lRes.document_id = currval('pjs.documents_id_seq');
			
			INSERT INTO pjs.pwt_documents(document_id, pwt_id, createuid, journal_id) VALUES (lRes.document_id, pPwtDocumentId, pUid, pJournalId);
			
			INSERT INTO pjs.document_versions(uid, version_num, version_type_id, document_id) VALUES (pUid, 1, lAuthorSubmittedVersionType, lRes.document_id);
			lVersionId = currval('pjs.document_versions_id_seq');
			
			INSERT INTO pjs.pwt_document_versions(version_id, "xml") VALUES (lVersionId, pDocumentXml);
			
			PERFORM pjs.spImportVersionComments(lVersionId, pCommentsXml);
			
			-- fetch xml data and update document info
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
				
				PERFORM pjs.spCopyVersionComments(lVersionId, lSEVersionId);
				
				INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id)
					VALUES (lRoundId, lRecord.id, lSEVersionId);
			END LOOP;
			-- updating document current round id
			UPDATE pjs.documents SET
				current_round_id = lRoundId
			WHERE id = lRes.document_id;
			
			-- SET due dates for current round and users
			PERFORM pjs.spUpdateDueDates(1, lRes.document_id, lSubmissionEventType, lRoundId, NULL);
			
		ELSE
			-- resubmitting new author version for the document
			
			-- Get last author version
			SELECT INTO lCurrentAuthorVersionId id 
			FROM pjs.document_versions
			WHERE document_id = lRes.document_id AND version_type_id = cAuthorVersionTypeId 
			ORDER BY id DESC 
			LIMIT 1;
			
			-- fetch xml data and update document info
			PERFORM spFetchPwtDocumentMetadata(lRes.document_id, pDocumentXml);
			
			UPDATE pjs.pwt_document_versions SET 
				"xml" = pDocumentXml
			WHERE version_id = lCurrentAuthorVersionId;
			
			PERFORM pjs.spImportVersionComments(lCurrentAuthorVersionId, pCommentsXml);
			
			-- perform different actions by document state
			IF (lOtherDocumentState = cWaitingAuthorVersionAfterReviewRoundDocumentState) THEN
				SELECT INTO lRes.event_id event_id FROM spSubmitAuthorVersionForReview(lRes.document_id, pUid);
			ELSEIF (lOtherDocumentState = cWaitingAuthorToProceedToCopyEditing) THEN
				SELECT INTO lRes.event_id event_id FROM spProceedDocumentToCopyEditing(lRes.document_id, pUid);
			ELSEIF(lOtherDocumentState = cWaitingAuthorToProceedToLayout OR lOtherDocumentState = cWaitingAuthorToProceedToLayoutAfterCopyEditing) THEN
				SELECT INTO lRes.event_id event_id FROM spProceedDocumentToLayoutEditing(lRes.document_id, pUid);
			END IF;
			
		END IF;
		
		SELECT INTO lRes.state_id state_id 
		FROM pjs.documents 
		WHERE id = lRes.document_id;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateDocumentFromPwtDocument(
	pPwtDocumentId bigint,
	pJournalId int,
	pDocumentXml xml,
	pUid int,
	pEditor_Round_Type int,
	pCommentsXml xml
) TO iusrpmt;
