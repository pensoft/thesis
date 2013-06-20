DROP FUNCTION spPhotos (
	pOper int,
	pID int,
	pStoryID int,
	pPicID int,
	pTitle varchar,
	pDescr varchar,
	pPlace int,
	pFirstPhoto int,
	pPos int
);

DROP TYPE retspPhotos;

CREATE TYPE retspPhotos AS (
	guid int,
	storyid int,
	title varchar,
	underpic varchar,
	place int,
	pos int,
	firstphoto int
);

CREATE OR REPLACE FUNCTION spPhotos (
	pOper int,
	pID int,
	pStoryID int,
	pPicID int,
	pTitle varchar,
	pDescr varchar,
	pPlace int,
	pFirstPhoto int,
	pPos int
) RETURNS retspPhotos AS
$$
DECLARE
	lResult retspPhotos;
	lPos int;
	rr RECORD;
BEGIN
	
	IF pStoryID IS NULL AND pOper <> 0 THEN 
		RETURN lResult;
	END IF;
	
	IF pOper = 1 THEN
		
		IF pPos IS NULL THEN
			SELECT INTO lPos coalesce((max(valint3) + 1), 1) FROM storyproperties WHERE guid = pStoryID AND propid = 2;
		ELSE 
			
			lPos := pPos + 1;
			FOR rr IN SELECT valint FROM storyproperties 
				WHERE guid = pStoryID AND propid = 2 AND (valint3 >= pPos OR valint3 IS NULL) AND valint <> coalesce(pPicID, pID)
				ORDER BY valint3
			LOOP
				UPDATE storyproperties SET valint3 = lPos WHERE valint = rr.valint;
				lPos := lPos + 1;
			END LOOP;
			
			lPos := pPos;
		
		END IF;
		
		IF pID IS NULL THEN
			-- INSERT
			
			INSERT INTO storyproperties (guid, propid, valint, valint2, valstr, valint3) 
			VALUES (pStoryID, 2, pPicID, pPlace, pDescr, lPos);
			
		ELSE
			--UPDATE
			IF pPicID IS NULL THEN 
				UPDATE storyproperties SET
					valint2 = pPlace,
					valint3 = lPos,
					valstr = pDescr
				WHERE guid = pStoryID 
				AND propid = 2 AND valint = pID;
			ELSE 
				
				DELETE FROM storyproperties WHERE guid = pStoryID 
				AND propid = 2 AND valint = pID;
				
				INSERT INTO storyproperties (guid, propid, valint, valint2, valstr, valint3) 
				VALUES (pStoryID, 2, pPicID, pPlace, pDescr, lPos);
				
			END IF;
			
		END IF;
		
		IF pFirstPhoto IS NOT NULL THEN
			UPDATE stories SET previewpicid = coalesce(pPicID, pID) WHERE guid = pStoryID;
		END IF;
		
	END IF;
	
	IF pOper = 3 THEN 
		SELECT INTO lPos coalesce(valint3, 1) FROM storyproperties WHERE guid = pStoryID 
		AND propid = 2 AND valint = pID;
		
		DELETE FROM storyproperties WHERE guid = pStoryID 
		AND propid = 2 AND valint = pID;
		
		FOR rr IN SELECT valint FROM storyproperties 
			WHERE guid = pStoryID AND propid = 2 AND (valint3 > lPos OR valint3 IS NULL)
			ORDER BY valint3
		LOOP
			UPDATE storyproperties SET valint3 = lPos WHERE valint = rr.valint;
			lPos := lPos + 1;
		END LOOP;
		
		IF EXISTS(SELECT guid FROM stories WHERE guid = pStoryID AND previewpicid = pID) THEN
			UPDATE stories SET previewpicid = NULL WHERE guid = pStoryID; 
		END IF;
		
	END IF;
	
	SELECT INTO lResult.guid, lResult.title
		p.guid, p.title 
	FROM photos p 
	WHERE p.guid = coalesce(pPicID, pID);
	
	SELECT INTO lResult.storyid, lResult.underpic, lResult.place, lResult.pos
		sp.guid, sp.valstr, sp.valint2, sp.valint3 
	FROM storyproperties sp 
	WHERE sp.valint = coalesce(pPicID, pID) AND sp.propid = 2 AND sp.guid = pStoryID; 
	
	RETURN lResult;
END;

$$
LANGUAGE 'plpgsql' SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPhotos (
	pOper int,
	pID int,
	pStoryID int,
	pPicID int,
	pTitle varchar,
	pDescr varchar,
	pPlace int,
	pFirstPhoto int,
	pPos int
) TO iusrpmt;

REVOKE ALL ON FUNCTION spPhotos (
	pOper int,
	pID int,
	pStoryID int,
	pPicID int,
	pTitle varchar,
	pDescr varchar,
	pPlace int,
	pFirstPhoto int,
	pPos int
) FROM public;




