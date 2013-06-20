DROP TYPE IF EXISTS ret_spSaveEditorRejectedDecision CASCADE;
CREATE TYPE ret_spSaveEditorRejectedDecision AS (
	result int,
	event_id bigint
);

CREATE OR REPLACE FUNCTION spSaveEditorRejectedDecision(
	pDecisionId int,
	pNotes varchar,
	pUid int, 
	pDocumentStateId int,
	pDocumentId int
)
  RETURNS ret_spSaveEditorRejectedDecision AS
$BODY$
	DECLARE
		lRes ret_spSaveEditorRejectedDecision;	
		
		lDocumentRoundId int;
		lERoleId int;		
		lInEditorState int;
		lRoundId int;
		lAuthorRoundType int;
		lEditorRoundType int;
		lDocumentVersionType int;
		lDocumentVersion int;
		lDocumentReviewRoundUserId int;
		lDocumentUserId int;
		lInReviewState int;
		lReviewRoundType int;
		lVersionId bigint;
		cAuthorVersionType CONSTANT int := 1;
		
		cRejectDecisionId CONSTANT int := 2;
		cRejectButAlabalaDecisionId CONSTANT int := 5;
		lSEDecisionEventType int;
		lJournalId int;
	BEGIN		
		
		lERoleId = 2;		
		lEditorRoundType = 4;
		lReviewRoundType = 1;
		lDocumentVersionType = 9;
		lInEditorState = 2;	
		lAuthorRoundType = 5;
		lInReviewState = 3;
		
		IF EXISTS(SELECT id FROM pjs.documents WHERE id = pDocumentId AND state_id IN (lInEditorState, lInReviewState)) THEN

			IF NOT EXISTS (SELECT id FROM pjs.document_users WHERE uid = pUid AND role_id = lERoleId AND document_id = pDocumentId) THEN
				INSERT INTO pjs.document_users(document_id, uid, role_id) VALUES (pDocumentId, pUid, lERoleId);
				lDocumentUserId = currval('pjs.document_users_id_seq');
			ELSE
				SELECT INTO lDocumentUserId id FROM pjs.document_users WHERE uid = pUid AND role_id = lERoleId AND document_id = pDocumentId;
			END IF;
			
			SELECT INTO lDocumentVersion id FROM spCreateDocumentVersion(pDocumentId, pUid, lDocumentVersionType, null);
			
			SELECT INTO lDocumentRoundId drr.id 
			FROM pjs.documents d
			JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id 
			WHERE d.id = pDocumentId AND drr.round_type_id IN (lEditorRoundType, lReviewRoundType)
				AND drr.decision_id IS NULL 
			LIMIT 1;
		
			INSERT INTO pjs.document_review_round_users(round_id, decision_id, document_user_id, document_version_id, decision_notes, decision_date) 
				VALUES (lDocumentRoundId, pDecisionId, lDocumentUserId, lDocumentVersion, pNotes, CURRENT_TIMESTAMP);
			lDocumentReviewRoundUserId = currval('pjs.document_review_round_reviewers_id_seq');

			UPDATE pjs.document_review_rounds SET decision_id = pDecisionId, decision_round_user_id = lDocumentReviewRoundUserId, decision_notes = pNotes WHERE id = lDocumentRoundId;
			UPDATE pjs.documents SET state_id = pDocumentStateId WHERE id = pDocumentId;
			
			SELECT INTO lVersionId id FROM pjs.document_versions WHERE document_id = pDocumentId AND version_type_id = cAuthorVersionType ORDER BY id DESC LIMIT 1;
			
			SELECT INTO lRoundId id FROM spCreateDocumentRound(pDocumentId, lAuthorRoundType);	
			UPDATE pjs.document_review_rounds SET create_from_version_id = lVersionId WHERE id = lRoundId;
			
			UPDATE pjs.documents SET
				current_round_id = lRoundId
			WHERE id = pDocumentId;
			
		IF pDecisionId = cRejectDecisionId THEN
			lSEDecisionEventType = 25;
		ELSEIF pDecisionId = cRejectButAlabalaDecisionId THEN
			lSEDecisionEventType = 26;
		END IF;
		
		IF(lSEDecisionEventType IS NOT NULL) THEN
			SELECT INTO lJournalId d.journal_id FROM pjs.documents d WHERE d.id = pDocumentId;
			SELECT INTO lRes.event_id event_id FROM spCreateEvent(lSEDecisionEventType, pDocumentId, pUid, lJournalId, null, null);
		END IF;
			
		ELSE
			RAISE EXCEPTION 'pjs.documentNotInEditorState';
		END IF;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveEditorRejectedDecision(
	pDecisionId int,
	pNotes varchar,
	pUid int, 
	pDocumentStateId int,
	pDocumentId int
) TO iusrpmt;
