-- DROP FUNCTION cmgetsecsitesublinks(psid int4);

CREATE OR REPLACE FUNCTION cmgetsecsitesublinks(psid int4)
  RETURNS SETOF retcmgetsecsitesublinks AS
$BODY$
	DECLARE
		lurl varchar(255);
		lcnt int;
		lurllen int;
		lResult	retcmGetSecSiteSubLinks%ROWTYPE;
	BEGIN
		SELECT INTO 
			lurl,
			lcnt, 
			lurllen
			url,
			cnt, 
			length(url)
		FROM 
			secsites
		WHERE
			id = psid;

		FOR lResult IN
			SELECT 
				id,
				url
			FROM
				secsites
			WHERE
				substring(url, 1, lurllen) = lurl
				AND cnt = lcnt + 1
				AND substring(name, 1, 1) <> '*'
			ORDER BY ord asc
		LOOP
			RETURN NEXT lResult;
		END LOOP;
		RETURN;
	END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION cmgetsecsitesublinks(psid int4) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION cmgetsecsitesublinks(psid int4) TO postgres84;
GRANT EXECUTE ON FUNCTION cmgetsecsitesublinks(psid int4) TO iusrpmt;
