DROP TYPE ret_spCreateDocumentRound CASCADE;
CREATE TYPE ret_spCreateDocumentRound AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spCreateDocumentRound(
	pDocumentId bigint,
	pRoundType int
)
  RETURNS ret_spCreateDocumentRound AS
$BODY$
	DECLARE
		lRes ret_spCreateDocumentRound;	
				
		lRoundNumber int;
		lReviewRoundType int;
		lRoundDueDate timestamp;
		lMaxReviewerRounds int;
		lRoundCount int;
		lAuthorRoundType int;
	BEGIN		
		lReviewRoundType = 1;
		lMaxReviewerRounds = 3;
		lAuthorRoundType = 5;
	
		SELECT INTO lRoundNumber, lRoundCount max(round_number), count(id) 
		FROM pjs.document_review_rounds
		WHERE document_id = pDocumentId AND round_type_id = pRoundType;
		
		lRoundNumber = coalesce(lRoundNumber, 0) + 1;
		lRoundDueDate = NULL;
		
		IF pRoundType = lReviewRoundType THEN
			--lRoundDueDate = now() + INTERVAL '1 week';
			IF(lRoundCount > lMaxReviewerRounds) THEN
				RAISE EXCEPTION 'pjs.maxreviewerroundsexceeded';
			END IF;
		/*ELSEIF pRoundType = lAuthorRoundType THEN
			lRoundDueDate = now() + INTERVAL '2 weeks';
		ELSE
			lRoundDueDate = NULL;
		*/
		END IF;
		
		INSERT INTO pjs.document_review_rounds(document_id, round_number, round_type_id, round_due_date) VALUES (pDocumentId, lRoundNumber, pRoundType, lRoundDueDate);

		lRes.id = currval('pjs.document_review_rounds_id_seq');
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateDocumentRound(
	pDocumentId bigint,
	pRoundType int
) TO iusrpmt;
