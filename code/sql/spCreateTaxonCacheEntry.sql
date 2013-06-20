DROP TYPE ret_spCreateTaxonCacheEntry CASCADE;
CREATE TYPE ret_spCreateTaxonCacheEntry AS (
	result int
);

CREATE OR REPLACE FUNCTION spCreateTaxonCacheEntry(
	pTaxonName varchar
)
  RETURNS ret_spCreateTaxonCacheEntry AS
$BODY$
DECLARE
	lRes ret_spCreateTaxonCacheEntry;
	
BEGIN
	INSERT INTO taxon_cache(taxon_name) VALUES (pTaxonName);
	lRes.result = 1;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateTaxonCacheEntry(
	pTaxonName varchar
) TO iusrpmt;
