-- Function: userfpass(character varying)

-- DROP FUNCTION userfpass(character varying);

CREATE OR REPLACE FUNCTION userfpass(pemail character varying, pUid int)
  RETURNS ret_userfpass AS
$BODY$
DECLARE
	lResult ret_UserFpass;
	lId int;
	ltmppass varchar;
BEGIN

	IF (pUid IS NULL) THEN
		SELECT INTO lId id FROM usr WHERE uname = pEmail;
	ELSE
		lId = pUid;
	END IF;
	
	IF lId IS NOT NULL THEN 
		ltmppass := md5(now() || pEmail);
		ltmppass := substring(ltmppass from 1 for 6);
		
		UPDATE usr SET upass = md5(ltmppass) WHERE id = lId;
		
		SELECT INTO lResult pEmail, ltmppass;
	END IF;
	
	RETURN lResult;
END;

$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION userfpass(character varying) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION userfpass(character varying) TO postgres;
GRANT EXECUTE ON FUNCTION userfpass(character varying) TO iusrpmt;
