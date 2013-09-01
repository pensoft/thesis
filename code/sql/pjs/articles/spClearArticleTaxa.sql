DROP TYPE ret_spClearArticleTaxa CASCADE;
CREATE TYPE ret_spClearArticleTaxa AS (
	result int
);


CREATE OR REPLACE FUNCTION spClearArticleTaxa(
	pArticleId bigint
)
  RETURNS ret_spClearArticleTaxa AS
$BODY$
	DECLARE		
		lRes ret_spClearArticleTaxa;				
	BEGIN				
		DELETE 
		FROM pjs.article_taxons
		WHERE article_id = pArticleId;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spClearArticleTaxa(
	pArticleId bigint
) TO iusrpmt;
