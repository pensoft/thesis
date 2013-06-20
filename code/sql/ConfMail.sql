DROP FUNCTION ConfMail (pConfHash varchar(32));

CREATE OR REPLACE FUNCTION ConfMail (pConfHash varchar(32)) RETURNS int AS
$$
DECLARE
	lResult int;
	lId int;
BEGIN
	
	lResult := 0;
	
	SELECT INTO lId id FROM usr WHERE state = 0 AND confhash = pConfHash;
	
	IF lId IS NOT NULL THEN 
		lResult := 1;
		--UPDATE
		UPDATE usr SET state = 1 WHERE id = lId;
	END IF;
	
	RETURN lResult;
END;

$$
LANGUAGE 'plpgsql' SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION ConfMail (pConfHash varchar(32)) TO iusrpmt;

REVOKE ALL ON FUNCTION ConfMail (pConfHash varchar(32)) FROM public;



