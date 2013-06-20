DROP TYPE IF EXISTS ret_spSaveSERejectedDecision CASCADE;
CREATE TYPE ret_spSaveSERejectedDecision AS (
	result int,
	event_id bigint
);

CREATE OR REPLACE FUNCTION spSaveSERejectedDecision(
	pDecisionId int,
	pNotes varchar,
	pUid int, 
	pDocumentStateId int,
	pDocumentId int
)
  RETURNS ret_spSaveSERejectedDecision AS
$BODY$
	DECLARE
		lRes ret_spSaveSERejectedDecision;	
		
		lDocumentRoundId int;
		lSERoleId int;		
		lInReviewState int;
		lRoundId int;
		lAuthorRoundType int;
		lReviewRoundType int;
		lDocumentReviewRoundUserId int;
		lDocumentUserId int;
		lJournalId int;
		lSubmittedEventType int;
		lAllReviewsSubmittedEventType int;
		lRejectDecisionId int;
		lRejectNicelyDecisionId int;
		lVersionId bigint;
		cAuthorVersionType CONSTANT int := 1;
	BEGIN		
		
		lSERoleId = 3;
		lReviewRoundType = 1;
		lInReviewState = 3;	
		lAuthorRoundType = 5;
		lAllReviewsSubmittedEventType = 10;
		lRejectDecisionId = 2;		
		lRejectNicelyDecisionId = 5;
		
		IF EXISTS(SELECT id FROM pjs.documents WHERE id = pDocumentId AND state_id = lInReviewState) THEN
			
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

			UPDATE pjs.document_review_rounds SET decision_id = pDecisionId, decision_round_user_id = lDocumentReviewRoundUserId WHERE id = lDocumentRoundId;
			UPDATE pjs.documents SET state_id = pDocumentStateId WHERE id = pDocumentId;
			
			SELECT INTO lVersionId id FROM pjs.document_versions WHERE document_id = pDocumentId AND version_type_id = cAuthorVersionType ORDER BY id DESC LIMIT 1;
			
			SELECT INTO lRoundId id FROM spCreateDocumentRound(pDocumentId, lAuthorRoundType);	
			UPDATE pjs.document_review_rounds SET create_from_version_id = lVersionId WHERE id = lRoundId;
			
			UPDATE pjs.documents SET
				current_round_id = lRoundId
			WHERE id = pDocumentId;
			
			SELECT INTO lJournalId d.journal_id FROM pjs.documents d WHERE d.id = pDocumentId;
			-- check if all reviews are submitted (event)
			
			IF pDecisionId = lRejectDecisionId THEN
				lSubmittedEventType = 25;
			ELSEIF pDecisionId = lRejectNicelyDecisionId THEN
				lSubmittedEventType = 26;
			END IF;
			
			SELECT INTO lRes.event_id event_id FROM spCreateEvent(lSubmittedEventType, pDocumentId, pUid, lJournalId, null, null);
		ELSE
			RAISE EXCEPTION 'pjs.documentNotInReviewState';
		END IF;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveSERejectedDecision(
	pDecisionId int,
	pNotes varchar,
	pUid int, 
	pDocumentStateId int,
	pDocumentId int
) TO iusrpmt;
