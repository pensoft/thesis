DROP FUNCTION pwt.spApiLogAuthenticate(
	pUname text,
	pPassword text,
	pIsSuccessful int,
	pErrMsg text
);

CREATE OR REPLACE FUNCTION pwt.spApiLogAuthenticate(
	pUname text,
	pPassword text,
	pIsSuccessful int,
	pErrMsg text,
	pIp inet
)
  RETURNS integer AS
$BODY$
		DECLARE
		BEGIN
			INSERT INTO pwt.api_authenticate_log(username, password, is_successful, err_msg, ip) VALUES (pUname, pPassword, pIsSuccessful::boolean, pErrMsg, pIp);
			RETURN 1;
		END;
	$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION pwt.spApiLogAuthenticate(
	pUname text,
	pPassword text,
	pIsSuccessful int,
	pErrMsg text,
	pIp inet
) TO iusrpmt;
