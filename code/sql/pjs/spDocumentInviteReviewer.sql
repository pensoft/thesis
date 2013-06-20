/*DROP TYPE ret_spDocumentInviteReviewer CASCADE;
CREATE TYPE ret_spDocumentInviteReviewer AS (
	result int,
	event_id bigint
);*/
DROP FUNCTION IF EXISTS pjs.spDocumentInviteReviewer(bigint,int[],int,int,int);
CREATE OR REPLACE FUNCTION pjs.spDocumentInviteReviewer(	
	pDocumentId bigint,
	pReviewerIds int[],
	pUid int,
	pRoleId int,
	pRoundId int
)
  RETURNS SETOF ret_spDocumentInviteReviewer AS
$BODY$
	DECLARE
		cNotEnoughReviewersEventType CONSTANT int := 39;
		cCanProceedEventType CONSTANT int := 38;
	
		lJournalEditorRoleId int := 2;
		lSERoleId int := 3;
		lWaitingSEAssignmentState int := 2;
		lInReviewState int := 3;
		lReviewRoundStateId int := 1;		
		lRoundId bigint;
		lVersionId bigint;
		lSubmittedVersionId bigint;
		lAuthorSubmittedVersionType int := 1;
		lSEVersionTypeId int := 3;
		lRecord record;
		lDocumentUserId bigint;
		lArrCnt int;
		lConfirmInvitationStateId int := 1;
		cReviewerRoleId int := 5;
		lDocUsrId bigint;
		lJournalId int;
		lReviewerInvitationEventType int := 3;
		lRes ret_spDocumentInviteReviewer;
		lEnoughReviewers boolean;
		lCanTakeDecision boolean;
		lCurrentRoundId bigint;
		lCanProceedFlag boolean;
		lSEUsrId bigint;
		cSERoleId int := 3;
		cPanelReviewerRoleId CONSTANT int := 7;
		lReviewerRoleId int;
		lSECommunityPublicEvent int;
		lPanelDueDate timestamp;
		lPublicDueDate timestamp;
		lDocumentReviewType int;
		lCanInvitePanelReviewers boolean;
	BEGIN		
		-- Check that the current user is SE for the specified document
		IF NOT EXISTS (
			SELECT u.id
			FROM usr u
			JOIN  pjs.journal_users ju ON ju.uid = u.id
			JOIN pjs.documents d ON d.journal_id = ju.journal_id
			WHERE u.id = pUid AND d.id = pDocumentId AND ju.role_id = lSERoleId
		) THEN
			RAISE EXCEPTION 'pjs.onlySECanExecuteThisAction';
		END IF;
		
		-- Check that the document is in review state
		
		IF NOT EXISTS (
			SELECT d.id			
			FROM pjs.documents d 
			WHERE d.id = pDocumentId
		) THEN
			RAISE EXCEPTION 'pjs.reviewersCanBeAddedOnlyToDocumentsInReview';
		END IF;
		
		SELECT INTO lJournalId, lCurrentRoundId d.journal_id, d.current_round_id FROM pjs.documents d WHERE d.id = pDocumentId;
		
		IF(pRoundId IS NULL) THEN
			pRoundId := lCurrentRoundId;
		END IF;
		
		lArrCnt = 1;
		WHILE(lArrCnt <= array_upper(pReviewerIds, 1)) LOOP
			
			IF EXISTS (
				SELECT drr.id
				FROM pjs.document_review_rounds drr
				JOIN pjs.document_review_round_users drru ON drru.round_id = drr.id
				WHERE drr.document_id = pDocumentId AND drr.round_number = 1 AND drr.round_type_id = 1 AND drru.id = pReviewerIds[lArrCnt]
			) THEN
				lReviewerInvitationEventType = 35;
			ELSE
				lReviewerInvitationEventType = 3;
			END IF;
			
			-- Check that the specified reviewer has not been invited for the current round before
			IF EXISTS (
				SELECT d.id			
				FROM pjs.documents d 
				JOIN pjs.document_user_invitations du ON du.document_id = d.id AND du.round_id = d.current_round_id
				WHERE d.id = pDocumentId AND du.uid = pReviewerIds[lArrCnt] AND du.role_id = pRoleId
			) THEN
				--RAISE EXCEPTION 'aaa';
				--RAISE NOTICE 'pReviewerIds=%', pReviewerIds[lArrCnt];
				--RAISE EXCEPTION 'pjs.thisReviewerHasAlreadyBeenInvited';
				
				-- check for can invite nominated/panel reviewers
				SELECT INTO lCanInvitePanelReviewers result FROM pjs."spCheckCanInviteReviewer"(pDocumentId, pRoundId, pRoleId);
				IF(lCanInvitePanelReviewers = FALSE) THEN
					RAISE EXCEPTION 'pjs.cannotInviteMoreReviewers';
				END IF;
				
				UPDATE pjs.document_user_invitations SET state_id = lConfirmInvitationStateId WHERE uid = pReviewerIds[lArrCnt] AND round_id = pRoundId;
				
				SELECT INTO lDocUsrId id FROM pjs.document_users WHERE uid = pReviewerIds[lArrCnt] AND document_id = pDocumentId AND role_id IN(cReviewerRoleId, cPanelReviewerRoleId) ORDER BY id DESC LIMIT 1;
				
				UPDATE pjs.document_review_round_users SET state_id = 1 WHERE document_user_id = lDocUsrId AND round_id = pRoundId;
				
				-- if we must set different event for reinvite
				IF(pRoleId = cPanelReviewerRoleId) THEN
					lReviewerInvitationEventType = 36;
					lReviewerRoleId = cPanelReviewerRoleId;
				ELSE
					lReviewerRoleId = cReviewerRoleId;
				END IF;
				
				SELECT INTO lRes.event_id event_id FROM spCreateEvent(lReviewerInvitationEventType, pDocumentId, pUid, lJournalId, pReviewerIds[lArrCnt], lReviewerRoleId);
				-- manage due dates
				--RAISE EXCEPTION 'Type: %, RoundId: %, ReviewerId: %', lReviewerInvitationEventType, pRoundId, pReviewerIds[lArrCnt];
				PERFORM pjs.spUpdateDueDates(3, pDocumentId, lReviewerInvitationEventType, pRoundId, pReviewerIds[lArrCnt]);
				
				SELECT INTO lDocumentReviewType, lPanelDueDate, lPublicDueDate document_review_type_id, panel_duedate, public_duedate FROM pjs.documents WHERE id = pDocumentId;
				--RAISE EXCEPTION 'lPanelDueDate: %, lDocumentReviewType: %', lPanelDueDate, lDocumentReviewType;
				IF(lDocumentReviewType = 4 AND lPublicDueDate IS NULL) THEN
					lSECommunityPublicEvent = 40;
					PERFORM pjs.spUpdateDueDates(5, pDocumentId, lSECommunityPublicEvent, NULL, NULL);
				ELSEIF(lDocumentReviewType = 3 AND lPanelDueDate IS NULL) THEN
					lSECommunityPublicEvent = 41;
					PERFORM pjs.spUpdateDueDates(4, pDocumentId, lSECommunityPublicEvent, NULL, NULL);
				END IF;
				
			ELSE 
				--RAISE EXCEPTION 'bbb';
				--RAISE NOTICE 'ELSE pReviewerIds=%', pReviewerIds[lArrCnt];
				--Invite the reviewer
				-- INSERT INTO pjs.document_user_invitations(uid, document_id, round_id, due_date, role_id) 
				-- SELECT pReviewerIds[lArrCnt], pDocumentId, d.current_round_id, now() + '1 week'::interval, pRoleId
				-- FROM pjs.documents d
				-- WHERE d.id = pDocumentId;
				
				-- check for can invite nominated/panel reviewers
				SELECT INTO lCanInvitePanelReviewers result FROM pjs."spCheckCanInviteReviewer"(pDocumentId, pRoundId, pRoleId);
				IF(lCanInvitePanelReviewers = FALSE) THEN
					RAISE EXCEPTION 'pjs.cannotInviteMoreReviewers';
				END IF;
				
				UPDATE pjs.document_user_invitations 
				   SET
						date_invited= now(),
						state_id = 1, -- new invitation state
						role_id  = pRoleId
				WHERE uid = pReviewerIds[lArrCnt] AND round_id = pRoundId;
				
				-- event for new invitation
				IF(pRoleId = cPanelReviewerRoleId) THEN
					lReviewerInvitationEventType = 36;
					lReviewerRoleId = cPanelReviewerRoleId;
				ELSE
					lReviewerRoleId = cReviewerRoleId;
				END IF;
				SELECT INTO lRes.event_id event_id FROM spCreateEvent(lReviewerInvitationEventType, pDocumentId, pUid, lJournalId, pReviewerIds[lArrCnt], lReviewerRoleId);
				-- manage due dates
				--RAISE EXCEPTION 'Type: %, RoundId: %, ReviewerId: %', lReviewerInvitationEventType, pRoundId, pReviewerIds[lArrCnt];
				PERFORM pjs.spUpdateDueDates(3, pDocumentId, lReviewerInvitationEventType, pRoundId, pReviewerIds[lArrCnt]);
				
				SELECT INTO lDocumentReviewType, lPanelDueDate, lPublicDueDate document_review_type_id, panel_duedate, public_duedate FROM pjs.documents WHERE id = pDocumentId;
				--RAISE EXCEPTION 'lPanelDueDate: %, lDocumentReviewType: %', lPanelDueDate, lDocumentReviewType;
				IF(lDocumentReviewType = 4 AND lPublicDueDate IS NULL) THEN
					lSECommunityPublicEvent = 40;
					PERFORM pjs.spUpdateDueDates(5, pDocumentId, lSECommunityPublicEvent, NULL, NULL);
				ELSEIF(lDocumentReviewType = 3 AND lPanelDueDate IS NULL) THEN
					lSECommunityPublicEvent = 41;
					PERFORM pjs.spUpdateDueDates(4, pDocumentId, lSECommunityPublicEvent, NULL, NULL);
				END IF;
				
			END IF;
			
			lArrCnt = lArrCnt + 1;
			
			lRes.result = 1;
			RETURN NEXT lRes;
		END LOOP;
		
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
		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spDocumentInviteReviewer(
	pDocumentId bigint,
	pReviewerIds int[],
	pUid int,
	pRoleId int,
	pRoundId int
) TO iusrpmt;
