
CREATE OR REPLACE FUNCTION pjs.spCheckIfPjsVersionIsReadonly(
	pVersionId bigint
)
  RETURNS int AS
$BODY$
	DECLARE
		lVersionTypeIsReadonly boolean;
		lRoundDecision int;
	BEGIN
		SELECT INTO lVersionTypeIsReadonly
			t.is_readonly
		FROM pjs.document_versions v
		JOIN pjs.document_version_types t ON t.id = v.version_type_id
		WHERE v.id = pVersionId;
		
		IF coalesce(lVersionTypeIsReadonly, true) = true THEN 
			RETURN 1;
		END IF;
		
		SELECT INTO lRoundDecision 
			decision_id
		FROM pjs.document_review_round_users 
		WHERE document_version_id = pVersionId;
		
		IF lRoundDecision IS NOT NULL THEN
			RETURN 1;
		END IF;
				
		RETURN 0;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spCheckIfPjsVersionIsReadonly(
	pVersionId bigint
) TO iusrpmt;