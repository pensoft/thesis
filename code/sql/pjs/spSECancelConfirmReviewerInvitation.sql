DROP TYPE ret_spSECancelConfirmReviewerInvitation CASCADE;
CREATE TYPE ret_spSECancelConfirmReviewerInvitation AS (
	result int,
	event_id bigint
);

CREATE OR REPLACE FUNCTION spSECancelConfirmReviewerInvitation(
	pOper int,
	pInvitationId bigint,
	pSEId int,
	pDocumentReviewerId bigint,
	pDocumentId bigint
)
  RETURNS ret_spSECancelConfirmReviewerInvitation AS
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
	BEGIN		
		
		lSERoleId = 3;		
		lInReviewState = 3;
		lConfirmedBySEStateId = 5;
		lCanceledBySEStateId = 6;
		lNewInvitationStateId = 1;
		lRemovedReviewerStateId = 2;
		lConfirmedInvitationStateId = 2;
		--lReviewerCanceledEventType = 7;
		
		-- Check that the passed user is SE for the document of the invitation
		-- Check also that the user is trying go cancel/confirm an invitation for the current round of the document which has not been confirmed/canceled before
		IF NOT EXISTS (
			SELECT u.id
			FROM usr u			
			JOIN pjs.document_users du ON du.uid = u.id
			JOIN pjs.documents d ON d.id = du.document_id
			JOIN pjs.document_user_invitations i ON i.document_id = d.id AND i.round_id = d.current_round_id AND d.state_id = lInReviewState
			WHERE u.id = pSEId AND i.id = pInvitationId AND du.role_id IN (lSERoleId, cERoleId) AND i.state_id IN (lNewInvitationStateId, lConfirmedInvitationStateId)
		) THEN
			RAISE EXCEPTION 'pjs.onlyE_or_SECanExecuteThisAction';
		END IF;
				
		SELECT INTO lJournalId, lCurrentRoundId d.journal_id, d.current_round_id FROM pjs.documents d WHERE d.id = pDocumentId;
		
		IF pOper = 1 THEN -- Confirm
			/*UPDATE pjs.document_user_invitations SET 
				state_id = lConfirmedBySEStateId,
				date_confirmed = now()
			WHERE id = pInvitationId AND state_id = lNewInvitationStateId;
			PERFORM spProcessReviewerInvitationConfirmation(pInvitationId);
			*/
			
		ELSE -- Cancel
			
			IF EXISTS(SELECT * FROM pjs.document_review_round_users WHERE id = pDocumentReviewerId AND state_id <> lRemovedReviewerStateId) THEN
				lReviewerCanceledEventType = 33;
			ELSE
				lReviewerCanceledEventType = 32;
			END IF;
			
			UPDATE pjs.document_user_invitations SET 
				state_id = lCanceledBySEStateId,
				date_canceled = now()
			WHERE id = pInvitationId; --AND state_id = lNewInvitationStateId;
			
			UPDATE pjs.document_review_round_users SET 
				state_id = lRemovedReviewerStateId 
			WHERE id = pDocumentReviewerId;
			
			SELECT INTO lReviewerId uid FROM pjs.document_user_invitations WHERE id = pInvitationId;
			SELECT INTO lRes.event_id event_id FROM spCreateEvent(lReviewerCanceledEventType, pDocumentId, pSEId, lJournalId, lReviewerId, lReviewerRoleId);
			
		END IF;
		
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
	
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSECancelConfirmReviewerInvitation(
	pOper int,
	pInvitationId bigint,
	pSEId int,
	pDocumentReviewerId bigint,
	pDocumentId bigint
) TO iusrpmt;
