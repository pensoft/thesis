-- Function: pwt.spdeletecomment(integer, integer)

-- DROP FUNCTION pwt.spdeletecomment(integer, integer);

CREATE OR REPLACE FUNCTION pwt.spdeletecomment(pcommentid integer, pUid integer)
  RETURNS integer AS
$BODY$
	DECLARE
		lHasRights int;
		lRet int;
	BEGIN
		lRet := 0;
		SELECT INTO lHasRights count(id) FROM pwt.msg WHERE id = pcommentid AND usr_id = pUid;
		IF lHasRights > 0 THEN
			DELETE FROM pwt.msg WHERE document_id = pcommentid OR rootid = pcommentid;
			lRet := 1;
		END IF;
		RETURN lRet;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spdeletecomment(integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spdeletecomment(integer, integer) TO public;
GRANT EXECUTE ON FUNCTION pwt.spdeletecomment(integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spdeletecomment(integer, integer) TO iusrpmt;
