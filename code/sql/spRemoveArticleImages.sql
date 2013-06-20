DROP TYPE ret_spRemoveArticleImages CASCADE;
CREATE TYPE ret_spRemoveArticleImages AS (
	result int
);

CREATE OR REPLACE FUNCTION spRemoveArticleImages(
	pArticleId int,
	pPhotoPropid int
)
  RETURNS ret_spRemoveArticleImages AS
$BODY$
DECLARE
	lRes ret_spRemoveArticleImages;	
BEGIN
	lRes.result = 1;
	
	DELETE FROM storyproperties WHERE guid = pArticleId AND propid = pPhotoPropid;
	
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spRemoveArticleImages(
	pArticleId int,
	pPhotoPropid int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spRemoveArticleImages(
	pArticleId int,
	pPhotoPropid int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spRemoveArticleImages(
	pArticleId int,
	pPhotoPropid int
) TO iusrpmt;
