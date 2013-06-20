CREATE OR REPLACE FUNCTION pjs."spEmailCancelReviewerInvitation"(
	pTaskDetailId bigint,
	pUID bigint,
	pDocumentId bigint
)
  RETURNS int AS
$BODY$
	DECLARE
		lRes ret_spSECancelConfirmReviewerInvitation;	
						
		lSERoleId int;		
		lInReviewState int;
		
		lConfirmedBySEStateId int;
		lCanceledBySEStateId int;
		lNewInvitationStateId int;
		lRemovedReviewerStateId int;
		lConfirmedInvitationStateId int;
		lReviewerCanceledEventType int;
		lJournalId int;
		lReviewerRoleId int := 5;
		lReviewerId bigint;
		cERoleId int := 2;
		lEnoughReviewers boolean;
		lCanTakeDecision boolean;
		lCurrentRoundId bigint;
		cNotEnoughReviewersEventType CONSTANT int := 39;
		cCanProceedEventType CONSTANT int := 38;
		lCanProceedFlag boolean;
		cSERoleId int := 3;
		lSEUsrId bigint;
		lDocumentReviewRoundUserId bigint;
	BEGIN		
		
		lSERoleId = 3;		
		lInReviewState = 3;
		lConfirmedBySEStateId = 5;
		lCanceledBySEStateId = 6;
		lNewInvitationStateId = 1;
		lRemovedReviewerStateId = 2;
		lConfirmedInvitationStateId = 2;
		--lReviewerCanceledEventType = 7;
		
		
		SELECT INTO lJournalId, lCurrentRoundId d.journal_id, d.current_round_id FROM pjs.documents d WHERE d.id = pDocumentId;
		
		SELECT INTO lDocumentReviewRoundUserId drru.id 
		FROM pjs.document_review_round_users drru
		JOIN pjs.document_users du ON du.id = drru.document_user_id AND du.uid = pUID
		WHERE drru.round_id = lCurrentRoundId;
		
		-- Check if reviewer can be canceled (if he has decision_id then he cannot be canceled)
		IF EXISTS (
			SELECT drru.id
			FROM pjs.document_review_round_users drru
			JOIN pjs.document_users du ON du.id = drru.document_user_id AND du.uid = pUID
			JOIN pjs.document_review_rounds drr ON drr.id = drru.round_id
			JOIN pjs.documents d ON d.current_round_id = drr.id
			WHERE d.id = pDocumentId 
				AND drru.decision_id IS NOT NULL
		) THEN
			RAISE EXCEPTION 'pjs.reviewer_cannot_be_canceled';
		END IF;
				
		UPDATE pjs.document_user_invitations SET 
			state_id = lCanceledBySEStateId,
			date_canceled = now()
		WHERE round_id = lCurrentRoundId AND document_id = pDocumentId AND uid = pUID;
		
		UPDATE pjs.document_review_round_users SET 
			state_id = lRemovedReviewerStateId 
		WHERE id = lDocumentReviewRoundUserId;
		
		DELETE FROM pjs.email_task_details WHERE id = pTaskDetailId;
		
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
		
		IF(lEnoughReviewers = FALSE) THEN
			PERFORM pjs.spUpdateDueDates(2, pDocumentId, cNotEnoughReviewersEventType, lCurrentRoundId, NULL);
		END IF;

		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spEmailCancelReviewerInvitation"(
	pTaskDetailId bigint,
	pUID bigint,
	pDocumentId bigint
) TO iusrpmt;
