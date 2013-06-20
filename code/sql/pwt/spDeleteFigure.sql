-- Function: pwt.spdeletefigure(integer, integer, integer)

-- DROP FUNCTION pwt.spdeletefigure(integer, integer, integer);

CREATE OR REPLACE FUNCTION pwt.spdeletefigure(pdocid integer, pplateid integer, pphotoid integer)
  RETURNS pwt.ret_spdeletefigure AS
$BODY$
DECLARE
	DECLARE 
	lRes pwt.ret_spdeletefigure;
	lPositionsArr int[];
	lPosition int;
	lRecord record;
	lRecord2 record;
	lCitations record;
BEGIN
	lRes.result := 0;
	
	
	IF (pDocId IS NOT NULL) THEN		
		IF (pPhotoId IS NOT NULL) THEN
			SELECT INTO lPosition 
				move_position 
			FROM pwt.media 
			WHERE id = pPhotoId AND document_id = pDocId;
			
			SELECT INTO lRes.curr_position 
				move_position 
			FROM pwt.media 
			WHERE id = pPhotoId AND document_id = pDocId;					
			
			--Update na vsi4ki position-i sled tozi koito triem za da ne se 4upi mesteneto
			FOR lRecord IN 
				SELECT move_position, id 
				FROM pwt.media 
				WHERE document_id = pDocId AND move_position > lPosition 
				ORDER BY move_position
			LOOP
				UPDATE pwt.media SET 
					move_position = move_position - 1 
				WHERE document_id = pDocId AND move_position = lRecord.move_position;
				
				UPDATE pwt.citations SET 
					is_dirty = true 
				WHERE document_id = pDocId AND lRecord.id = ANY(object_ids);
			END LOOP;
			--RAISE EXCEPTION 'Position - %',lPosition;
			--RAISE EXCEPTION 'lPositionsArr - %',lPositionsArr;
		
			DELETE FROM pwt.media 
			WHERE id = pPhotoId AND document_id = pDocId;
			
			<<lCitationsLoop>>
			FOR lCitations IN
				SELECT * 
				FROM pwt.citations 
				WHERE document_id = pDocId AND pPhotoId = ANY(object_ids)
			LOOP
				IF array_upper(lCitations.object_ids, 1) > 1 THEN
					UPDATE pwt.citations SET 
						is_dirty = true, 
						object_ids = array_pop(object_ids, pPhotoId::bigint)
					WHERE lCitations.id = id;
				ELSE 
					DELETE FROM pwt.citations 
					WHERE lCitations.id = id;
				END IF;
			END LOOP lCitationsLoop;
			
			lRes.result := 1;
		END IF;
		IF (pPlateId IS NOT NULL) THEN
			SELECT INTO lPosition max(move_position) FROM pwt.media WHERE plate_id = pPlateId AND document_id = pDocId;
			
			lRes.curr_position = lPosition;			
			
			<<lPlateRelaitedPhotosLoop>>
			FOR lRecord2 IN
				SELECT * FROM pwt.media WHERE plate_id = pPlateId AND document_id = pDocId
			LOOP
				--UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lRecord2.id = ANY(object_ids);
				<<lCitationsLoop>>
				FOR lCitations IN
					SELECT * 
					FROM pwt.citations 
					WHERE document_id = pDocId AND lRecord2.id = ANY(object_ids)
				LOOP
					IF array_upper(lCitations.object_ids, 1) > 1 THEN
						UPDATE pwt.citations SET 
							is_dirty = true, 
							object_ids = array_pop(object_ids, lRecord2.id::bigint)
						WHERE lCitations.id = id;
					ELSE 
						DELETE FROM pwt.citations 
						WHERE lCitations.id = id;
					END IF;
				END LOOP lCitationsLoop;
				
			END LOOP lPlateRelaitedPhotosLoop;
			
			--Update na vsi4ki position-i sled tozi koito triem za da ne se 4upi mesteneto
			FOR lRecord IN 
				SELECT move_position, id FROM pwt.media WHERE document_id = pDocId AND move_position > lPosition ORDER BY move_position
			LOOP
				UPDATE pwt.media SET 
					move_position = move_position - 1 
				WHERE document_id = pDocId AND move_position = lRecord.move_position;
				
				UPDATE pwt.citations SET 
					is_dirty = true 
				WHERE document_id = pDocId AND lRecord.id = ANY(object_ids);
			END LOOP;
			
			DELETE FROM pwt.media 
			WHERE plate_id = pPlateId AND document_id = pDocId;
			
			DELETE FROM pwt.plates 
			WHERE id = pPlateId AND document_id = pDocId;
			
			lRes.result := 1;
		END IF;
		
		SELECT INTO lRes.min_position, lRes.max_position
			min(move_position), max(move_position)
		FROM pwt.media 
		WHERE document_id = pDocId;	
	END IF;

	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spdeletefigure(integer, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spdeletefigure(integer, integer, integer) TO public;
GRANT EXECUTE ON FUNCTION pwt.spdeletefigure(integer, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spdeletefigure(integer, integer, integer) TO iusrpmt;
