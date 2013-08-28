CREATE OR REPLACE FUNCTION spNormalizeTaxonName(
	pName varchar
)
  RETURNS varchar AS
$BODY$
	DECLARE		
		lResult varchar;
	BEGIN			
		lResult = lower(translate(pName, ' ,.-*', ''));
		RETURN lResult;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spNormalizeTaxonName(
	pName varchar
) TO iusrpmt;
