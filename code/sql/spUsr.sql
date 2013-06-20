-- Function: spUsr(pOp int, pId int, pUname varchar, pName varchar, pUPass varchar)

DROP FUNCTION spUsr(pOp int, pId int, pUname varchar, pName varchar, pUPass varchar);

DROP FUNCTION spUsr(
	pOp int, 
	pId int, 
	pUname varchar, 
	pName varchar, 
	pUPass varchar, 
	pEmail varchar, 
	pPhone varchar, 
	pState int,
	pType int
);

CREATE OR REPLACE FUNCTION spUsr(
	pOp int, 
	pId int, 
	pUname varchar, 
	pName varchar, 
	pUPass varchar, 
	pEmail varchar, 
	pPhone varchar, 
	pState int,
	pType int
) RETURNS usr AS
$BODY$
DECLARE
	lResult usr;
BEGIN

	IF (pOp = 0) THEN
		SELECT INTO lResult *
		FROM usr
		WHERE id = pId;
	ELSIF (pOp = 1) THEN
		
		IF (pId is null) THEN
			IF EXISTS (SELECT * FROM usr WHERE uname = pUname) THEN
				RAISE EXCEPTION 'This user exists!';
			END IF;
			
			INSERT INTO usr(uname, name, upass, email, phone, state, utype)
			VALUES(pUname, pName, md5(pUPass), pEmail, pPhone, pState, pType);
			lResult.id = currval('usr_id_seq');
		ELSE
			
			IF EXISTS (SELECT * FROM usr WHERE uname = pUname AND id <> pId) THEN
				RAISE EXCEPTION 'This user exists!';
			END IF;
			
			UPDATE usr SET 
			name = pName,
			email = pEmail,
			phone = pPhone,
			state = pState,
			utype = pType,
			upass = coalesce(md5(pUPass), upass)
			WHERE id = pId;
		END IF;
	ELSIF (pOp = 3) THEN
		DELETE FROM usr WHERE id = pId;
	END IF;

	RETURN lResult;

END ;
$BODY$
  LANGUAGE 'plpgsql' SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spUsr(
	pOp int, 
	pId int, 
	pUname varchar, 
	pName varchar, 
	pUPass varchar, 
	pEmail varchar, 
	pPhone varchar, 
	pState int,
	pType int
) TO iusrpmt;

REVOKE ALL ON FUNCTION spUsr(
	pOp int, 
	pId int, 
	pUname varchar, 
	pName varchar, 
	pUPass varchar, 
	pEmail varchar, 
	pPhone varchar, 
	pState int,
	pType int
) FROM public;
