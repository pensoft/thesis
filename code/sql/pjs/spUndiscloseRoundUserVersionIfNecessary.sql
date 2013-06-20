DROP TYPE IF EXISTS ret_spUndiscloseRoundUserVersionIfNecessary CASCADE;
CREATE TYPE ret_spUndiscloseRoundUserVersionIfNecessary AS (
	result int
);

CREATE OR REPLACE FUNCTION pjs.spUndiscloseRoundUserVersionIfNecessary(
	pRoundUserId bigint
)
	RETURNS ret_spUndiscloseRoundUserVersionIfNecessary AS
$BODY$
	DECLARE
		lRes ret_spUndiscloseRoundUserVersionIfNecessary;
		lRecord record;		
	BEGIN		
		SELECT INTO lRecord
			f.*, ru.document_version_id, du.role_id
		FROM pjs.document_review_round_users_form f
		JOIN pjs.document_review_round_users ru ON ru.id = f.document_review_round_user_id
		JOIN pjs.document_users du ON du.id = ru.document_user_id
		WHERE f.document_review_round_user_id = pRoundUserId;
		
		IF coalesce(lRecord.disclose_name, 0) = 0 THEN
			PERFORM pjs.spUndiscloseVersion(lRecord.document_version_id, lRecord.role_id);
		END IF;
		
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spUndiscloseRoundUserVersionIfNecessary(
	pRoundUserId bigint
) TO iusrpmt;
