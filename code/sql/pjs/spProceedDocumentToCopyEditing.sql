DROP TYPE IF EXISTS ret_spProceedDocumentToCopyEditing CASCADE;
CREATE TYPE ret_spProceedDocumentToCopyEditing AS (
	result int,
	event_id bigint
);

CREATE OR REPLACE FUNCTION spProceedDocumentToCopyEditing(
	pDocumentId bigint,	
	pUid int
)
  RETURNS ret_spProceedDocumentToCopyEditing AS
$BODY$
	DECLARE
		lRes ret_spProceedDocumentToCopyEditing;	
		lAuthorSubmittedVersionTypeId int;
		lCurrentAuthorVersionId bigint;
		lNewAuthorVersionId bigint;
		lAuthorVersionType int;
		lDocumentState int;
		lWaitingAuthorToProceedToCopyEditingDocumentStateId int;
		lReadyForCopyEditingDocumentState int;
		lAuthorDecisionId int;
		lAuthorRoundType int;
		lDocumentRoundId bigint;
		lRoundId bigint;
		lERoundType int;
		lERoleId int;
		lEDocumentUserId bigint;
		lERoundUsrId bigint;
		lDocumentUserId bigint;
		lAuthorRoleId int;
		lAuthorSubmitReadyForCopyEditingEventType int;
		lJournalId int;
	BEGIN		
				
		lReadyForCopyEditingDocumentState = 15;
		lWaitingAuthorToProceedToCopyEditingDocumentStateId = 14;
		lAuthorSubmittedVersionTypeId = 1;
		lAuthorVersionType = 1;
		lAuthorDecisionId := 1;
		lAuthorRoundType = 5;
		lERoundType = 4;
		lERoleId = 2;
		lAuthorRoleId = 11;
		lAuthorSubmitReadyForCopyEditingEventType = 15;
		
		-- Check that the passed user is the document submitting author 
		-- Check also that the document is in the appropriate state
		-- 
		SELECT INTO lDocumentState d.state_id FROM pjs.documents d WHERE d.submitting_author_id = pUid AND d.id = pDocumentId;
		
		IF( lDocumentState <> lWaitingAuthorToProceedToCopyEditingDocumentStateId) THEN
			RAISE EXCEPTION 'pjs.theDocumentIsNotInTheAppropriateStateOrYouAreNotItsAuthor';
		END IF;
		
		-- Create new document version
		-- For now take the current version
		SELECT INTO lCurrentAuthorVersionId id 
		FROM pjs.document_versions
		WHERE document_id = pDocumentId AND version_type_id = lAuthorVersionType 
		ORDER BY id DESC LIMIT 1;
		
		-- >>> closing author round
		SELECT INTO lDocumentUserId id FROM pjs.document_users WHERE role_id = lAuthorRoleId AND document_id = pDocumentId;
		
		SELECT INTO lDocumentRoundId drr.id 
		FROM pjs.documents d
		JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id 
		WHERE d.id = pDocumentId AND drr.round_type_id = lAuthorRoundType AND drr.decision_id IS NULL 
		LIMIT 1;
		
		UPDATE pjs.document_review_round_users SET decision_id = lAuthorDecisionId WHERE round_id = lDocumentRoundId AND decision_id IS NULL;
		UPDATE pjs.document_review_rounds SET decision_id = lAuthorDecisionId WHERE id = lDocumentRoundId;
		-- <<< closing author round
		
		
		--SELECT INTO lNewAuthorVersionId id FROM spCreateDocumentVersion(pDocumentId, pUid, lAuthorSubmittedVersionTypeId, lCurrentAuthorVersionId);
		lNewAuthorVersionId = lCurrentAuthorVersionId;
		
		-- >>> open editor round
		SELECT INTO lRoundId id FROM spCreateDocumentRound(pDocumentId, lERoundType);
		UPDATE pjs.document_review_rounds SET create_from_version_id = lNewAuthorVersionId WHERE id = lRoundId;
		
		SELECT INTO lEDocumentUserId id FROM pjs.document_users WHERE document_id = pDocumentId AND role_id = lERoleId;
		
		INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id) VALUES (lRoundId, lEDocumentUserId, lNewAuthorVersionId);
		lERoundUsrId = currval('pjs.document_review_round_reviewers_id_seq');
		UPDATE pjs.document_review_rounds SET decision_round_user_id = lERoundUsrId, round_due_date = now() + INTERVAL '1 week' WHERE id = lRoundId;
		-- <<< open editor round
		
		-- Change document state
		UPDATE pjs.documents SET
			state_id = lReadyForCopyEditingDocumentState,
			current_round_id = lRoundId
		WHERE id = pDocumentId;
	
		SELECT INTO lJournalId d.journal_id FROM pjs.documents d WHERE d.id = pDocumentId;
		SELECT INTO lRes.event_id event_id FROM spCreateEvent(lAuthorSubmitReadyForCopyEditingEventType, pDocumentId, pUid, lJournalId, null, null);
		
		-- manage due dates
		PERFORM pjs.spUpdateDueDates(1, pDocumentId, lAuthorSubmitReadyForCopyEditingEventType, lRoundId, lERoundUsrId);
	
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spProceedDocumentToCopyEditing(
	pDocumentId bigint,	
	pUid int
) TO iusrpmt;
