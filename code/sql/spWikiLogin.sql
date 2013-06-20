DROP TYPE ret_spWikiLogin CASCADE;
CREATE TYPE ret_spWikiLogin AS (
	id int,
	username varchar,
	password varchar
);

CREATE OR REPLACE FUNCTION spWikiLogin(
	pOper int,
	pId int,
	pUsername varchar,
	pPassword varchar
)
  RETURNS ret_spWikiLogin AS
$BODY$
DECLARE
lRes ret_spWikiLogin;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO wiki_login(username, password) VALUES (pUsername, pPassword);
		lId = currval('wiki_login_id_seq');
	ELSE -- Update
		UPDATE wiki_login SET
			username = pUsername,
			password = pPassword
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM wiki_login WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes id, username, password FROM wiki_login WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spWikiLogin(
	pOper int,
	pId int,
	pUsername varchar,
	pPassword varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spWikiLogin(
	pOper int,
	pId int,
	pUsername varchar,
	pPassword varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spWikiLogin(
	pOper int,
	pId int,
	pUsername varchar,
	pPassword varchar
) TO iusrpmt;
