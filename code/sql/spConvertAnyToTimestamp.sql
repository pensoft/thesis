CREATE OR REPLACE FUNCTION spConvertAnyToTimestamp(
	pValue text
)
  RETURNS timestamp AS
$BODY$
DECLARE
	lRes timestamp;
	lRecord record;
BEGIN
	
	BEGIN
		lRes := pValue::timestamp;
	EXCEPTION WHEN OTHERS THEN
		-- RAISE NOTICE 'Invalid date value: "%".  Returning NULL.', pValue;
		RETURN NULL;
	END;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spConvertAnyToTimestamp(
	pValue text
) TO iusrpmt;

CREATE OR REPLACE FUNCTION spConvertAnyToTimestamp(
	pValue anyelement
)
  RETURNS date AS
$BODY$
DECLARE
	lRes timestamp;
	lRecord record;
BEGIN
	
	BEGIN
		lRes := pValue::text::timestamp;
	EXCEPTION WHEN OTHERS THEN
		-- RAISE NOTICE 'Invalid date value: "%".  Returning NULL.', pValue;
		RETURN NULL;
	END;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spConvertAnyToTimestamp(
	pValue anyelement
) TO iusrpmt;