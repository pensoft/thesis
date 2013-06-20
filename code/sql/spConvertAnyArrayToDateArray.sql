DROP FUNCTION spConvertAnyArrayToDateArray(
	pValue anyarray
);

CREATE OR REPLACE FUNCTION spConvertAnyArrayToDateArray(
	pValue anyarray
)
  RETURNS date[] AS
$BODY$
DECLARE
	lRes date[];
	lRecord record;
	lIter int;
	lTemp date;
BEGIN
	lRes = ARRAY[]::date[];
	FOR lIter IN
		1 .. array_upper(pValue, 1)
	LOOP
	
		lTemp = spConvertAnyToDate(pValue[lIter]);
		IF lTemp IS NOT NULL THEN
			lRes = array_append(lRes, lTemp);
		END IF;
	END LOOP;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spConvertAnyArrayToDateArray(
	pValue anyarray
) TO iusrpmt;
