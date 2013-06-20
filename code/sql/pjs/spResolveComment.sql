DROP TYPE IF EXISTS pjs.ret_spResolveComment CASCADE;
CREATE TYPE pjs.ret_spResolveComment AS (
	result int,
	resolve_uid int,
	resolve_date timestamp,
	is_resolved int
);

CREATE OR REPLACE FUNCTION pjs.spResolveComment(
	pCommentId int,
	pResolve int,
	pUid int
)
  RETURNS pjs.ret_spResolveComment AS
$BODY$
	DECLARE
		lRes pjs.ret_spResolveComment;
		lIsResolved int;
		lOriginalId int;
	BEGIN	
		lRes.result = 1;		
		
		SELECT INTO lIsResolved, lOriginalId
			is_resolved::int,
			original_id
		FROM pjs.msg
		WHERE id = pCommentId;	
		
		IF lOriginalId IS NULL THEN
			RETURN lRes;
		END IF;
		
		lIsResolved = coalesce(lIsResolved, 0);
		
		IF pResolve > 0 AND lIsResolved = 0 THEN
			UPDATE pjs.msg SET
				is_resolved = true,
				resolve_uid = pUid,
				resolve_date = now()
			WHERE original_id = lOriginalId;
		ELSEIF pResolve = 0 AND lIsResolved > 0 THEN
			UPDATE pjs.msg SET
				is_resolved = false,
				resolve_uid = null,
				resolve_date = null
			WHERE original_id = lOriginalId;
		END IF;
			
		SELECT INTO lRes
			1, resolve_uid, resolve_date, is_resolved::int 
		FROM pjs.msg
		WHERE id = pCommentId;
		
		
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs.spResolveComment(
	pCommentId int,
	pResolve int,
	pUid int
) TO iusrpmt;
