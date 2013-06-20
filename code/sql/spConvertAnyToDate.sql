CREATE OR REPLACE FUNCTION spConvertAnyToDate(
	pValue text
)
  RETURNS date AS
$BODY$
DECLARE
	lRes date;
	lRecord record;
BEGIN
	
	BEGIN
		lRes := pValue::date;
	EXCEPTION WHEN OTHERS THEN
		-- RAISE NOTICE 'Invalid date value: "%".  Returning NULL.', pValue;
		RETURN NULL;
	END;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spConvertAnyToDate(
	pValue text
) TO iusrpmt;

CREATE OR REPLACE FUNCTION spConvertAnyToDate(
	pValue anyelement
)
  RETURNS date AS
$BODY$
DECLARE
	lRes date;
	lRecord record;
BEGIN
	
	BEGIN
		lRes := pValue::text::date;
	EXCEPTION WHEN OTHERS THEN
		-- RAISE NOTICE 'Invalid date value: "%".  Returning NULL.', pValue;
		RETURN NULL;
	END;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spConvertAnyToDate(
	pValue anyelement
) TO iusrpmt;