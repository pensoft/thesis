CREATE OR REPLACE FUNCTION spUpdateApproveDate()
  RETURNS int AS
$BODY$
	DECLARE
		lRecord record;
	BEGIN		
		
		FOR lRecord IN (
			select dr.document_id, drru.decision_date FROM  pjs.document_review_rounds dr 
				JOIN pjs.document_review_round_users drru ON drru.id = dr.decision_round_user_id
				WHERE dr.decision_id = 1 
					AND dr.round_type_id = 1
		)
		LOOP
			UPDATE pjs.documents SET is_approved = TRUE, approve_date = lRecord.decision_date WHERE id = lRecord.document_id;
		END LOOP;
		
		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spUpdateApproveDate() TO iusrpmt;
