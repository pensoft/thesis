DROP TYPE ret_spSaveArticleAuthorPreview CASCADE;
CREATE TYPE ret_spSaveArticleAuthorPreview AS (
	cache_id bigint
);

CREATE OR REPLACE FUNCTION spSaveArticleAuthorPreview(
	pArticleId bigint,	
	pAuthorUid int,
	pPreview varchar
)
  RETURNS ret_spSaveArticleAuthorPreview AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleAuthorPreview;		
		lElementCacheTypeId int = 17;
		lArticleElementId bigint;
		lCacheId bigint;
	BEGIN				
		SELECT INTO lArticleElementId, lCacheId
			ar.id, ar.cache_id
		FROM pjs.article_authors ar		
		WHERE ar.article_id = pArticleId AND ar.author_uid = pAuthorUid;
		
		IF lArticleElementId IS NULL THEN
			INSERT INTO article_authors(article_id, author_uid)
				VALUES (pArticleId, pAuthorUid);
			lArticleElementId = currval('pjs.article_authors_id_seq'::regclass);
						
		END IF;	
		
		IF lCacheId IS NULL THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type)
				VALUES (pPreview, lElementCacheTypeId);
				
			lCacheId = currval('pjs.article_cached_items_id_seq'::regclass);
			
			UPDATE pjs.article_authors SET
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

GRANT EXECUTE ON FUNCTION spSaveArticleAuthorPreview(
	pArticleId bigint,	
	pAuthorUid int,
	pPreview varchar
) TO iusrpmt;
