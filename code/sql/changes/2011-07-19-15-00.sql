CREATE TABLE taxon_cache(
	id bigserial PRIMARY KEY,
	taxon_name varchar,
	createdate timestamp DEFAULT NOW(),
	state int DEFAULT 0,
	pid int DEFAULT 0
);

GRANT ALL ON TABLE taxon_cache TO iusrpmt;


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
