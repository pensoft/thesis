DROP TYPE ret_spSaveArticleXml CASCADE;
CREATE TYPE ret_spSaveArticleXml AS (
	cache_id bigint
);

CREATE OR REPLACE FUNCTION spSaveArticleXml(
	pArticleId bigint,
	pPreview varchar
)
  RETURNS ret_spSaveArticleXml AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleXml;		
		lElementCacheTypeId int = 1;	
		lElementMetricTypeId int = 3;	
		lCacheId bigint;
	BEGIN				
		SELECT INTO lCacheId
			xml_cache_id
		FROM pjs.articles
		WHERE id = pArticleId;		
		
		IF lCacheId IS NULL THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type, article_id)
				VALUES (pPreview, lElementCacheTypeId, pArticleId);
				
			lCacheId = currval('pjs.article_cached_items_id_seq'::regclass);
			
			UPDATE pjs.articles SET
				xml_cache_id = lCacheId
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

GRANT EXECUTE ON FUNCTION spSaveArticleXml(
	pArticleId bigint,	
	pPreview varchar
) TO iusrpmt;
