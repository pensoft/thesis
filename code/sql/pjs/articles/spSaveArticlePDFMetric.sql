DROP TYPE ret_spSaveArticlePDFMetric CASCADE;
CREATE TYPE ret_spSaveArticlePDFMetric AS (
	result int
);

CREATE OR REPLACE FUNCTION spSaveArticlePDFMetric(
	pArticleId bigint
)
  RETURNS ret_spSaveArticlePDFMetric AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticlePDFMetric;				
		lElementMetricTypeId int = 2;			
	BEGIN				
		
		PERFORM spCreateArticleMetric(pArticleId, lElementMetricTypeId);
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveArticlePDFMetric(
	pArticleId bigint
) TO iusrpmt;
