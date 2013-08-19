DROP TYPE ret_spSaveArticleFigurePreview CASCADE;
CREATE TYPE ret_spSaveArticleFigurePreview AS (
	cache_id bigint
);

CREATE OR REPLACE FUNCTION spSaveArticleFigurePreview(
	pArticleId bigint,	
	pInstanceId bigint,
	pPreview varchar
)
  RETURNS ret_spSaveArticleFigurePreview AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleFigurePreview;		
		lElementCacheTypeId int = 4;
		lArticleElementId bigint;
		lCacheId bigint;
	BEGIN				
		SELECT INTO lArticleElementId, lCacheId
			id, cache_id
		FROM pjs.article_figures 
		WHERE article_id = pArticleId AND instance_id = pInstanceId;
		
		IF lArticleElementId IS NULL THEN
			INSERT INTO pjs.article_figures(article_id, instance_id)
				VALUES (pArticleId, pInstanceId);
			lArticleElementId = currval('pjs.article_figures_id_seq'::regclass);
		END IF;	
		
		IF lCacheId IS NULL THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type, article_id)
				VALUES (pPreview, lElementCacheTypeId, pArticleId);
				
			lCacheId = currval('pjs.article_cached_items_id_seq'::regclass);
			
			UPDATE pjs.article_figures SET
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

GRANT EXECUTE ON FUNCTION spSaveArticleFigurePreview(
	pArticleId bigint,	
	pInstanceId bigint,
	pPreview varchar
) TO iusrpmt;
