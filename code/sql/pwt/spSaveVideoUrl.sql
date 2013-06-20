DROP TYPE IF EXISTS pwt.ret_spSaveVideoUrl;
CREATE TYPE pwt.ret_spSaveVideoUrl AS (
	video_id int
);

DROP FUNCTION IF EXISTS pwt.spSaveVideoUrl(poper integer, pid integer, pdocid integer, plink character varying, ptitle character varying, pCreateUid integer, pftype integer, ppposition integer);
CREATE OR REPLACE FUNCTION pwt.spSaveVideoUrl(poper integer, pid integer, pdocid integer, plink character varying, ptitle character varying, pCreateUid integer, pftype integer, ppposition integer)
 RETURNS pwt.ret_spSaveVideoUrl AS
$BODY$
DECLARE
	lId pwt.ret_spSaveVideoUrl;
	lMaxPosition int;
BEGIN
	
	IF (pOper = 1) THEN -- INSERT
		
		SELECT INTO lMaxPosition max(move_position) FROM pwt.media WHERE document_id = pDocId;
		IF lMaxPosition IS NULL THEN
			lMaxPosition := 0;
		END IF;
		INSERT INTO pwt.media (document_id, link, title, description, usr_id, ftype, mimetype, move_position) 
				VALUES (pDocId, plink, ptitle, ptitle, pCreateUid, pftype, 'video/x-msvideo', lMaxPosition + 1);
			lId.video_id := currval('public.stories_guid_seq');

	ELSIF pOper = 2 THEN -- UPDATE
		UPDATE pwt.media SET 
					link = plink,
					description = pTitle,
					title = pTitle,
					lastmod = CURRENT_TIMESTAMP
				WHERE id = pId;			
		lId.video_id := pId;
	ELSIF pOper = 3 THEN -- DELETE
		DELETE FROM pwt.media WHERE id = pId;
	END IF;

	RETURN lId;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spSaveVideoUrl(integer, integer, integer, character varying, character varying, integer, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spSaveVideoUrl(integer, integer, integer, character varying, character varying, integer, integer, integer) TO public;
GRANT EXECUTE ON FUNCTION pwt.spSaveVideoUrl(integer, integer, integer, character varying, character varying, integer, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spSaveVideoUrl(integer, integer, integer, character varying, character varying, integer, integer, integer) TO iusrpmt;
