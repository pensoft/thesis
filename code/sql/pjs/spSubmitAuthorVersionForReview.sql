DROP TYPE IF EXISTS ret_spSubmitAuthorVersionForReview CASCADE;
CREATE TYPE ret_spSubmitAuthorVersionForReview AS (
	result int,
	event_id bigint
);

CREATE OR REPLACE FUNCTION spSubmitAuthorVersionForReview(
	pDocumentId bigint,	
	pUid int
)
  RETURNS ret_spSubmitAuthorVersionForReview AS
$BODY$
	DECLARE
		lRes ret_spSubmitAuthorVersionForReview;	
						
		lInReviewState int;
		lRoundId bigint;
		lAuthorSubmittedVersionTypeId int;
		lAuthorVersionTypeId int;
		lSEVersionTypeId int;
		
		lCurrentAuthorVersionId bigint;
		lNewAuthorVersionId bigint;
		lWaitingAuthorVersionAfterReviewRoundDocumentStateId int;
		lReviewRoundType int;
		lRoundNum int;
		lVersionId bigint;
		lRecord record;	
		lSERoleId int;
		lOldRoundId bigint;
		lAuthorRoleId int;
		lApproveDecisionId int;
		lSEReviewUsrId bigint;
		lAuthorSubmitVersionForReviewEventType int;
		lJournalId int;
		lOldRoundNumber int;
		lEnoughReviewers boolean;
		lCanTakeDecision boolean;
		lCurrentRoundId bigint;
		cSERoleId int := 3;
		lSEUsrId bigint;
		cCanProceedEventType CONSTANT int := 38;
		lOldReviewRoundId int;
	BEGIN		
		
		lSERoleId = 3;		
		lInReviewState = 3;
		lAuthorSubmittedVersionTypeId = 1;
		lAuthorVersionTypeId = 1;
		lSEVersionTypeId = 3;
		
		lWaitingAuthorVersionAfterReviewRoundDocumentStateId = 9;
				
		lReviewRoundType = 1;
		lAuthorRoleId = 11;
		lApproveDecisionId = 1;
		
		-- Check that the passed user is the document submitting author 
		-- Check also that the document is in the appropriate state
		-- 
		IF NOT EXISTS (
			SELECT d.id
			FROM pjs.documents d 			
			WHERE d.submitting_author_id = pUid
			AND d.state_id = lWaitingAuthorVersionAfterReviewRoundDocumentStateId AND d.id = pDocumentId
		) THEN
			RAISE EXCEPTION 'pjs.theDocumentIsNotInTheAppropriateStateOrYouAreNotItsAuthor';
		END IF;
		
		-- For now take the current version
		SELECT INTO lCurrentAuthorVersionId id 
		FROM pjs.document_versions
		WHERE document_id = pDocumentId AND version_type_id = lAuthorVersionTypeId 
		ORDER BY id DESC LIMIT 1;
		
		SELECT INTO lOldRoundId, lOldRoundNumber drr.id, drr.round_number
		FROM pjs.document_review_rounds drr
		JOIN pjs.documents d ON d.current_round_id = drr.id
		WHERE d.id = pDocumentId;
		
		UPDATE pjs.document_review_rounds SET decision_id = lApproveDecisionId WHERE id = lOldRoundId;
		
		-- Update decision
		UPDATE pjs.document_review_round_users SET 
			decision_id = lApproveDecisionId,
			decision_date = now()
		WHERE decision_id IS NULL AND round_id = lOldRoundId;
		
		--SELECT INTO lNewAuthorVersionId id FROM spCreateDocumentVersion(pDocumentId, pUid, lAuthorSubmittedVersionTypeId, lCurrentAuthorVersionId);
		
		lNewAuthorVersionId = lCurrentAuthorVersionId;
		
		-- Get old review Round Id
		SELECT INTO lOldReviewRoundId id
		FROM pjs.document_review_rounds 
		WHERE document_id = pDocumentId AND round_type_id = lReviewRoundType
		ORDER BY round_number DESC
		LIMIT 1;
		
		
		-- Create new round
		SELECT INTO lRoundNum max(round_number) 
		FROM pjs.document_review_rounds 
		WHERE document_id = pDocumentId AND round_type_id = lReviewRoundType;
		
		lRoundNum = coalesce(lRoundNum, 0) + 1;
		INSERT INTO pjs.document_review_rounds(document_id, round_type_id, round_number, create_from_version_id) VALUES (pDocumentId, lReviewRoundType, lRoundNum, lNewAuthorVersionId);
		lRoundId = currval('pjs.document_review_rounds_id_seq');
		
		-- Change document state
		UPDATE pjs.documents SET
			state_id = lInReviewState, 
			current_round_id = lRoundId
		WHERE id = pDocumentId;
		
		-- Insert Old review Round Users Into New Round with as suggested
		PERFORM pjs."spCopyInvitedUsersFromOldReviewRound"(lOldReviewRoundId,lRoundId, pDocumentId);
		
		-- Create versions for the SE-s for the new round
		FOR lRecord IN
			SELECT * 
			FROM pjs.document_users
			WHERE document_id = pDocumentId AND role_id = lSERoleId
		LOOP
			SELECT INTO lVersionId id FROM spCreateDocumentVersion(pDocumentId, lRecord.uid, lSEVersionTypeId, lNewAuthorVersionId);
			INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id) VALUES (lRoundId, lRecord.id, lVersionId);
			lSEReviewUsrId = currval('pjs.document_review_round_reviewers_id_seq');
			
			UPDATE pjs.document_review_rounds SET decision_round_user_id = lSEReviewUsrId WHERE id = lRoundId;
		END LOOP;
	
		IF(lOldRoundNumber = 1) THEN
			lAuthorSubmitVersionForReviewEventType = 29;
		ELSE
			lAuthorSubmitVersionForReviewEventType = 30;
		END IF;
	
		SELECT INTO lJournalId, lCurrentRoundId d.journal_id, d.current_round_id FROM pjs.documents d WHERE d.id = pDocumentId;
		SELECT INTO lRes.event_id event_id FROM spCreateEvent(lAuthorSubmitVersionForReviewEventType, pDocumentId, pUid, lJournalId, null, null);
	
		-- check SE can take decision and enough reviewers assigned
		SELECT INTO lCanTakeDecision result FROM pjs.spCheckIfSECanTakeADecision(pDocumentId);
		--RAISE EXCEPTION 'SE Decision: %', lCanTakeDecision;
		UPDATE pjs.document_review_rounds SET can_proceed = lCanTakeDecision WHERE id = lCurrentRoundId;
		SELECT INTO lEnoughReviewers result FROM pjs.spCheckEnoughReviewrs(pDocumentId);
		--RAISE EXCEPTION 'Enough Reviewers: %', lEnoughReviewers;
		UPDATE pjs.document_review_rounds SET enough_reviewers = lEnoughReviewers WHERE id = lCurrentRoundId;
		
		-- manage due dates
		PERFORM pjs.spUpdateDueDates(1, pDocumentId, lAuthorSubmitVersionForReviewEventType, lCurrentRoundId, null);
		IF (lCanTakeDecision = TRUE) THEN
			SELECT INTO lSEUsrId dru.id 
			FROM pjs.document_users du
			JOIN pjs.document_review_round_users dru ON dru.document_user_id = du.id AND round_id = lCurrentRoundId
			WHERE du.role_id = cSERoleId AND du.document_id = pDocumentId;
			
			PERFORM pjs.spUpdateDueDates(1, pDocumentId, cCanProceedEventType, null, lSEUsrId);
		END IF;
	
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSubmitAuthorVersionForReview(
	pDocumentId bigint,	
	pUid int
) TO iusrpmt;
