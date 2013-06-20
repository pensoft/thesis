-- Function: cmaddsecgrp(pname "varchar")

-- DROP FUNCTION cmaddsecgrp(pname "varchar");

DROP TYPE retcmaddsecgrp CASCADE;

CREATE TYPE retcmaddsecgrp AS (res int);

CREATE OR REPLACE FUNCTION cmaddsecgrp(pname "varchar")
  RETURNS retcmaddsecgrp AS
$BODY$
	DECLARE
		lResult retcmAddSecGrp%ROWTYPE;
	BEGIN
		IF EXISTS(SELECT * FROM secgrp WHERE name = pname) THEN
			lResult.res := 0;
		ELSE
			INSERT INTO secgrp (name) VALUES(pname);
			lResult.res := currval('secgrp_id_seq');
		END IF;
		RETURN lResult;
	END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION cmaddsecgrp(pname "varchar") OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION cmaddsecgrp(pname "varchar") TO postgres84;
GRANT EXECUTE ON FUNCTION cmaddsecgrp(pname "varchar") TO iusrpmt;


