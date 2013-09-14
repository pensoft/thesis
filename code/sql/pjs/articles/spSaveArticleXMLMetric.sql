DROP TYPE ret_spSaveArticleXMLMetric CASCADE;
CREATE TYPE ret_spSaveArticleXMLMetric AS (
	result int
);

CREATE OR REPLACE FUNCTION spSaveArticleXMLMetric(
	pArticleId bigint
)
  RETURNS ret_spSaveArticleXMLMetric AS
$BODY$
	DECLARE		
		lRes ret_spSaveArticleXMLMetric;				
		lElementMetricTypeId int = 3;			
	BEGIN				
		
		PERFORM spCreateArticleMetric(pArticleId, lElementMetricTypeId);
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSaveArticleXMLMetric(
	pArticleId bigint
) TO iusrpmt;
