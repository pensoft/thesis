DROP FUNCTION spFileUpload(
	pOper int, 
	pID int, 
	pUserId int, 
	pDocumentId int, 
	pTitle varchar, 
	pOriginalName varchar,
	pMimetype varchar
);
CREATE OR REPLACE FUNCTION spFileUpload(
	pOper int, 
	pID int, 
	pUserId int,
	pDocumentId int, 
	pTitle varchar, 
	pOriginalName varchar,
	pMimetype varchar
) RETURNS int AS 
$$
DECLARE
	lRes int;
BEGIN
	lRes := 0;
	
	IF pOper = 1 THEN -- INSERT
		INSERT INTO media (document_id, ftype, title, usr_id, original_name, mimetype) 
			VALUES (pDocumentId, 1, pTitle, pUserId, pOriginalName, pMimetype);
		lRes := currval('media_id_seq');
		
		--UPDATE photos SET imgname = lRes || pExt WHERE guid = lRes;
		
	ELSIF pOper = 3 THEN -- DELETE
		DELETE FROM media WHERE guid = pID;
	END IF;

	RETURN lRes;
END;
$$ LANGUAGE 'plpgsql' SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spFileUpload(
	pOper int, 
	pID int, 
	pUserId int,
	pDocumentId int, 
	pTitle varchar, 
	pOriginalName varchar,
	pMimetype varchar
) TO iusrpmt;
