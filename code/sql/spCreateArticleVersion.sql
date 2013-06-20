DROP TYPE ret_spCreateArticleVersion CASCADE;
CREATE TYPE ret_spCreateArticleVersion AS (
	id int,
	article_id int,
	version int
);

CREATE OR REPLACE FUNCTION spCreateArticleVersion(	
	pArticleId int	
)
  RETURNS ret_spCreateArticleVersion AS
$BODY$
DECLARE
lRes ret_spCreateArticleVersion;
lCurTime timestamp;
lId int;
lVersionNumber int;
lXml text;
BEGIN
SELECT INTO lVersionNumber max(version) + 1 FROM article_versions WHERE article_id = pArticleId;
lVersionNumber = coalesce(lVersionNumber, 1);
SELECT INTO lXml xml_content FROM articles WHERE id = pArticleId;
INSERT INTO article_versions(article_id, version, xml_content) VALUES ( pArticleId, lVersionNumber, lXml);
lId = currval('article_versions_id_seq');

SELECT INTO lRes lId, pArticleId, lVersionNumber;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spCreateArticleVersion(
	pArticleId int	
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spCreateArticleVersion(
	pArticleId int	
) TO postgres84;
GRANT EXECUTE ON FUNCTION spCreateArticleVersion(
	pArticleId int	
) TO iusrpmt;
