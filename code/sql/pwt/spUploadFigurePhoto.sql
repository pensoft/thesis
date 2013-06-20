-- Function: pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer)

-- DROP FUNCTION pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer);

CREATE OR REPLACE FUNCTION pwt.spuploadfigurephoto(poper integer, pid integer, pdocid integer, pplateid integer, ptitle character varying, pdesc character varying, pcreateuid integer, pfnupl character varying, pposition integer, pplateval integer)
  RETURNS pwt.ret_spuploadfigurephoto AS
$BODY$
DECLARE
	DECLARE lRes pwt.ret_spuploadfigurephoto;
	lPlate int;
	lMaxPosition int;
BEGIN
	
	IF (pOper = 1) THEN -- INSERT
		IF pPlateVal > 0 THEN
			SELECT INTO lPlate id
			FROM pwt.plates
			WHERE document_id = pDocId AND id = pPlateId;
			IF lPlate > 0 THEN
				SELECT INTO lMaxPosition max(move_position) FROM pwt.media WHERE document_id = pDocId AND plate_id = lPlate;
				IF lMaxPosition IS NULL THEN
					lMaxPosition := 1;
				END IF;
				INSERT INTO pwt.media (document_id, plate_id, title, description, usr_id, original_name, mimetype, position, move_position) 
				VALUES (pDocId, lPlate, pTitle, pDesc, pCreateUid, pFnupl, 'image/jpeg', pPosition, lMaxPosition);
				lRes.photo_id := currval('public.stories_guid_seq');
				lRes.plate_id := lPlate;
			ELSE
				INSERT INTO pwt.plates (document_id, title, description, format_type, createdate, lastmod, usr_id)
					VALUES (pDocId, pDesc, pDesc, pPlateVal, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, pCreateUid);
				lRes.plate_id := currval('pwt.plates_id_seq');
				SELECT INTO lMaxPosition max(move_position) FROM pwt.media WHERE document_id = pDocId;
				IF lMaxPosition IS NULL THEN
					lMaxPosition := 0;
				END IF;	
				INSERT INTO pwt.media (document_id, plate_id, title, description, usr_id, original_name, mimetype, position, move_position) 
					VALUES (pDocId, lRes.plate_id, pTitle, pDesc, pCreateUid, pFnupl, 'image/jpeg', pPosition, lMaxPosition + 1);
				lRes.photo_id := currval('public.stories_guid_seq');
			END IF;
		ELSE
			SELECT INTO lMaxPosition max(move_position) FROM pwt.media WHERE document_id = pDocId;
			IF lMaxPosition IS NULL THEN
				lMaxPosition := 0;
			END IF;
			IF coalesce(pPlateId, 0) > 0 THEN
				lPlate = pPlateId;
			END IF;
			INSERT INTO pwt.media (document_id, plate_id, title, description, usr_id, original_name, mimetype, move_position) 
					VALUES (pDocId, lPlate, pTitle, pDesc, pCreateUid, pFnupl, 'image/jpeg', lMaxPosition + 1);
				lRes.photo_id := currval('public.stories_guid_seq');
		END IF;
	ELSIF pOper = 2 THEN -- UPDATE
		UPDATE pwt.media SET 
					title = pTitle, 
					description = pDesc,
					original_name = pFnupl,
					lastmod = CURRENT_TIMESTAMP
				WHERE id = pId;			
		lRes.photo_id := pId;
	ELSIF pOper = 3 THEN -- DELETE
		DELETE FROM pwt.media WHERE id = pId;
	END IF;

	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer) TO public;
GRANT EXECUTE ON FUNCTION pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer) TO pensoft;
