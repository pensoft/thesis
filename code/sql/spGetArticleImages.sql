DROP TYPE ret_spGetArticleImages CASCADE;
CREATE TYPE ret_spGetArticleImages AS (
	id int,
	imgname varchar,
	lastmod timestamp,
	is_needed int
);

CREATE OR REPLACE FUNCTION spGetArticleImages(
	pArticleId int,
	pImageNames varchar[],
	pImageLastMods timestamp[],
	pPropid int
)
  RETURNS SETOF ret_spGetArticleImages AS
$BODY$
DECLARE
	lRes ret_spGetArticleImages;
	lImageCount int;
	lIter int;
	lPicName varchar;
	lPicLastMod timestamp;
BEGIN

	lImageCount = array_upper(pImageNames, 1);

	FOR lIter IN 1 .. lImageCount LOOP
		lPicName := pImageNames[lIter];
		lPicLastMod := pImageLastMods[lIter];
		
		SELECT INTO lRes p.guid, p.filenameupl, p.lastmod, CASE WHEN p.lastmod < lPicLastMod THEN 1 ELSE 0 END AS is_needed 
		FROM photos p
		JOIN storyproperties sp ON sp.valint = p.guid AND sp.propid = pPropid
		JOIN articles a ON a.id = sp.guid
		WHERE a.id = pArticleId AND p.filenameupl = lPicName LIMIT 1;
		
		IF lRes.id IS NULL THEN
			lRes.imgname = lPicName;
			lRes.is_needed = 1;
		END IF;
		
		RETURN NEXT lRes;
	END LOOP;
	RETURN;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spGetArticleImages(
	pArticleId int,
	pImageNames varchar[],
	pImageLastMods timestamp[],
	pPropid int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spGetArticleImages(
	pArticleId int,
	pImageNames varchar[],
	pImageLastMods timestamp[],
	pPropid int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spGetArticleImages(
	pArticleId int,
	pImageNames varchar[],
	pImageLastMods timestamp[],
	pPropid int
) TO iusrpmt;
