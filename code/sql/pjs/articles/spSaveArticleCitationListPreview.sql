DROP TYPE ret_spSaveArticleCitationListPreview CASCADE;
CREATE TYPE ret_spSaveArticleCitationListPreview AS (
	cache_id bigint
);


CREATE OR REPLACE FUNCTION spSaveArticleCitationListPreview(
	pArticleId bigint,
	pPreview varchar
)
  RETURNS ret_spSaveArticleCitationListPreview AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleCitationListPreview;		
		lElementCacheTypeId int = 18;		
		lCacheId bigint;
	BEGIN				
		SELECT INTO lCacheId
			citation_list_cache_id
		FROM pjs.articles
		WHERE id = pArticleId;		
		
		IF lCacheId IS NULL THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type, article_id)
				VALUES (pPreview, lElementCacheTypeId, pArticleId);
				
			lCacheId = currval('pjs.article_cached_items_id_seq'::regclass);
			
			UPDATE pjs.articles SET
				citation_list_cache_id = lCacheId
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

GRANT EXECUTE ON FUNCTION spSaveArticleCitationListPreview(
	pArticleId bigint,	
	pPreview varchar
) TO iusrpmt;
