DROP TYPE ret_spArticle CASCADE;
CREATE TYPE ret_spArticle AS (
	id int,
	xml varchar,
	new_id int
);

CREATE OR REPLACE FUNCTION spArticle(
	pOper int,
	pId int,
	pXml varchar,
	pUID int
)
  RETURNS ret_spArticle AS
$BODY$
DECLARE
lItemName varchar;
lRes ret_spArticle;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Update
	PERFORM spCreateArticleVersion(pId);
	UPDATE articles SET 
		xml_content = pXml, 
		lastmod = CURRENT_TIMESTAMP
	WHERE id = pId;
END IF;

IF pOper = 2 THEN -- Update And Create New
	PERFORM spCreateArticleVersion(pId);
	UPDATE articles SET 
		xml_content = pXml, 
		lastmod = CURRENT_TIMESTAMP
	WHERE id = pId;
	
	SELECT INTO lId id FROM spArticlesData(1, null, pUID, 'Untitled', null,	pXml, null, null, null, null, null, null, null,	null, null, null, null,	null, null, null, null, null, null);
	
END IF;

SELECT INTO lRes id, xml_content, lId FROM articles WHERE id = pId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spArticle(
	pOper int,
	pId int,
	pXml varchar,
	pUID int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spArticle(
	pOper int,
	pId int,
	pXml varchar,
	pUID int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spArticle(
	pOper int,
	pId int,
	pXml varchar,
	pUID int
) TO iusrpmt;
