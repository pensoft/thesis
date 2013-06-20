CREATE OR REPLACE FUNCTION spHandleCanProceedAction() RETURNS int AS
$BODY$
	DECLARE
		lRecord record;
		cClosedPeerReview CONSTANT int := 2;
		cCommunityPeerReview CONSTANT int := 3;
		cPublicPeerReview CONSTANT int := 4;
		cDocumentReviewStateID CONSTANT int := 3;
		lOffset int;
		
		cCanProceedEventType CONSTANT int := 38;
		cSERoleId CONSTANT int := 3;
		lSEUsrId bigint;
		lCanTakeDecision boolean;
		lCanProceedFlag boolean;
	BEGIN		
	
		FOR lRecord IN 
			SELECT d.id as document_id, drr.id as round_id
			FROM pjs.documents d
			JOIN pjs.document_review_rounds drr ON drr.id = d.current_round_id
			WHERE d.state_id = cDocumentReviewStateID
				AND d.document_review_type_id IN (cClosedPeerReview, cCommunityPeerReview, cPublicPeerReview)
				AND drr.can_proceed = FALSE
		LOOP
			SELECT INTO lCanProceedFlag can_proceed FROM pjs.document_review_rounds WHERE id = lRecord.round_id;
			SELECT INTO lCanTakeDecision result FROM pjs.spCheckIfSECanTakeADecision(lRecord.document_id);
			
			IF(lCanTakeDecision = TRUE AND lCanProceedFlag = FALSE) THEN
				SELECT INTO lSEUsrId dru.id 
				FROM pjs.document_users du
				JOIN pjs.document_review_round_users dru ON dru.document_user_id = du.id AND round_id = lRecord.round_id
				WHERE du.role_id = cSERoleId AND du.document_id = lRecord.document_id;
				
				PERFORM pjs.spUpdateDueDates(1, lRecord.document_id, cCanProceedEventType, NULL, lSEUsrId);
				
				UPDATE pjs.document_review_rounds SET can_proceed = TRUE WHERE id = lRecord.round_id;
			END IF;
		END LOOP;
		
		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spHandleCanProceedAction() TO iusrpmt;
