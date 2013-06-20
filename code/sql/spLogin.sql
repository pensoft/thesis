-- Function: splogin(puname varchar, ppass varchar, pip varchar)

DROP FUNCTION splogin(puname varchar, ppass varchar, pip varchar);

DROP TYPE retsplogin;

CREATE TYPE retsplogin AS (
    id int,
    uname varchar,
    fullname varchar,
    url varchar,
    actype int,
    error int
);
ALTER TYPE retsplogin OWNER TO postgres84;

CREATE OR REPLACE FUNCTION splogin(puname varchar, ppass varchar, pip varchar)
  RETURNS SETOF retsplogin AS
$BODY$
	DECLARE
		lResult retspLogin;
		luname varchar;
		lfullname varchar;
		lid int;
		lstate int;
		ltype int;
		lerr int;
	BEGIN
		SELECT INTO lid, luname, lfullname, lstate, ltype 
			id, uname, name, state, utype 
		FROM usr 
		WHERE uname = puname AND upass = md5(ppass);
		
		lerr := 0;
		IF lstate = 0 THEN
			lerr := 1;
		END IF;
		
		IF lstate = 1 AND ltype = 1 THEN
			lerr := 2;
		END IF;
		
		IF lid IS NOT NULL AND lerr = 0 THEN
			FOR lResult IN
				SELECT 
					lid, 
					luname,
					lfullname,
					s.url,
					MAX(ga.type)
				FROM 
					secgrpdet gd
					JOIN secgrpacc ga ON (gd.gid = ga.gid)
					JOIN secsites s ON (s.id = ga.sid AND s.type = 1)
				WHERE 
					gd.uid = lid
				GROUP BY ga.sid, s.url
			LOOP
				RETURN NEXT lResult;
			END LOOP;
		ELSE 
			lResult.error := lerr;
			RETURN NEXT lResult;
		END IF;
		
		RETURN;
	END ;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION splogin(puname varchar, ppass varchar, pip varchar) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION splogin(puname varchar, ppass varchar, pip varchar) TO postgres84;
GRANT EXECUTE ON FUNCTION splogin(puname varchar, ppass varchar, pip varchar) TO iusrpmt;
