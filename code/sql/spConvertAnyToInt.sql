CREATE OR REPLACE FUNCTION spConvertAnyToInt(
	pValue text
)
  RETURNS int AS
$BODY$
DECLARE
	lRes int;
	lRecord record;
BEGIN
	
	BEGIN
		lRes := pValue::int;
	EXCEPTION WHEN OTHERS THEN
		-- RAISE NOTICE 'Invalid integer value: "%".  Returning NULL.', pValue;
		RETURN NULL;
	END;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spConvertAnyToInt(
	pValue text
) TO iusrpmt;

CREATE OR REPLACE FUNCTION spConvertAnyToInt(
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

GRANT EXECUTE ON FUNCTION spConvertAnyToInt(
	pValue anyelement
) TO iusrpmt;