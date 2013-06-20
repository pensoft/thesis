-- Function: siteloginremember(character varying, inet)

-- DROP FUNCTION siteloginremember(character varying, inet);

CREATE OR REPLACE FUNCTION siteloginremember(phash character varying, pip inet)
  RETURNS t_usr AS
$BODY$
DECLARE
	lResult t_usr;
	lRecord RECORD;
BEGIN
	SELECT INTO lRecord id, uname, coalesce(name, uname) AS fullname, email, usr_title_id, photo_id, utype, state, ut.name AS salutation, u.staff, pIP, u.admin AS ip
	FROM usr WHERE md5(uname || '-' || upass) = pHash;
	
	IF lRecord.id IS NOT NULL THEN
		SELECT INTO lResult lRecord.id, lRecord.uname, lRecord.fullname, lRecord.email, lRecord.usr_title_id, lRecord.photo_id, lRecord.utype, lRecord.state, lRecord.salutation, lRecord.staff, lRecord.ip, lRecord.admin;
	END IF;
	
	RETURN lResult;
END ;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION siteloginremember(character varying, inet) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION siteloginremember(character varying, inet) TO postgres;
GRANT EXECUTE ON FUNCTION siteloginremember(character varying, inet) TO public;
GRANT EXECUTE ON FUNCTION siteloginremember(character varying, inet) TO iusrpmt;
GRANT EXECUTE ON FUNCTION siteloginremember(character varying, inet) TO pensoft;
