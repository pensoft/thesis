DROP TYPE ret_spAddPhotoToArticle CASCADE;
CREATE TYPE ret_spAddPhotoToArticle AS (
	res int
);

CREATE OR REPLACE FUNCTION spAddPhotoToArticle(
	pArticleId int,
	pPicId int,
	pPropid int
)
  RETURNS ret_spAddPhotoToArticle AS
$BODY$
DECLARE
	lRes ret_spAddPhotoToArticle;
BEGIN
	lRes.res = 1;
	
	IF NOT EXISTS(SELECT * FROM storyproperties WHERE guid = pArticleId AND propid = pPropid AND valint = pPicId) THEN
		INSERT INTO storyproperties(guid, valint, propid) VALUES(pArticleId, pPicId, pPropid);
	END IF;
	
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spAddPhotoToArticle(
	pArticleId int,
	pPicId int,
	pPropid int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spAddPhotoToArticle(
	pArticleId int,
	pPicId int,
	pPropid int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spAddPhotoToArticle(
	pArticleId int,
	pPicId int,
	pPropid int
) TO iusrpmt;
