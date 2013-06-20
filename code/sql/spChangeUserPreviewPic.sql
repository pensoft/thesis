DROP TYPE IF EXISTS ret_spChangeUserPreviewPic CASCADE;
CREATE TYPE ret_spChangeUserPreviewPic AS (	
	result int
);

CREATE OR REPLACE FUNCTION spChangeUserPreviewPic(
	pUID int,
	pPicId int
)
  RETURNS ret_spChangeUserPreviewPic AS
$BODY$
	DECLARE
		lUid int;
		lResult ret_spChangeUserPreviewPic;
	BEGIN
		UPDATE usr SET
			photo_id = pPicId
		WHERE id = pUID;
		
		lResult.result = 1;
		RETURN lResult;
	END;
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spChangeUserPreviewPic(
	pUID int,
	pPicId int
) TO iusrpmt;
