DROP TYPE IF EXISTS ret_spSaveSEDecision CASCADE;
CREATE TYPE ret_spSaveSEDecision AS (
	result int,
	event_id bigint
);

CREATE OR REPLACE FUNCTION spSaveSEDecision(
	pRoundUserId bigint,
	pDecisionId int,
	pNotes varchar,
	pUid int,
	pDocumentId bigint
)
  RETURNS ret_spSaveSEDecision AS
$BODY$
	DECLARE
		lRes ret_spSaveSEDecision;	
						
		lSERoleId int;		
		lInReviewState int;
		lAcceptDecisionId int;
		lAcceptWithMinorCorrectionsDecisionId int;
		lAcceptWithMajorCorrectionsDecisionId int;
		lRejectDecisionId int;
		lDocumentId bigint;
		
		lNewDocumentStateId int;
		lWaitingAuthorToProceedToLayoutDocumentStateId int;
		lWaitingAuthorVersionAfterReviewRoundDocumentStateId int;
		lReadyForCopyEditingDocumentStateId int;
		lRejectedDocumentStateId int;
		
		lDocumentVersionAfterReviewTypeId int;
		lReviewerVersionId bigint;
		
		lSECanTakeDecision int;
		lAuthorRoundType int;
		lRoundId int;
		lDocumentRoundId bigint;
		lDocumentReviewRoundUserId int;
		lReviewRoundType int;
		lAuthorUsrType int;
		lAuthorId bigint;
		
		lAuthorDocumentVersion bigint;
		lAuthorRoundId bigint;
		lAuthorSubmittedVersionType int;
		lJournalId int;
		lSEDecisionEventType int;
		lRejectNicelyDecisionId int;
	BEGIN		
		
		lReviewRoundType = 1;
		
		lSERoleId = 3;		
		lInReviewState = 3;	
		lAcceptDecisionId = 1;
		lAcceptWithMinorCorrectionsDecisionId = 3;
		lAcceptWithMajorCorrectionsDecisionId = 4;		
		lRejectDecisionId = 2;		
		lRejectNicelyDecisionId = 5;
		lAuthorRoundType = 5;
		lAuthorUsrType = 11;
		lAuthorSubmittedVersionType = 1;
		
		lWaitingAuthorVersionAfterReviewRoundDocumentStateId = 9;
		lWaitingAuthorToProceedToLayoutDocumentStateId = 12;
		lReadyForCopyEditingDocumentStateId = 14;
		lRejectedDocumentStateId = 7;
		
		lDocumentVersionAfterReviewTypeId = 4;
		
		SELECT INTO lDocumentRoundId drr.id 
		FROM pjs.documents d
		JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id 
		WHERE d.id = pDocumentId AND drr.round_type_id = lReviewRoundType 
			AND drr.decision_id IS NULL 
		LIMIT 1;
		
		SELECT INTO lDocumentReviewRoundUserId drru.id
		FROM pjs.document_users du
		JOIN pjs.document_review_round_users drru ON drru.document_user_id = du.id
		WHERE du.uid = pUid AND du.role_id = lSERoleId;
		
		lDocumentId = pDocumentId;
		
		SELECT INTO lReviewerVersionId ru.document_version_id
		FROM  pjs.document_review_round_users ru
		JOIN pjs.document_users du ON du.id = ru.document_user_id
		WHERE ru.id = lDocumentReviewRoundUserId;

		-- Update decision
		UPDATE pjs.document_review_round_users SET 
			decision_id = pDecisionId,
			decision_date = now(),
			decision_notes = pNotes
		WHERE decision_id IS NULL AND round_id = lDocumentRoundId;
		
		UPDATE pjs.document_review_rounds SET 
			decision_id = pDecisionId
		WHERE id = lDocumentRoundId;
		
		-- Change document state
		IF pDecisionId = lAcceptDecisionId THEN
		
			UPDATE pjs.documents 
				SET 
					is_approved = TRUE, 
					approve_date = now() 
			WHERE id = lDocumentId;
			
			IF EXISTS (
				SELECT * 
				FROM pjs.documents
				WHERE id = lDocumentId AND has_lingiustics_editing = true
			) THEN
				lNewDocumentStateId = lReadyForCopyEditingDocumentStateId;	
				lSEDecisionEventType = 28;
			ELSE
				lNewDocumentStateId = lWaitingAuthorToProceedToLayoutDocumentStateId;			
				lSEDecisionEventType = 27;
			END IF;
		ELSEIF pDecisionId = lAcceptWithMinorCorrectionsDecisionId OR pDecisionId = lAcceptWithMajorCorrectionsDecisionId THEN
			lNewDocumentStateId = lWaitingAuthorVersionAfterReviewRoundDocumentStateId;
			IF (pDecisionId = lAcceptWithMinorCorrectionsDecisionId) THEN
				lSEDecisionEventType = 23;
			ELSE
				lSEDecisionEventType = 24;
			END IF;
		ELSEIF pDecisionId = lRejectDecisionId THEN
			lNewDocumentStateId = lRejectedDocumentStateId;
			lSEDecisionEventType = 25;
		ELSEIF pDecisionId = lRejectNicelyDecisionId THEN
			lSEDecisionEventType = 26;
		END IF;
		
		IF (pDecisionId <> lRejectDecisionId) THEN
			SELECT INTO lAuthorDocumentVersion dv.id 
			FROM pjs.document_versions dv
			WHERE dv.document_id = pDocumentId AND dv.version_type_id = lAuthorSubmittedVersionType 
			ORDER BY dv.id 
			DESC LIMIT 1;
			
			/*SELECT INTO lAuthorId id FROM pjs.document_users WHERE document_id = pDocumentId AND role_id = lAuthorUsrType;*/
			SELECT INTO lAuthorId du.id
			FROM pjs.document_users du
			JOIN pjs.documents d ON d.id = du.document_id
			WHERE du.role_id = lAuthorUsrType AND du.document_id = pDocumentId AND d.submitting_author_id = du.uid;
			
			SELECT INTO lRoundId id FROM spCreateDocumentRound(pDocumentId, lAuthorRoundType);	
			UPDATE pjs.document_review_rounds SET create_from_version_id = lAuthorDocumentVersion WHERE id = lRoundId;
			
			INSERT INTO pjs.document_review_round_users(round_id, document_user_id, document_version_id) VALUES (lRoundId, lAuthorId, lAuthorDocumentVersion);
			lAuthorRoundId = currval('pjs.document_review_round_reviewers_id_seq');
			
			UPDATE pjs.document_review_rounds SET
				decision_round_user_id = lAuthorRoundId
			WHERE id = lRoundId;
			
			UPDATE pjs.documents SET
				current_round_id = lRoundId
			WHERE id = lDocumentId;
		END IF;
		
		UPDATE pjs.documents SET
			state_id = lNewDocumentStateId
		WHERE id = lDocumentId;
	
		-- SE decision event
		SELECT INTO lJournalId d.journal_id FROM pjs.documents d WHERE d.id = pDocumentId;
		SELECT INTO lRes.event_id event_id FROM spCreateEvent(lSEDecisionEventType, pDocumentId, pUid, lJournalId, null, null);
		PERFORM pjs.spUpdateDueDates(1, pDocumentId, lSEDecisionEventType, lRoundId, lAuthorRoundId);
	
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveSEDecision(
	pRoundUserId bigint,
	pDecisionId int,
	pNotes varchar,
	pUid int,
	pDocumentId bigint
) TO iusrpmt;
