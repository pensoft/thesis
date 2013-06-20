CREATE OR REPLACE FUNCTION spConvertAnyToBigInt(
	pValue text
)
  RETURNS bigint AS
$BODY$
DECLARE
	lRes bigint;
	lRecord record;
BEGIN
	
	BEGIN
		lRes := pValue::bigint;
	EXCEPTION WHEN OTHERS THEN
		-- RAISE NOTICE 'Invalid integer value: "%".  Returning NULL.', pValue;
		RETURN NULL;
	END;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spConvertAnyToBigInt(
	pValue text
) TO iusrpmt;

CREATE OR REPLACE FUNCTION spConvertAnyToBigInt(
	pValue anyelement
)
  RETURNS int AS
$BODY$
DECLARE
	lRes int;
	lRecord record;
BEGIN
	
	BEGIN
		lRes := pValue::text::int;
	EXCEPTION WHEN OTHERS THEN
		-- RAISE NOTICE 'Invalid integer value: "%".  Returning NULL.', pValue;
		RETURN NULL;
	END;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spConvertAnyToBigInt(
	pValue anyelement
) TO iusrpmt;