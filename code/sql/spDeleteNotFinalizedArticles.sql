DROP TYPE ret_spDeleteNotFinalizedArticles CASCADE;
CREATE TYPE ret_spDeleteNotFinalizedArticles AS (
	result int
);

/**
	Трие нефинализираните статии, чиито id-та са в интервала pStartArticleId и pEndArticleId
*/
CREATE OR REPLACE FUNCTION spDeleteNotFinalizedArticles(
	pStartArticleId int,
	pEndArticleId int
)
  RETURNS ret_spDeleteNotFinalizedArticles AS
$BODY$
DECLARE
	lRes ret_spDeleteNotFinalizedArticles;
	lArticleId int;
BEGIN
	IF pStartArticleId > pEndArticleId THEN
		RAISE EXCEPTION 'articles.startIdIsGreaterThanEndId';
	END IF;
	FOR lArticleId IN 
		SELECT id FROM articles WHERE is_finalized = 0 AND id >= pStartArticleId AND id <= pEndArticleId
	LOOP
		PERFORM spArticlesData(3, lArticleId, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
	END LOOP;	
	
	lRes.result = 1;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spDeleteNotFinalizedArticles(
	pStartArticleId int,
	pEndArticleId int
) TO iusrpmt;
