DROP TYPE IF EXISTS pjs."ret_spManageUserInvitationsAndReviews" CASCADE;
CREATE TYPE pjs."ret_spManageUserInvitationsAndReviews" AS (
	result int,
	event_id bigint,
	event_id_sec bigint
);

CREATE OR REPLACE FUNCTION pjs."spManageUserInvitationsAndReviews"(
	pDocumentId bigint,
	pRoundId int,
	pUsersInvitaionsToCancel varchar,
	pUsersReviewsToSubmit varchar
)
  RETURNS SETOF pjs."ret_spManageUserInvitationsAndReviews" AS
$BODY$
	DECLARE
		lRes pjs."ret_spManageUserInvitationsAndReviews" ;
		lReviewerRoleId int;
		lInvitedReviewerStateId int;
		lReviewerCanceledStateId int;
		lAutoSubmittedReviewDecisionId int;
		lUserInvIdsArr int[];
		lUserRevIdsArr int[];
		lSERoleId int;
		lSEUserId int;
		lJournalId int;
		lUserCancelInvitationEventId int;
		lUserSubmitReviewEventId int;
		lUserId int;
	BEGIN
		lReviewerRoleId = 5;
		lInvitedReviewerStateId = 1;
		lReviewerCanceledStateId = 3;
		lAutoSubmittedReviewDecisionId = 10;
		lSERoleId = 3;
		lUserCancelInvitationEventId = 97;
		lUserSubmitReviewEventId = 98;
		
		-- Canceling user invitations
		IF (pUsersInvitaionsToCancel <> '') THEN
			lUserInvIdsArr = string_to_array(pUsersInvitaionsToCancel, ',');
			SELECT INTO lJournalId journal_id FROM pjs.documents WHERE id = pDocumentId;
			
			UPDATE pjs.document_user_invitations SET state_id = lReviewerCanceledStateId 
			WHERE uid = ANY (lUserInvIdsArr) AND document_id = pDocumentId AND round_id = pRoundId;
			
			SELECT INTO lSEUserId uid FROM pjs.document_users WHERE document_id = pDocumentId AND role_id = lSERoleId;
			
			FOR i IN 1 .. array_upper(lUserInvIdsArr, 1) LOOP
				--RAISE NOTICE 'lUserInvIdsArr: %', lUserInvIdsArr[i];
				
				SELECT INTO lRes.event_id event_id FROM spCreateEvent(lUserCancelInvitationEventId, pDocumentId, lSEUserId, lJournalId, lUserInvIdsArr[i], lReviewerRoleId);
				
				RETURN NEXT lRes;
				
			END LOOP; 
			
		END IF;
		
		-- Submitting reviews
		IF (pUsersReviewsToSubmit <> '') THEN
			lUserRevIdsArr = string_to_array(pUsersReviewsToSubmit, ',');
			SELECT INTO lJournalId journal_id FROM pjs.documents WHERE id = pDocumentId;
			
			UPDATE pjs.document_review_round_users SET decision_id = lAutoSubmittedReviewDecisionId
			WHERE document_user_id = ANY (lUserRevIdsArr) AND round_id = pRoundId;
			
			SELECT INTO lSEUserId uid FROM pjs.document_users WHERE document_id = pDocumentId AND role_id = lSERoleId;
			
			--RAISE NOTICE 'lSEUserId: %', lSEUserId;
			FOR i IN 1 .. array_upper(lUserRevIdsArr, 1) LOOP
				SELECT INTO lUserId uid FROM pjs.document_users WHERE document_id = pDocumentId AND id = lUserRevIdsArr[i];
				--RAISE NOTICE 'lUserRevIdsArr: %', lUserRevIdsArr[i];
				--RAISE NOTICE 'lUserId: %', lUserId;
				
				SELECT INTO lRes.event_id event_id FROM spCreateEvent(lUserSubmitReviewEventId, pDocumentId, lSEUserId, lJournalId, lUserId, lReviewerRoleId);
				
				RETURN NEXT lRes;
				
			END LOOP; 
		END IF;
		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION  pjs."spManageUserInvitationsAndReviews"(
	pDocumentId bigint,
	pRoundId int,
	pUsersInvitaionsToCancel varchar,
	pUsersReviewsToSubmit varchar
) TO iusrpmt;

