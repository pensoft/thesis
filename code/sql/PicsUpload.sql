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

