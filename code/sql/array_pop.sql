DROP FUNCTION array_pop(
	pArray anyarray,
	pElement anyelement
);

CREATE OR REPLACE FUNCTION array_pop(
	pArray anyarray,
	pElement anyelement
)
  RETURNS anyarray AS
$BODY$
	DECLARE
		lResult pArray%TYPE;
	BEGIN
		SELECT ARRAY(
			SELECT b.e FROM (
				SELECT unnest(pArray)
			) AS b(e) WHERE b.e <> pElement
		) INTO lResult;

		RETURN lResult;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION array_pop(
	pArray anyarray,
	pElement anyelement
) TO iusrpmt;
