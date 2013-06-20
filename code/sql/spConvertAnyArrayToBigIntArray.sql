DROP FUNCTION spConvertAnyArrayToBigIntArray(
	pValue anyarray
);

CREATE OR REPLACE FUNCTION spConvertAnyArrayToBigIntArray(
	pValue anyarray
)
  RETURNS bigint[] AS
$BODY$
DECLARE
	lRes bigint[];
	lRecord record;
	lIter int;
	lTemp bigint;
BEGIN
	lRes = ARRAY[]::bigint[];
	FOR lIter IN
		1 .. coalesce(array_upper(pValue, 1), 0)
	LOOP
	
		lTemp = spConvertAnyToBigInt(pValue[lIter]);
		IF lTemp IS NOT NULL THEN
			lRes = array_append(lRes, lTemp);
		END IF;
	END LOOP;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spConvertAnyArrayToBigIntArray(
	pValue anyarray
) TO iusrpmt;
