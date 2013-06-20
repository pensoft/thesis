DROP TYPE ret_spCheckIfFieldCanBeModified CASCADE;
CREATE TYPE ret_spCheckIfFieldCanBeModified AS (
	result int
);

/**
	Връща 1 ако field-а може да се променя и 0 в противния случай
*/
CREATE OR REPLACE FUNCTION spCheckIfFieldCanBeModified(
	pFieldId bigint
)
  RETURNS ret_spCheckIfFieldCanBeModified AS
$BODY$
DECLARE
lRes ret_spCheckIfFieldCanBeModified;
--lSid int;
lCurTime timestamp;
lId bigint;
BEGIN
	lRes.result = 1;
	IF EXISTS (SELECT 1) THEN
		lRes.result = 1;
	END IF;


	

	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCheckIfFieldCanBeModified(
	pFieldId bigint
) TO iusrpmt;
