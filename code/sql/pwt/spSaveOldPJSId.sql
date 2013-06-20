DROP FUNCTION IF EXISTS pSaveOldPJSId(character varying, integer);
CREATE OR REPLACE FUNCTION spSaveOldPJSId(pUname character varying, pOldPjsId integer)
 RETURNS integer AS
$BODY$
DECLARE
	lId int;
	lMaxPosition int;
BEGIN
	
	UPDATE usr SET oldpjs_cid = pOldPjsId WHERE uname = pUname;

	RETURN 1;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spSaveOldPJSId(character varying, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spSaveOldPJSId(character varying, integer) TO public;
GRANT EXECUTE ON FUNCTION spSaveOldPJSId(character varying, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spSaveOldPJSId(character varying, integer) TO iusrpmt;
