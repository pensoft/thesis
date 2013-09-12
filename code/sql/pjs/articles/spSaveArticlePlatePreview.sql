DROP TYPE ret_spSaveArticlePlatePreview CASCADE;
CREATE TYPE ret_spSaveArticlePlatePreview AS (
	cache_id bigint
);

CREATE OR REPLACE FUNCTION spSaveArticlePlatePreview(
	pArticleId bigint,	
	pInstanceId bigint,
	pPreview varchar
)
  RETURNS ret_spSaveArticlePlatePreview AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticlePlatePreview;		
		lElementCacheTypeId int = 4;
		lElementMetricTypeId int = 4;
		lArticleElementId bigint;
		lCacheId bigint;
		lPicId bigint;
		lPicIdFieldId int = 484;
		lPicNumberFieldId int = 489;
		lFigureObjectId int = 221;
		lParentFigureInstanceId bigint;
		lDisplayLabel varchar;
		lPlateInstanceId bigint;
		lPlatePartLetter varchar;
	BEGIN				
		SELECT INTO lArticleElementId, lCacheId, lPicId, lDisplayLabel, lPlateInstanceId
			id, cache_id, pic_id, display_label, plate_instance_id
		FROM pjs.article_figures 
		WHERE article_id = pArticleId AND instance_id = pInstanceId;
		
		IF lArticleElementId IS NULL THEN
			INSERT INTO pjs.article_figures(article_id, instance_id, is_plate)
				VALUES (pArticleId, pInstanceId, true);
			lArticleElementId = currval('pjs.article_figures_id_seq'::regclass);
		END IF;	
		
		IF coalesce(lPicId, 0) = 0 THEN
			SELECT INTO lPicId
				m.id
			FROM pwt.media m 
			JOIN pwt.instance_field_values fv ON fv.value_int = m.id
			JOIN pwt.document_object_instances i ON i.id = fv.instance_id			
			WHERE i.id = pInstanceId AND fv.field_id = lPicIdFieldId;
			
			UPDATE pjs.article_figures SET
				pic_id = lPicId
			WHERE id = lArticleElementId;
		END IF;
		
		IF coalesce(lPlateInstanceId, 0) = 0 THEN
			SELECT INTO lPlateInstanceId
				p.id
			FROM pwt.document_object_instances i 
			JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND substring(i.pos, 1, char_length(p.pos)) = p.pos			
			WHERE i.id = pInstanceId AND p.object_id = lFigureObjectId;
			
			UPDATE pjs.article_figures SET
				plate_instance_id = lPlateInstanceId
			WHERE id = lArticleElementId;
		END IF;
		
		IF coalesce(lDisplayLabel, '') = '' THEN
			lPlatePartLetter = spGetPlatePartLetter(pInstanceId);
			
			SELECT INTO lDisplayLabel
				fv.value_int::varchar || ' ' || lPlatePartLetter
			FROM pwt.instance_field_values fv					
			WHERE fv.instance_id = lPlateInstanceId AND fv.field_id = lPicNumberFieldId;
			
			UPDATE pjs.article_figures SET
				display_label = lDisplayLabel
			WHERE id = lArticleElementId;
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
		
		PERFORM spCreateArticleMetric(lArticleElementId, lElementMetricTypeId);
		
		lRes.cache_id = lCacheId;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveArticlePlatePreview(
	pArticleId bigint,	
	pInstanceId bigint,
	pPreview varchar
) TO iusrpmt;
