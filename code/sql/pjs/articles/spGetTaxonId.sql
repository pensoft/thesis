DROP TYPE ret_spGetTaxonId CASCADE;
CREATE TYPE ret_spGetTaxonId AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spGetTaxonId(	
	pTaxonName varchar
)
  RETURNS ret_spGetTaxonId AS
$BODY$
	DECLARE		
		lRes ret_spGetTaxonId;		
		lArticleElementId bigint;
	BEGIN				
		SELECT INTO lArticleElementId
			id
		FROM pjs.taxons 
		WHERE spNormalizeTaxonName(name) = spNormalizeTaxonName(pTaxonName);
		
		IF lArticleElementId IS NULL THEN
			INSERT INTO pjs.taxons(name)
				VALUES (pTaxonName);
			lArticleElementId = currval('pjs.taxons_id_seq'::regclass);
		END IF;	
				
		lRes.id = lArticleElementId;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetTaxonId(
	pTaxonName varchar
) TO iusrpmt;
