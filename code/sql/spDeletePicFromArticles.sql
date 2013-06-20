DROP TYPE ret_spDeletePicFromArticles CASCADE;
CREATE TYPE ret_spDeletePicFromArticles AS (
	result int
);

CREATE OR REPLACE FUNCTION spDeletePicFromArticles(
	pPhotoId int,
	pPhotoPropid int
)
  RETURNS ret_spDeletePicFromArticles AS
$BODY$
DECLARE
	lRes ret_spDeletePicFromArticles;	
BEGIN
	lRes.result = 1;
	
	DELETE FROM storyproperties WHERE valint = pPhotoId AND propid = pPhotoPropid;
	
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spDeletePicFromArticles(
	pPhotoId int,
	pPhotoPropid int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spDeletePicFromArticles(
	pPhotoId int,
	pPhotoPropid int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spDeletePicFromArticles(
	pPhotoId int,
	pPhotoPropid int
) TO iusrpmt;
