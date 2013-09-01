DROP TYPE ret_spClearArticleLocalities CASCADE;
CREATE TYPE ret_spClearArticleLocalities AS (
	result int
);


CREATE OR REPLACE FUNCTION spClearArticleLocalities(
	pArticleId bigint
)
  RETURNS ret_spClearArticleLocalities AS
$BODY$
	DECLARE		
		lRes ret_spClearArticleLocalities;				
	BEGIN	
		DELETE 
		FROM pjs.article_instance_localities il
		USING pjs.article_localities l
		WHERE l.article_id = pArticleId AND il.locality_id = l.id;
				
		DELETE 
		FROM pjs.article_localities
		WHERE article_id = pArticleId;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spClearArticleLocalities(
	pArticleId bigint
) TO iusrpmt;
