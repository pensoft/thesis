DROP TYPE ret_spSaveArticleReferencePreview CASCADE;
CREATE TYPE ret_spSaveArticleReferencePreview AS (
	cache_id bigint
);

CREATE OR REPLACE FUNCTION spSaveArticleReferencePreview(
	pArticleId bigint,	
	pInstanceId bigint,
	pPreview varchar
)
  RETURNS ret_spSaveArticleReferencePreview AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleReferencePreview;		
		lElementCacheTypeId int = 3;
		lArticleElementId bigint;
		lCacheId bigint;
	BEGIN				
		SELECT INTO lArticleElementId, lCacheId
			ar.reference_id, r.cache_id
		FROM pjs.article_references ar
		JOIN pjs."references" r ON r.id = ar.reference_id
		WHERE ar.article_id = pArticleId AND ar.instance_id = pInstanceId;
		
		IF lArticleElementId IS NULL THEN
			INSERT INTO pjs."references"(name)
				VALUES ('Reference ' || pInstanceId);
			lArticleElementId = currval('pjs.references_id_seq'::regclass);
			
			INSERT INTO pjs.article_references(article_id, reference_id, instance_id) 
				VALUES (pArticleId, lArticleElementId, pInstanceId);
		END IF;	
		
		IF lCacheId IS NULL THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type)
				VALUES (pPreview, lElementCacheTypeId);
				
			lCacheId = currval('pjs.article_cached_items_id_seq'::regclass);
			
			UPDATE pjs."references" SET
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

GRANT EXECUTE ON FUNCTION spSaveArticleReferencePreview(
	pArticleId bigint,	
	pInstanceId bigint,
	pPreview varchar
) TO iusrpmt;
