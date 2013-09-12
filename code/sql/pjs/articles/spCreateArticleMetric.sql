DROP TYPE IF EXISTS ret_spCreateArticleMetric CASCADE;
CREATE TYPE ret_spCreateArticleMetric AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spCreateArticleMetric(
	pItemId bigint,
	pItemType int
)
  RETURNS ret_spCreateArticleMetric AS
$BODY$
	DECLARE		
		lRes ret_spCreateArticleMetric;				
	BEGIN				
		SELECT INTO lRes.id
			id
		FROM pjs.article_metrics
		WHERE item_id = pItemId AND item_type = pItemType;		
		
		IF lRes.id IS NULL THEN
			INSERT INTO pjs.article_metrics(item_id, item_type)
				VALUES (pItemId, pItemType);
				
			lRes.id = currval('pjs.article_metrics_id_seq'::regclass);			
			
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateArticleMetric(
	pItemId bigint,	
	pItemType int
) TO iusrpmt;
