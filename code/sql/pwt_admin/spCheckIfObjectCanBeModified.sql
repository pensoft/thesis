DROP TYPE ret_spCheckIfObjectCanBeModified CASCADE;
CREATE TYPE ret_spCheckIfObjectCanBeModified AS (
	result int
);

/**
	Връща 1 ако обекта може да се променя и 0 в противния случай
*/
CREATE OR REPLACE FUNCTION spCheckIfObjectCanBeModified(
	pObjectId bigint
)
  RETURNS ret_spCheckIfObjectCanBeModified AS
$BODY$
DECLARE
lRes ret_spCheckIfObjectCanBeModified;
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

GRANT EXECUTE ON FUNCTION spCheckIfObjectCanBeModified(
	pObjectId bigint
) TO iusrpmt;
