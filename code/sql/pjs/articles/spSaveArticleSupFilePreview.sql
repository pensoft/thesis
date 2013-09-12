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
		lElementMetricTypeId int = 6;	
		lArticleElementId bigint;
		lCacheId bigint;
		lFileOriginalName varchar;
		lSupplFileFieldId int = 222;
		lDisplayLabel varchar;
		lSupFileNumberFieldId int = 489;
	BEGIN				
		SELECT INTO lArticleElementId, lCacheId, lFileOriginalName, lDisplayLabel
			id, cache_id, file_name, display_label
		FROM pjs.article_sup_files 
		WHERE article_id = pArticleId AND instance_id = pInstanceId;
		
		IF lArticleElementId IS NULL THEN
			INSERT INTO pjs.article_sup_files(article_id, instance_id)
				VALUES (pArticleId, pInstanceId);
			lArticleElementId = currval('pjs.article_sup_files_id_seq'::regclass);
		END IF;	
		
		IF coalesce(lFileOriginalName, '') = '' THEN
			SELECT INTO lFileOriginalName
				m.original_name
			FROM pwt.media m 
			JOIN pwt.instance_field_values fv ON fv.value_int = m.id
			WHERE fv.instance_id = pInstanceId AND fv.field_id = lSupplFileFieldId;
			
			UPDATE pjs.article_sup_files SET
				file_name = lFileOriginalName
			WHERE id = lArticleElementId;
		END IF;
		
		IF coalesce(lDisplayLabel, '') = '' THEN
			SELECT INTO lDisplayLabel
				(count(*) + 1)::varchar
			FROM pwt.document_object_instances c
			JOIN pwt.document_object_instances i ON i.parent_id = c.parent_id AND i.object_id = c.object_id
			WHERE c.pos < i.pos AND i.id = pInstanceId;
			
			UPDATE pjs.article_sup_files SET
				display_label = lDisplayLabel
			WHERE id = lArticleElementId;
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
		
		PERFORM spCreateArticleMetric(lArticleElementId, lElementMetricTypeId);
		
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
