DROP TYPE IF EXISTS ret_spCancelConfirmReviewerInvitation CASCADE;
CREATE TYPE ret_spCancelConfirmReviewerInvitation AS (
	result int,
	event_id bigint,
	event_id_sec bigint
);

CREATE OR REPLACE FUNCTION spCancelConfirmReviewerInvitation(
	pOper int,
	pInvitationId bigint,
	pUid int,
	pDocumentId bigint
)
  RETURNS ret_spCancelConfirmReviewerInvitation AS
$BODY$
	DECLARE
		lRes ret_spCancelConfirmReviewerInvitation;	
						
		lInReviewState int;
		
		lConfirmedStateId int;
		lCanceledStateId int;
		lNewInvitationStateId int;
		lJournalId int;
		lReviewAcceptedEventType int;
		lReviewDeclinedEventType int;
		lAllReviewDeclinedEventType int;
		
		lEUsrId bigint;
		lSEUsrId bigint;
		lInvId bigint;
		cSERoleId int := 3;
		lReviewerUsrId bigint;
		lEnoughReviewers boolean;
		lCanTakeDecision boolean;
		lCurrentRoundId bigint;
		cNotEnoughReviewersEventType CONSTANT int := 39;
		cCanProceedEventType CONSTANT int := 38;
		lCanProceedFlag boolean;
		lRoleId int;
		cPanelReviewerRoleId CONSTANT int := 7;
	BEGIN		
				
		lInReviewState = 3;
		lConfirmedStateId = 2;
		lCanceledStateId = 3;
		lNewInvitationStateId = 1;
		lReviewAcceptedEventType = 6;
		lReviewDeclinedEventType = 8;
		lAllReviewDeclinedEventType = 9;
		
		-- Check that the passed user is the user invited for the specified  invitation
		-- Check also that the user is trying go cancel/confirm an invitation for the current round of the document which has not been confirmed/canceled before
		SELECT INTO lJournalId, lCurrentRoundId d.journal_id, d.current_round_id FROM pjs.documents d WHERE d.id = pDocumentId;
		
		-- Checking if user performing this action is E/SE/current reviewer
		SELECT INTO lEUsrId uid FROM pjs.journal_users WHERE journal_id = lJournalId AND uid = pUid;
		SELECT INTO lSEUsrId id FROM pjs.document_users WHERE role_id = cSERoleId AND uid = pUid AND document_id = pDocumentId;
		SELECT INTO lInvId, lRoleId i.id, role_id
			FROM pjs.document_user_invitations i			
			JOIN pjs.documents d ON d.id = i.document_id AND i.round_id = d.current_round_id AND d.state_id = lInReviewState
			WHERE i.uid = pUid AND i.id = pInvitationId AND i.state_id = lNewInvitationStateId;
		
		IF (lEUsrId IS NULL AND lSEUsrId IS NULL AND lInvId IS NULL) THEN
			RAISE EXCEPTION 'pjs.thisInvitationIsNotYoursToConfirmOrTheDocumentHasAlreadyPassedTheReviewState';
		END IF;
		
		IF pOper = 1 THEN -- Confirm
			UPDATE pjs.document_user_invitations SET 
				state_id = lConfirmedStateId,
				date_confirmed = now()
			WHERE id = pInvitationId AND state_id = lNewInvitationStateId;
			
			PERFORM spProcessReviewerInvitationConfirmation(pInvitationId);
			
			IF(lInvId IS NOT NULL) THEN
				lReviewerUsrId = pUid;
			ELSE
				SELECT INTO lReviewerUsrId uid FROM pjs.document_user_invitations WHERE id = pInvitationId;
			END IF;
			
			-- There is no accept invitation event for panel reviewers
			IF(lRoleId <> cPanelReviewerRoleId) THEN
				-- Reviewer accept event
				SELECT INTO lRes.event_id event_id FROM spCreateEvent(lReviewAcceptedEventType, pDocumentId, lReviewerUsrId, lJournalId, null, null);
			END IF;
			
		ELSE -- Cancel
			UPDATE pjs.document_user_invitations SET 
				state_id = lCanceledStateId,
				date_canceled = now()
			WHERE id = pInvitationId;-- AND state_id = lNewInvitationStateId;
			
			-- checking if all reviewers decline to take the reveiw (event)
			IF NOT EXISTS(
				SELECT * FROM pjs.document_user_invitations dui
				LEFT JOIN (
					SELECT d.id as document_id FROM pjs.documents d
					JOIN pjs.document_review_rounds drr ON drr.document_id = d.id
					JOIN pjs.document_review_round_users drru ON drru.round_id = drr.id AND drru.state_id = 1
					WHERE drru.decision_id IS NOT NULL
				) a ON a.document_id = dui.document_id
				WHERE dui.state_id IN (1,2,5) AND role_id = 5 AND dui.document_id = pDocumentId
			) THEN
				SELECT INTO lRes.event_id_sec event_id FROM spCreateEvent(lAllReviewDeclinedEventType, pDocumentId, pUid, lJournalId, null, null);
			END IF;
			
			-- Reviewer decline event
			SELECT INTO lRes.event_id event_id FROM spCreateEvent(lReviewDeclinedEventType, pDocumentId, pUid, lJournalId, null, null);
			
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

GRANT EXECUTE ON FUNCTION spCancelConfirmReviewerInvitation(
	pOper int,
	pInvitationId bigint,
	pUid int,
	pDocumentId bigint
) TO iusrpmt;
