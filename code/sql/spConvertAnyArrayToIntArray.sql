DROP FUNCTION spConvertAnyArrayToIntArray(
	pValue anyarray
);

CREATE OR REPLACE FUNCTION spConvertAnyArrayToIntArray(
	pValue anyarray
)
  RETURNS int[] AS
$BODY$
DECLARE
	lRes int[];
	lRecord record;
	lIter int;
	lTemp int;
BEGIN
	lRes = ARRAY[]::int[];
	FOR lIter IN
		1 .. coalesce(array_upper(pValue, 1), 0)
	LOOP
	
		lTemp = spConvertAnyToInt(pValue[lIter]);
		IF lTemp IS NOT NULL THEN
			lRes = array_append(lRes, lTemp);
		END IF;
	END LOOP;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spConvertAnyArrayToIntArray(
	pValue anyarray
) TO iusrpmt;
