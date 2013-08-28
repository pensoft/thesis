DROP TYPE ret_spSaveArticleTaxon CASCADE;
CREATE TYPE ret_spSaveArticleTaxon AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spSaveArticleTaxon(
	pArticleId bigint,	
	pTaxonName varchar
)
  RETURNS ret_spSaveArticleTaxon AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleTaxon;		
		lArticleElementId bigint;
	BEGIN				
		SELECT INTO lArticleElementId
			id
		FROM spGetTaxonId(pTaxonName);
		
		IF NOT EXISTS (
			SELECT * 
			FROM pjs.article_taxons
			WHERE article_id = pArticleId AND taxon_id = lArticleElementId
		) THEN
			INSERT INTO pjs.article_taxons(article_id, taxon_id)
				VALUES (pArticleId, lArticleElementId);
		END IF;
		
		lRes.id = lArticleElementId;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveArticleTaxon(
	pArticleId bigint,	
	pTaxonName varchar
) TO iusrpmt;
