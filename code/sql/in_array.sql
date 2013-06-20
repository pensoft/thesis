DROP FUNCTION in_array(
	pElement anyelement,
	pArray anyarray
);

CREATE OR REPLACE FUNCTION in_array(	
	pElement anyelement,
	pArray anyarray
)
  RETURNS boolean AS
$BODY$
	DECLARE
		lResult pArray%TYPE;
	BEGIN	
		RETURN pArray @> ARRAY[pElement];
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION in_array(
	pElement anyelement,
	pArray anyarray
) TO iusrpmt;
