DROP TYPE IF EXISTS ret_spSaveLEDecision CASCADE;
CREATE TYPE ret_spSaveLEDecision AS (
	result int,
	event_id bigint
);

CREATE OR REPLACE FUNCTION spSaveLEDecision(
	pRoundUserId bigint,
	pDecisionId int,
	pNotes varchar,
	pUid int
)
  RETURNS ret_spSaveLEDecision AS
$BODY$
	DECLARE
		lRes ret_spSaveLEDecision;	
						
		lLERoleId int;		
		lInLayoutReviewDocumentState int;
		lAcceptDecisionId int;
		lReturnToAuthorDecisionId int;
		lDocumentId bigint;
		
		lNewDocumentStateId int;
		lApprovedForPublishDocumentStateId int;
		lWaitingForAuthorVersionAfterLayoutDocumentStateId int;
		
		lDocumentVersionAfterLayoutTypeId int;
		lReviewerVersionId bigint;
		lAuthorSubmittedVersionType int;
		lCurrentAuthorVersionId bigint;
		lEUsrId int;
		lERoleId int;
		lEVersionType int;
		lERoundType int;
		lRoundId bigint;
		lDocumentVersion bigint;
		lDocumentReviewRoundUserId bigint;
		lUsrId int;
		lApproveForPublishingEventType int;
		lJournalId int;
	BEGIN		
		
		lLERoleId = 8;		
		lInLayoutReviewDocumentState = 4;	
		
		lAcceptDecisionId = 6;
		lReturnToAuthorDecisionId = 7;		
		
		lApprovedForPublishDocumentStateId = 11;
		lWaitingForAuthorVersionAfterLayoutDocumentStateId = 10;
		
		lDocumentVersionAfterLayoutTypeId = 6;
		lAuthorSubmittedVersionType = 1;
		lERoleId = 2;
		lEVersionType = 3;
		lERoundType = 4;
		lApproveForPublishingEventType = 18;
		
		-- Check that the passed user is the user making the decision
		-- Check also that the user is trying to make a decision for the current round of the document which is in review mode
		-- 
		IF NOT EXISTS (
			SELECT u.id
			FROM usr u			
			JOIN pjs.document_users du ON du.uid = u.id
			JOIN pjs.documents d ON d.id = du.document_id
			JOIN pjs.document_review_rounds r ON r.document_id = d.id AND r.id = d.current_round_id 
			JOIN pjs.document_review_round_users i ON i.round_id = r.id AND i.document_user_id = du.id
			WHERE u.id = pUid AND i.id = pRoundUserId AND du.role_id = lLERoleId
			AND d.state_id = lInLayoutReviewDocumentState
		) THEN
			RAISE EXCEPTION 'pjs.youCantPerformThisAction';
		END IF;
		
		SELECT INTO lDocumentId, lReviewerVersionId du.document_id, ru.document_version_id
		FROM  pjs.document_review_round_users ru
		JOIN pjs.document_users du ON du.id = ru.document_user_id
		WHERE ru.id = pRoundUserId;
		
		-- Update decision
		UPDATE pjs.document_review_round_users SET 
			decision_id = pDecisionId,
			decision_notes = pNotes,
			decision_date = now()
		WHERE id = pRoundUserId;
		
		-- Update round decision
		UPDATE pjs.document_review_rounds r SET
			decision_id = pDecisionId
		FROM pjs.document_review_round_users du 
		WHERE du.id = pRoundUserId AND r.id = du.round_id;
		
		-- Change document state
		IF pDecisionId = lAcceptDecisionId THEN
			lNewDocumentStateId = lApprovedForPublishDocumentStateId;			
		ELSEIF pDecisionId = lReturnToAuthorDecisionId THEN			
			lNewDocumentStateId = lWaitingForAuthorVersionAfterLayoutDocumentStateId;
		END IF;
		
		UPDATE pjs.documents SET
			state_id = lNewDocumentStateId
		WHERE id = lDocumentId;
		
		SELECT INTO lCurrentAuthorVersionId id 
			FROM pjs.document_versions
			WHERE document_id = lDocumentId AND version_type_id = lAuthorSubmittedVersionType 
			ORDER BY id DESC LIMIT 1;
		
		SELECT INTO lEUsrId, lUsrId id, uid FROM pjs.document_users WHERE role_id = lERoleId AND document_id = lDocumentId;
		
		-- Create new document version
		SELECT INTO lDocumentVersion id FROM spCreateDocumentVersion(lDocumentId, lUsrId, lEVersionType, lCurrentAuthorVersionId);
		SELECT INTO lRoundId id FROM spCreateDocumentRound(lDocumentId, lERoundType);
		UPDATE pjs.document_review_rounds SET create_from_version_id = lCurrentAuthorVersionId WHERE id = lRoundId;
		UPDATE pjs.documents SET
			current_round_id = lRoundId
		WHERE id = lDocumentId;
		
		INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id) 
			VALUES (lRoundId, lEUsrId, lDocumentVersion);
		lDocumentReviewRoundUserId = currval('pjs.document_review_round_reviewers_id_seq');
	
		UPDATE pjs.document_review_rounds SET decision_round_user_id = lDocumentReviewRoundUserId WHERE id = lRoundId;
	
		SELECT INTO lJournalId d.journal_id FROM pjs.documents d WHERE d.id = lDocumentId;
		SELECT INTO lRes.event_id event_id FROM spCreateEvent(lApproveForPublishingEventType, lDocumentId, pUid, lJournalId, null, null);
		
		-- manage due dates
		PERFORM pjs.spUpdateDueDates(1, lDocumentId, lApproveForPublishingEventType, lRoundId, lDocumentReviewRoundUserId);
	
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveLEDecision(
	pRoundUserId bigint,
	pDecisionId int,
	pNotes varchar,
	pUid int
) TO iusrpmt;
