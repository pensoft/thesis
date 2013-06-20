--DROP FUNCTION langs(pOp int, pLangid int, pCode varchar, pName varchar);

CREATE OR REPLACE FUNCTION langs(pOp int, pLangid int, pCode varchar, pName varchar) RETURNS languages AS 
$$
DECLARE
	lResult languages;
BEGIN
	
IF (pOp = 0) THEN
	SELECT INTO lResult *
		FROM languages
		WHERE code = pCode;
ELSIF (pOp = 1) THEN
	IF NOT EXISTS(SELECT * FROM languages WHERE code = pCode) THEN --INSERT
		INSERT INTO languages(langid, code, name)
			VALUES(pLangid, pCode, pName);
	ELSE --UPDATE
		UPDATE languages
		SET langid = pLangid,
			code = pCode,
			name = pName
		WHERE code = pCode;
	END IF;
ELSIF (pOp = 3) THEN
	DELETE FROM languages WHERE code = pCode;
END IF;

RETURN lResult;

END ;
$$ LANGUAGE 'plpgsql' SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION langs(pOp int, pLangid int, pCode varchar, pName varchar) TO iusrpmt;
REVOKE ALL ON FUNCTION langs(pOp int, pLangid int, pCode varchar, pName varchar) FROM public;
