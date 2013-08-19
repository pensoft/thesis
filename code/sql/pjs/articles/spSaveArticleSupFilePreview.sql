DROP TYPE ret_spSaveArticleSupFilePreview CASCADE;
CREATE TYPE ret_spSaveArticleSupFilePreview AS (
	cache_id bigint
);

CREATE OR REPLACE FUNCTION spSaveArticleSupFilePreview(
	pArticleId bigint,	
	pInstanceId bigint,
	pPreview varchar
)
  RETURNS ret_spSaveArticleSupFilePreview AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleSupFilePreview;		
		lElementCacheTypeId int = 6;
		lArticleElementId bigint;
		lCacheId bigint;
	BEGIN				
		SELECT INTO lArticleElementId, lCacheId
			id, cache_id
		FROM pjs.article_sup_files 
		WHERE article_id = pArticleId AND instance_id = pInstanceId;
		
		IF lArticleElementId IS NULL THEN
			INSERT INTO pjs.article_sup_files(article_id, instance_id)
				VALUES (pArticleId, pInstanceId);
			lArticleElementId = currval('pjs.article_sup_files_id_seq'::regclass);
		END IF;	
		
		IF lCacheId IS NULL THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type, article_id)
				VALUES (pPreview, lElementCacheTypeId, pArticleId);
				
			lCacheId = currval('pjs.article_cached_items_id_seq'::regclass);
			
			UPDATE pjs.article_sup_files SET
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

GRANT EXECUTE ON FUNCTION spSaveArticleSupFilePreview(
	pArticleId bigint,	
	pInstanceId bigint,
	pPreview varchar
) TO iusrpmt;
