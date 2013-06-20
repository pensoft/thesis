-- Function: userfpass(character varying)

--DROP FUNCTION userfpass(character varying) CASCADE;
-- Type: ret_userfpass

DROP TYPE ret_userfpass CASCADE;

CREATE TYPE ret_userfpass AS
   (uname character varying,
    upass character varying,
	fullname character varying);
ALTER TYPE ret_userfpass OWNER TO postgres;

DROP FUNCTION IF EXISTS userfpass(pemail character varying, puid integer);

CREATE OR REPLACE FUNCTION userfpass(pemail character varying, puid integer)
  RETURNS ret_userfpass AS
$BODY$
DECLARE
	lResult ret_userfpass;
	lId int;
	ltmppass varchar;
	lhash varchar;
	lfullname varchar;
BEGIN

	IF (pUid IS NULL) THEN
		SELECT INTO lId id FROM usr WHERE uname = pEmail;
	ELSE
		lId = pUid;
	END IF;
	
	IF lId IS NOT NULL THEN 
		ltmppass := md5(now() || pEmail);
		ltmppass := substring(ltmppass from 1 for 6);
		lhash := md5(now() || ltmppass); /* Hash za autologina */
		
		UPDATE usr SET upass = md5(ltmppass), autolog_hash = lhash, plain_upass = ltmppass WHERE id = lId;
		
		SELECT INTO lResult u.uname, ltmppass as pass, coalesce(ut.name || ' ' || u.first_name || ' ' || u.last_name, u.uname) as fullname
		FROM usr u
		LEFT JOIN usr_titles ut ON ut.id = u.usr_title_id
		WHERE u.id = lId;
		
	END IF;
	
	RETURN lResult;
END;

$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION userfpass(character varying, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION userfpass(character varying, integer) TO postgres;
GRANT EXECUTE ON FUNCTION userfpass(character varying, integer) TO public;
GRANT EXECUTE ON FUNCTION userfpass(character varying, integer) TO iusrpmt;
