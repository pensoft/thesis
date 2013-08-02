DROP TYPE IF EXISTS pjs.ret_spDeleteComment CASCADE;
CREATE TYPE pjs.ret_spDeleteComment AS (
	result int
);

CREATE OR REPLACE FUNCTION pjs.spDeleteComment(
	pCommentId bigint,
	pUserId int
)
  RETURNS pjs.ret_spDeleteComment AS
$BODY$
	DECLARE
		lRes pjs.ret_spDeleteComment;
		lVersionId bigint;
		lRoundId bigint;
		lVersionIsReadonly int;
	BEGIN		
		SELECT INTO lVersionId 
			version_id 
		FROM pjs.msg 
		WHERE id = rootid AND id = original_id AND id = pCommentId AND usr_id = pUserId;
			
		IF lVersionId IS NULL THEN
			RAISE EXCEPTION 'pjs.youCannotDeleteThisComment';
		END IF;
		
		lVersionIsReadonly = pjs.spCheckIfPjsVersionIsReadonly(lVersionId);
		IF coalesce(lVersionIsReadonly, 0) = 1 THEN
			RAISE EXCEPTION 'pjs.specifiedVersionIsReadonly';
		END IF;
		
		DELETE FROM pjs.msg WHERE rootid = pCommentId AND version_id = lVersionId;
		
		lRes.result = 1;		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spDeleteComment(
	pCommentId bigint,
	pUserId int
) TO iusrpmt;
