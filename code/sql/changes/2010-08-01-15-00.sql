INSERT INTO propnames(propid, propname) VALUES (20, 'Свързани снимки към article');


DROP FUNCTION PicsUpload(
	pOper int, 
	pID int, 
	pSrc int, 
	pTitle varchar, 
	pFnUpl varchar, 
	pDescr varchar
);

CREATE OR REPLACE FUNCTION PicsUpload(
	pOper int, 
	pID int, 
	pSrc int, 
	pTitle varchar, 
	pFnUpl varchar, 
	pDescr varchar
) RETURNS int AS 
$$
DECLARE
	lRes int;
BEGIN
	lRes := 0;
	
	IF pOper = 1 THEN -- INSERT
		IF coalesce(pID, 0) = 0 THEN
			INSERT INTO photos (lang, title, createuid, description, filenameupl, source, mimetype, lastmod) 
				VALUES ('bg', pTitle, 1, pDescr, pFnUpl, pSrc, 'image/jpeg', now());
			lRes := currval('stories_guid_seq');
		ELSE
			UPDATE photos SET
				title = pTitle, 
				description = pDescr,
				filenameupl = filenameupl, 
				source = pSrc,
				lastmod = now()
			WHERE guid = pID;
			lRes = pID;
		END IF;
	ELSIF pOper = 3 THEN -- DELETE
		DELETE FROM photos WHERE guid = pID;
	END IF;

	RETURN lRes;
END;
$$ LANGUAGE 'plpgsql' SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION PicsUpload(
	pOper int, 
	pID int, 
	pSrc int, 
	pTitle varchar, 
	pFnUpl varchar, 
	pDescr varchar
) TO iusrpmt;

REVOKE ALL ON FUNCTION PicsUpload(
	pOper int, 
	pID int, 
	pSrc int, 
	pTitle varchar, 
	pFnUpl varchar, 
	pDescr varchar
) FROM public;

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
) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spGetArticleImages(
	pArticleId int,
	pImageNames varchar[],
	pImageLastMods timestamp[],
	pPropid int
) TO postgres;
GRANT EXECUTE ON FUNCTION spGetArticleImages(
	pArticleId int,
	pImageNames varchar[],
	pImageLastMods timestamp[],
	pPropid int
) TO iusrpmt;

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
) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spAddPhotoToArticle(
	pArticleId int,
	pPicId int,
	pPropid int
) TO postgres;
GRANT EXECUTE ON FUNCTION spAddPhotoToArticle(
	pArticleId int,
	pPicId int,
	pPropid int
) TO iusrpmt;
