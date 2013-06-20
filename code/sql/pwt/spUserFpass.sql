DROP TYPE IF EXISTS ret_userfpass_new CASCADE;

CREATE TYPE ret_userfpass_new AS
   (uname character varying,
    upass character varying,
	fullname character varying,
	oldpjs_cid integer
	);
ALTER TYPE ret_userfpass_new OWNER TO postgres;


CREATE OR REPLACE FUNCTION spUserFpass(pemail character varying, puid integer)
  RETURNS ret_userfpass_new AS
$BODY$
DECLARE
	lResult ret_userfpass_new;
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
		
		SELECT INTO lResult u.uname, ltmppass as pass, coalesce(ut.name || ' ' || u.first_name || ' ' || u.last_name, u.uname) as fullname, oldpjs_cid as oldpjs_cid
		FROM usr u
		LEFT JOIN usr_titles ut ON ut.id = u.usr_title_id
		WHERE u.id = lId;
		
	END IF;
	
	RETURN lResult;
END;

$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spUserFpass(character varying, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spUserFpass(character varying, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spUserFpass(character varying, integer) TO public;
GRANT EXECUTE ON FUNCTION spUserFpass(character varying, integer) TO iusrpmt;
