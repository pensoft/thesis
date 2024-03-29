DROP TYPE ret_spSaveArticlePreview CASCADE;
CREATE TYPE ret_spSaveArticlePreview AS (
	cache_id bigint
);

CREATE OR REPLACE FUNCTION spSaveArticlePreview(
	pArticleId bigint,
	pPreview varchar
)
  RETURNS ret_spSaveArticlePreview AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticlePreview;		
		lElementCacheTypeId int = 2;		
		lElementMetricTypeId int = 1;	
		lCacheId bigint;
	BEGIN				
		SELECT INTO lCacheId
			preview_cache_id
		FROM pjs.articles
		WHERE id = pArticleId;		
		
		IF lCacheId IS NULL THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type, article_id)
				VALUES (pPreview, lElementCacheTypeId, pArticleId);
				
			lCacheId = currval('pjs.article_cached_items_id_seq'::regclass);
			
			UPDATE pjs.articles SET
				preview_cache_id = lCacheId
			WHERE id = pArticleId;
		ELSE
			UPDATE pjs.article_cached_items SET
				cached_val = pPreview,
				lastmoddate = now()
			WHERE id = lCacheId;
		END IF;
		
		PERFORM spCreateArticleMetric(pArticleId, lElementMetricTypeId);
		
		lRes.cache_id = lCacheId;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveArticlePreview(
	pArticleId bigint,	
	pPreview varchar
) TO iusrpmt;
