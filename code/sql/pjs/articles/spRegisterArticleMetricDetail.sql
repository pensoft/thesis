DROP TYPE IF EXISTS ret_spRegisterArticleMetricDetail CASCADE;
CREATE TYPE ret_spRegisterArticleMetricDetail AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spRegisterArticleMetricDetail(
	pItemId bigint,
	pItemType int,
	pDetailType int,
	pIpAddr inet
)
  RETURNS ret_spRegisterArticleMetricDetail AS
$BODY$
	DECLARE		
		lRes ret_spRegisterArticleMetricDetail;	
		lMetricId bigint;
		lViewDetailTypeId int = 1;
		lDownloadDetailTypeId int = 2;
		lDetailIsUnique int = 0;
	BEGIN				
		SELECT INTO lMetricId
			id
		FROM pjs.article_metrics
		WHERE item_id = pItemId AND item_type = pItemType;		
		
		IF lMetricId IS NOT NULL THEN
			IF NOT EXISTS (
				SELECT * 
				FROM pjs.article_metrics_details
				WHERE metric_id = lMetricId AND detail_type = pDetailType AND ip = pIpAddr
			) THEN
				lDetailIsUnique = 1;
			END IF;
			IF pDetailType = lViewDetailTypeId THEN
				UPDATE pjs.article_metrics SET
					view_cnt = view_cnt + 1,
					view_unique_cnt = view_unique_cnt + lDetailIsUnique
				WHERE id = lMetricId;
			ELSEIF pDetailType = lDownloadDetailTypeId THEN
				UPDATE pjs.article_metrics SET
					download_cnt = download_cnt + 1,
					download_unique_cnt = download_unique_cnt + lDetailIsUnique
				WHERE id = lMetricId;
			END IF;
			
			INSERT INTO pjs.article_metrics_details(metric_id, detail_type, ip)
				VALUES (lMetricId, pDetailType, pIpAddr);
				
			lRes.id = currval('pjs.article_metrics_details_id_seq'::regclass);						
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spRegisterArticleMetricDetail(
	pItemId bigint,
	pItemType int,
	pDetailType int,
	pIpAddr inet
) TO iusrpmt;
