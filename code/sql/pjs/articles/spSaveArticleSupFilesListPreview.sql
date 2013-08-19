DROP TYPE ret_spSaveArticleSupFilesListPreview CASCADE;
CREATE TYPE ret_spSaveArticleSupFilesListPreview AS (
	cache_id bigint
);


CREATE OR REPLACE FUNCTION spSaveArticleSupFilesListPreview(
	pArticleId bigint,
	pPreview varchar
)
  RETURNS ret_spSaveArticleSupFilesListPreview AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleSupFilesListPreview;		
		lElementCacheTypeId int = 14;		
		lCacheId bigint;
	BEGIN				
		SELECT INTO lCacheId
			sup_files_list_cache_id
		FROM pjs.articles
		WHERE id = pArticleId;		
		
		IF lCacheId IS NULL THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type, article_id)
				VALUES (pPreview, lElementCacheTypeId, pArticleId);
				
			lCacheId = currval('pjs.article_cached_items_id_seq'::regclass);
			
			UPDATE pjs.articles SET
				sup_files_list_cache_id = lCacheId
			WHERE id = pArticleId;
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

GRANT EXECUTE ON FUNCTION spSaveArticleSupFilesListPreview(
	pArticleId bigint,	
	pPreview varchar
) TO iusrpmt;
