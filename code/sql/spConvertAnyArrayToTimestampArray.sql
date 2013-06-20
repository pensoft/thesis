DROP FUNCTION spConvertAnyArrayToTimestampArray(
	pValue anyarray
);

CREATE OR REPLACE FUNCTION spConvertAnyArrayToTimestampArray(
	pValue anyarray
)
  RETURNS timestamp[] AS
$BODY$
DECLARE
	lRes timestamp[];
	lRecord record;
	lIter int;
	lTemp timestamp;
BEGIN
	lRes = ARRAY[]::Timestamp[];
	FOR lIter IN
		1 .. coalesce(array_upper(pValue, 1), 0)
	LOOP
	
		lTemp = spConvertAnyToTimestamp(pValue[lIter]);
		IF lTemp IS NOT NULL THEN
			lRes = array_append(lRes, lTemp);
		END IF;
	END LOOP;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spConvertAnyArrayToTimestampArray(
	pValue anyarray
) TO iusrpmt;
