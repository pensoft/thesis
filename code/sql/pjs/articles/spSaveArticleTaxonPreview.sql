DROP TYPE ret_spSaveArticleTaxonPreview CASCADE;
CREATE TYPE ret_spSaveArticleTaxonPreview AS (
	cache_id bigint
);

CREATE OR REPLACE FUNCTION spSaveArticleTaxonPreview(
	pArticleId bigint,	
	pTaxonName varchar,
	pPreview varchar
)
  RETURNS ret_spSaveArticleTaxonPreview AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleTaxonPreview;		
		lElementCacheTypeId int = 7;
		lArticleElementId bigint;
		lCacheId bigint;
	BEGIN				
		SELECT INTO lArticleElementId, lCacheId
			ar.taxon_id, r.cache_id
		FROM pjs.article_taxons ar
		JOIN pjs.taxons r ON r.id = ar.taxon_id
		WHERE ar.article_id = pArticleId 
			AND lower(translate(r.name::text, ' ,.-*', '')) = lower(translate(pTaxonName, ' ,.-*', ''));
		
		IF lArticleElementId IS NULL THEN
			SELECT INTO lArticleElementId, lCacheId
				ar.taxon_id, r.cache_id
			FROM pjs.taxons r 
			WHERE lower(translate(r.name::text, ' ,.-*', '')) = lower(translate(pTaxonName, ' ,.-*', ''));
			
			IF lArticleElementId IS NULL THEN 
				INSERT INTO pjs.taxons(name)
					VALUES (pTaxonName);
				lArticleElementId = currval('pjs.taxons_id_seq'::regclass);
			END IF;
			
			INSERT INTO pjs.article_taxons(article_id, taxon_id) 
				VALUES (pArticleId, lArticleElementId);
		END IF;	
		
		IF lCacheId IS NULL THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type)
				VALUES (pPreview, lElementCacheTypeId);
				
			lCacheId = currval('pjs.article_cached_items_id_seq'::regclass);
			
			UPDATE pjs.taxons SET
				cache_id = lCacheId
			WHERE id = lArticleElementId;
			
		ELSE
			UPDATE pjs.article_cached_items SET
				cached_val = pPreview,
				lastmoddate = now()
			WHERE id = lCacheId;
		END IF;
		
		lRes.cache_id = lCacheId;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveArticleTaxonPreview(
	pArticleId bigint,	
	pTaxonName varchar,
	pPreview varchar
) TO iusrpmt;
