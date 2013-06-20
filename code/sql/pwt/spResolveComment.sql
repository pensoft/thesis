DROP TYPE IF EXISTS pwt.ret_spResolveComment CASCADE;
CREATE TYPE pwt.ret_spResolveComment AS (
	result int,
	resolve_uid int,
	resolve_date timestamp,
	is_resolved int
);

CREATE OR REPLACE FUNCTION pwt.spResolveComment(
	pCommentId int,
	pResolve int,
	pUid int
)
  RETURNS pwt.ret_spResolveComment AS
$BODY$
	DECLARE
		lRes pwt.ret_spResolveComment;
		lIsResolved int;		
	BEGIN	
		lRes.result = 1;		
		
		SELECT INTO lIsResolved
			is_resolved::int
		FROM pwt.msg
		WHERE id = pCommentId;	
				
		lIsResolved = coalesce(lIsResolved, 0);
		
		IF pResolve > 0 AND lIsResolved = 0 THEN
			UPDATE pwt.msg SET
				is_resolved = true,
				resolve_uid = pUid,
				resolve_date = now()
			WHERE id = pCommentId;
		ELSEIF pResolve = 0 AND lIsResolved > 0 THEN
			UPDATE pwt.msg SET
				is_resolved = false,
				resolve_uid = null,
				resolve_date = null
			WHERE id = pCommentId;
		END IF;
			
		SELECT INTO lRes
			1, resolve_uid, resolve_date, is_resolved::int 
		FROM pwt.msg
		WHERE id = pCommentId;
		
		
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spResolveComment(
	pCommentId int,
	pResolve int,
	pUid int
) TO iusrpmt;
