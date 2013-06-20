-- Function: pwt.spmovefigure(integer, integer, integer, integer, integer)

-- DROP FUNCTION pwt.spmovefigure(integer, integer, integer, integer, integer);

CREATE OR REPLACE FUNCTION pwt.spmovefigure(pdirection integer, pdocid integer, pphotoid integer, pposition integer, pplateflag integer)
  RETURNS pwt.ret_spmovefigure AS
$BODY$
DECLARE
	DECLARE lRes pwt.ret_spmovefigure;
	lPosition int;
	lMinPosition int;
	lMaxPosition int;
	lUpdatedElemId bigint;
	lPlateID int;
	lArrPlateObjs bigint[];
	lRec record;
BEGIN
	lRes.result := 0;
	lPosition := 0;
	IF pPlateFlag > 0 THEN
		SELECT INTO lPosition max(move_position) FROM pwt.media WHERE plate_id = pPhotoId AND document_id = pDocId;
	ELSE
		SELECT INTO lPosition move_position FROM pwt.media WHERE id = pPhotoId AND document_id = pDocId;
	END IF;
	
	SELECT INTO lMinPosition min(move_position) FROM pwt.media WHERE document_id = pDocId;
	SELECT INTO lMaxPosition max(move_position) FROM pwt.media WHERE document_id = pDocId;
	lRes.max_position := lMaxPosition;
	lRes.min_position := lMinPosition;
	lRes.curr_position := lPosition;
	IF lPosition IS NOT NULL THEN
		--RAISE EXCEPTION 'lPosition = % ',lPosition;
		IF (pDirection = 1 AND lPosition > lMinPosition) THEN -- MoveUP
			SELECT INTO lUpdatedElemId, lPlateID id, plate_id FROM pwt.media WHERE move_position = lPosition - 1 AND document_id = pDocId;
			IF(lPlateID IS NULL) THEN -- if it's not a plate then update the citation
				UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lUpdatedElemId = ANY(object_ids);
			ELSE -- if it's plate, we have to update citation and check for all elements in the plate
				FOR lRec IN SELECT id FROM pwt.media WHERE plate_id = lPlateID AND document_id = pDocId
				LOOP
					lArrPlateObjs = lArrPlateObjs || lRec.id;
				END LOOP;
				
				UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lArrPlateObjs @> object_ids;
				--RAISE EXCEPTION 'lArrPlateObjs: %', lArrPlateObjs;
			END IF;
			
			UPDATE pwt.media SET move_position = lPosition WHERE move_position = lPosition - 1 AND document_id = pDocId; --Update prev element
			IF pPlateFlag > 0 THEN
				SELECT INTO lUpdatedElemId id FROM pwt.media WHERE plate_id = pPhotoId AND document_id = pDocId;
				UPDATE pwt.media SET move_position = lPosition - 1 WHERE plate_id = pPhotoId AND document_id = pDocId; --Update current element
				UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lUpdatedElemId = ANY(object_ids);
			ELSE
				SELECT INTO lUpdatedElemId id FROM pwt.media WHERE id = pPhotoId AND document_id = pDocId;
				UPDATE pwt.media SET move_position = lPosition - 1 WHERE id = pPhotoId AND document_id = pDocId; --Update current element
				UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lUpdatedElemId = ANY(object_ids);
			END IF;
			lRes.new_position := lPosition - 1;
		ELSIF (pDirection = 2 AND lPosition < lMaxPosition) THEN -- MoveDown
			SELECT INTO lUpdatedElemId, lPlateID id, plate_id FROM pwt.media WHERE move_position = lPosition + 1 AND document_id = pDocId;
			
			IF(lPlateID IS NULL) THEN -- if it's not a plate then update the citation dirty flag
				UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lUpdatedElemId = ANY(object_ids);
			ELSE -- if it's plate, we have to update citation dirty flag and check for all elements in the plate
				FOR lRec IN SELECT id FROM pwt.media WHERE plate_id = lPlateID AND document_id = pDocId
				LOOP
					lArrPlateObjs = lArrPlateObjs || lRec.id;
				END LOOP;
				
				UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lArrPlateObjs @> object_ids;
				--RAISE EXCEPTION 'lArrPlateObjs: %', lArrPlateObjs;
			END IF;
			--UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lUpdatedElemId = ANY(object_ids);
			
			UPDATE pwt.media SET move_position = lPosition WHERE move_position = lPosition + 1 AND document_id = pDocId; --Update next element
			
			IF pPlateFlag > 0 THEN
				SELECT INTO lUpdatedElemId id FROM pwt.media WHERE plate_id = pPhotoId AND document_id = pDocId;
				UPDATE pwt.media SET move_position = lPosition + 1 WHERE plate_id = pPhotoId AND document_id = pDocId; --Update current element
				UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lUpdatedElemId = ANY(object_ids);
			ELSE
				SELECT INTO lUpdatedElemId id FROM pwt.media WHERE id = pPhotoId AND document_id = pDocId AND plate_id IS NULL;
				UPDATE pwt.media SET move_position = lPosition + 1 WHERE id = pPhotoId AND document_id = pDocId AND plate_id IS NULL; --Update current element
				UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lUpdatedElemId = ANY(object_ids);
			END IF;
			lRes.new_position := lPosition + 1;
		END IF;
		lRes.result := 1;
	END IF;
	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spmovefigure(integer, integer, integer, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spmovefigure(integer, integer, integer, integer, integer) TO public;
GRANT EXECUTE ON FUNCTION pwt.spmovefigure(integer, integer, integer, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spmovefigure(integer, integer, integer, integer, integer) TO iusrpmt;
