DROP TYPE IF EXISTS ret_spSaveReviewerDecision CASCADE;
CREATE TYPE ret_spSaveReviewerDecision AS (
	result int,
	event_id bigint,
	event_id_sec bigint
);

CREATE OR REPLACE FUNCTION spSaveReviewerDecision(
	pRoundUserId bigint,
	pDecisionId int,
	pNotes varchar,
	pUid int,
	pDocumentId bigint
)
  RETURNS ret_spSaveReviewerDecision AS
$BODY$
	DECLARE
		lRes ret_spSaveReviewerDecision;	
						
		lDedicatedReviewerRoleId int;		
		lCpanelReviwerRoleId int;
		lInReviewState int;
		lJournalId int;
		lReviewerSubmittedReviewEventType int;
		lAllReviewsSubmittedEventType int;
		lUsrWithoutDecision bigint;
		lNotConfirmedInvitation bigint;
		lEnoughReviewers boolean;
		lCanTakeDecision boolean;
		lCurrentRoundId bigint;
		lCanProceedFlag boolean;
		lSEUsrId bigint;
		cCanProceedEventType CONSTANT int := 38;
		cSERoleId CONSTANT int := 3;
	BEGIN		
		
		lDedicatedReviewerRoleId = 5;		
		lCpanelReviwerRoleId = 7;		
		lInReviewState = 3;		
		lReviewerSubmittedReviewEventType = 12;
		lAllReviewsSubmittedEventType = 10;
		
		-- Check that the passed user is the user making the decision
		-- Check also that the user is trying to make a decision for the current round of the document which is in review mode
		IF NOT EXISTS (
			SELECT u.id
			FROM usr u			
			JOIN pjs.document_users du ON du.uid = u.id
			JOIN pjs.documents d ON d.id = du.document_id
			JOIN pjs.document_review_rounds r ON r.document_id = d.id AND r.id = d.current_round_id 
			JOIN pjs.document_review_round_users i ON i.round_id = r.id AND i.document_user_id = du.id
			WHERE u.id = pUid AND i.id = pRoundUserId AND du.role_id IN (lDedicatedReviewerRoleId, lCpanelReviwerRoleId)
			AND d.state_id = lInReviewState
		) THEN
			RAISE EXCEPTION 'pjs.youCantPerformThisAction';
		END IF;
				
		
		UPDATE pjs.document_review_round_users SET 
			decision_id = pDecisionId,
			decision_notes = pNotes,
			decision_date = now()
		WHERE id = pRoundUserId;
		
		SELECT INTO lJournalId, lCurrentRoundId d.journal_id, d.current_round_id FROM pjs.documents d WHERE d.id = pDocumentId;
		
		-- check if all reviews are submitted (event)
		
		SELECT INTO lUsrWithoutDecision drru.id
			FROM pjs.documents d
			JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id
			JOIN pjs.document_review_round_users drru ON drru.round_id = drr.id AND drru.state_id = 1
			WHERE d.id = pDocumentId AND drru.decision_id IS NULL;
		
		SELECT INTO lNotConfirmedInvitation id FROM pjs.document_user_invitations WHERE document_id = pDocumentId AND state_id = 1;
		
		IF (lUsrWithoutDecision IS NULL AND lNotConfirmedInvitation IS NULL) THEN
			SELECT INTO lRes.event_id_sec event_id FROM spCreateEvent(lAllReviewsSubmittedEventType, pDocumentId, pUid, lJournalId, null, null);
		END IF;

		-- submitted review event
		SELECT INTO lRes.event_id event_id FROM spCreateEvent(lReviewerSubmittedReviewEventType, pDocumentId, pUid, lJournalId, null, null);
		SELECT INTO lCanProceedFlag can_proceed FROM pjs.document_review_rounds WHERE id = lCurrentRoundId;
	
		-- check SE can take decision and enough reviewers assigned
		SELECT INTO lCanTakeDecision result FROM pjs.spCheckIfSECanTakeADecision(pDocumentId);
		--RAISE EXCEPTION 'SE Decision: %', lCanTakeDecision;
		UPDATE pjs.document_review_rounds SET can_proceed = lCanTakeDecision WHERE id = lCurrentRoundId;
		SELECT INTO lEnoughReviewers result FROM pjs.spCheckEnoughReviewrs(pDocumentId);
		--RAISE EXCEPTION 'Enough Reviewers: %', lEnoughReviewers;
		UPDATE pjs.document_review_rounds SET enough_reviewers = lEnoughReviewers WHERE id = lCurrentRoundId;
	
		IF(lCanTakeDecision = TRUE AND lCanProceedFlag = FALSE) THEN
			SELECT INTO lSEUsrId dru.id 
			FROM pjs.document_users du
			JOIN pjs.document_review_round_users dru ON dru.document_user_id = du.id AND round_id = lCurrentRoundId
			WHERE du.role_id = cSERoleId AND du.document_id = pDocumentId;
			
			PERFORM pjs.spUpdateDueDates(1, pDocumentId, cCanProceedEventType, NULL, lSEUsrId);
		END IF;
	
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveReviewerDecision(
	pRoundUserId bigint,
	pDecisionId int,
	pNotes varchar,
	pUid int,
	pDocumentId bigint
) TO iusrpmt;
