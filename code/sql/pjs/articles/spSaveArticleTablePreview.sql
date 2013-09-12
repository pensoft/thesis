DROP TYPE ret_spSaveArticleTablePreview CASCADE;
CREATE TYPE ret_spSaveArticleTablePreview AS (
	cache_id bigint
);

CREATE OR REPLACE FUNCTION spSaveArticleTablePreview(
	pArticleId bigint,	
	pInstanceId bigint,
	pPreview varchar
)
  RETURNS ret_spSaveArticleTablePreview AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleTablePreview;		
		lElementCacheTypeId int = 5;
		lElementMetricTypeId int = 5;
		lArticleElementId bigint;
		lCacheId bigint;
		lDisplayLabel varchar;
		lTableNumberFieldId int = 489;
	BEGIN				
		SELECT INTO lArticleElementId, lCacheId, lDisplayLabel
			id, cache_id, display_label
		FROM pjs.article_tables 
		WHERE article_id = pArticleId AND instance_id = pInstanceId;
		
		IF lArticleElementId IS NULL THEN
			INSERT INTO pjs.article_tables(article_id, instance_id)
				VALUES (pArticleId, pInstanceId);
			lArticleElementId = currval('pjs.article_tables_id_seq'::regclass);
		END IF;	
		
		IF coalesce(lDisplayLabel, '') = '' THEN
			SELECT INTO lDisplayLabel
				fv.value_int::varchar
			FROM pwt.instance_field_values fv					
			WHERE fv.instance_id = pInstanceId AND fv.field_id = lTableNumberFieldId;
			
			UPDATE pjs.article_tables SET
				display_label = lDisplayLabel
			WHERE id = lArticleElementId;
		END IF;
		
		IF lCacheId IS NULL THEN
			INSERT INTO pjs.article_cached_items(cached_val, item_type, article_id)
				VALUES (pPreview, lElementCacheTypeId, pArticleId);
				
			lCacheId = currval('pjs.article_cached_items_id_seq'::regclass);
			
			UPDATE pjs.article_tables SET
				cache_id = lCacheId
			WHERE id = lArticleElementId;
		ELSE
			UPDATE pjs.article_cached_items SET
				cached_val = pPreview,
				lastmoddate = now()
			WHERE id = lCacheId;
		END IF;
		
		PERFORM spCreateArticleMetric(lArticleElementId, lElementMetricTypeId);
		
		lRes.cache_id = lCacheId;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveArticleTablePreview(
	pArticleId bigint,	
	pInstanceId bigint,
	pPreview varchar
) TO iusrpmt;
