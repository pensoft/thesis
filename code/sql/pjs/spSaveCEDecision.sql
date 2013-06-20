DROP TYPE IF EXISTS ret_spSaveCEDecision CASCADE;
CREATE TYPE ret_spSaveCEDecision AS (
	result int,
	event_id bigint
);

CREATE OR REPLACE FUNCTION spSaveCEDecision(
	pRoundUserId bigint,
	pDecisionId int,
	pNotes varchar,
	pUid int
)
  RETURNS ret_spSaveCEDecision AS
$BODY$
	DECLARE
		lRes ret_spSaveCEDecision;	
						
		lCERoleId int;		
		lInCopyReviewDocumentState int;
		lAcceptDecisionId int;		
		lDocumentId bigint;
		
		lWaitingAuthorToProceedToLayoutEditingAfterCEDocumentStateId int;
		
		lDocumentVersionAfterCopyEditTypeId int;
		lReviewerVersionId bigint;
		lAuthorRoundType int;
		lRoundId bigint;
		
		lAUsrId int;
		lARoleId int;
		lAVersionType int;
		lARoundType int;
		lDocumentVersion bigint;
		lDocumentReviewRoundUserId bigint;
		lUsrId int;
		lCurrentAuthorVersionId bigint;
		lAuthorSubmittedVersionType int;
		lCEDecisionEventType int;
		lJournalId int;
		cWaitingAuthorToProceedToCopyEditingAfterCEDocumentStateId CONSTANT int := 14;
	BEGIN		
		
		lCERoleId = 9;		
		lInCopyReviewDocumentState = 8;	
		
		lAcceptDecisionId = 9;		
		lWaitingAuthorToProceedToLayoutEditingAfterCEDocumentStateId = 17;
		
		lDocumentVersionAfterCopyEditTypeId = 8;
		lAuthorRoundType = 5;
		lARoleId = 11;
		lAVersionType = 1;
		lARoundType = 5;
		lAuthorSubmittedVersionType = 1;
		lCEDecisionEventType = 19;
		
		
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
			WHERE u.id = pUid AND i.id = pRoundUserId AND du.role_id = lCERoleId
			AND d.state_id = lInCopyReviewDocumentState
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
		--RAISE EXCEPTION 'test';
		UPDATE pjs.documents SET
			state_id = cWaitingAuthorToProceedToCopyEditingAfterCEDocumentStateId
		WHERE id = lDocumentId;
		
		SELECT INTO lCurrentAuthorVersionId id 
			FROM pjs.document_versions
			WHERE document_id = lDocumentId AND version_type_id = lAuthorSubmittedVersionType 
			ORDER BY id DESC LIMIT 1;
		
		SELECT INTO lAUsrId, lUsrId du.id, du.uid 
		FROM pjs.document_users du
		JOIN pjs.documents d ON d.id = du.document_id
		WHERE du.role_id = lARoleId AND du.document_id = lDocumentId AND d.submitting_author_id = du.uid;
		
		SELECT INTO lDocumentVersion id 
		FROM pjs.document_versions
		WHERE document_id = lDocumentId AND version_type_id = lAVersionType 
		ORDER BY id DESC LIMIT 1;
		
		-- Create new document version
		--SELECT INTO lDocumentVersion id FROM spCreateDocumentVersion(lDocumentId, lUsrId, lAVersionType, lCurrentAuthorVersionId);
		SELECT INTO lRoundId id FROM spCreateDocumentRound(lDocumentId, lARoundType);
		UPDATE pjs.document_review_rounds SET create_from_version_id = lDocumentVersion WHERE id = lRoundId;
		
		INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id) 
			VALUES (lRoundId, lAUsrId, lDocumentVersion);
		lDocumentReviewRoundUserId = currval('pjs.document_review_round_reviewers_id_seq');
	
		UPDATE pjs.document_review_rounds SET decision_round_user_id = lDocumentReviewRoundUserId WHERE id = lRoundId;

		UPDATE pjs.documents SET
			current_round_id = lRoundId
		WHERE id = lDocumentId;
		
		SELECT INTO lJournalId d.journal_id FROM pjs.documents d WHERE d.id = lDocumentId;
		SELECT INTO lRes.event_id event_id FROM spCreateEvent(lCEDecisionEventType, lDocumentId, pUid, lJournalId, null, null);
		
		-- manage due dates
		PERFORM pjs.spUpdateDueDates(1, lDocumentId, lCEDecisionEventType, lRoundId, lDocumentReviewRoundUserId);
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveCEDecision(
	pRoundUserId bigint,
	pDecisionId int,
	pNotes varchar,
	pUid int
) TO iusrpmt;
