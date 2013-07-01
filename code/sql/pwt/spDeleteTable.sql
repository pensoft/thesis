DROP TYPE ret_spDeleteTable CASCADE;

CREATE TYPE ret_spDeleteTable AS (
	result int,
	move_position int,
	max_position int,
	min_position int
);


-- Function: pwt.spdeletetable(integer, integer, integer)

-- DROP FUNCTION pwt.spdeletetable(integer, integer, integer);

CREATE OR REPLACE FUNCTION pwt.spdeletetable(pdocid integer, ptableid integer, pusrid integer)
  RETURNS ret_spDeleteTable AS
$BODY$
DECLARE
	DECLARE lRes ret_spDeleteTable;
	lRecord record;
	lCitations record;
	lPosition int;
BEGIN
	lRes.result := 0;
	
	IF (pDocId IS NOT NULL) THEN
		IF (pTableId IS NOT NULL) THEN
		
			SELECT INTO lPosition move_position FROM pwt.tables WHERE id = ptableid AND document_id = pDocId;
		
			DELETE FROM pwt.tables WHERE id = pTableId AND document_id = pDocId;
			
			<<lCitationsLoop>>
			FOR lCitations IN
				SELECT * FROM pwt.citations WHERE document_id = pDocId AND pTableId = ANY(object_ids)
			LOOP
				IF array_upper(lCitations.object_ids, 1) > 1 THEN
					UPDATE pwt.citations SET is_dirty = true, object_ids = array_pop(object_ids, pTableId::bigint)
					WHERE lCitations.id = id;
				ELSE 
					PERFORM spDeleteCitation(lCitations.id, pusrid);
				END IF;
			END LOOP lCitationsLoop;
		
			FOR lRecord IN 
				SELECT move_position, id FROM pwt.tables WHERE document_id = pDocId AND move_position > lPosition ORDER BY move_position
			LOOP
				UPDATE pwt.tables SET move_position = move_position - 1 WHERE document_id = pDocId AND move_position = lRecord.move_position;
				UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lRecord.id = ANY(object_ids);
			END LOOP;
		
			SELECT INTO lRes.max_position, lRes.min_position
				max(move_position), min(move_position)
			FROM pwt.tables WHERE document_id = pDocId;
			
			lRes.result := 1;
			lRes.move_position := lPosition - 1;
		
		END IF;
	END IF;
	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spdeletetable(integer, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spdeletetable(integer, integer, integer) TO public;
GRANT EXECUTE ON FUNCTION pwt.spdeletetable(integer, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spdeletetable(integer, integer, integer) TO iusrpmt;
