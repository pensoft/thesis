-- Function: pwt.spmovetable(integer, integer, integer, integer, integer)

-- DROP FUNCTION pwt.spmovetable(integer, integer, integer, integer, integer);

-- Type: pwt.ret_spmovefigure

DROP TYPE IF EXISTS pwt.ret_spmovetable CASCADE;

CREATE TYPE pwt.ret_spmovetable AS
   (min_position integer,
    max_position integer,
    curr_position integer,
    new_position integer,
    result integer);
ALTER TYPE pwt.ret_spmovetable OWNER TO postgres;

CREATE OR REPLACE FUNCTION pwt.spmovetable(pdirection integer, pdocid integer, pTableid bigint)
  RETURNS pwt.ret_spmovetable AS
$BODY$
DECLARE
	DECLARE lRes pwt.ret_spmovetable;
	lPosition int;
	lMinPosition int;
	lMaxPosition int;
	lUpdatedElemId bigint;
BEGIN
	lRes.result := 0;
	lPosition := 0;

	SELECT INTO lPosition move_position FROM pwt.tables WHERE id = pTableid AND document_id = pDocId;
	
	SELECT INTO lMinPosition min(move_position) FROM pwt.tables WHERE document_id = pDocId;
	SELECT INTO lMaxPosition max(move_position) FROM pwt.tables WHERE document_id = pDocId;
	lRes.max_position := lMaxPosition;
	lRes.min_position := lMinPosition;
	lRes.curr_position := lPosition;
	IF lPosition IS NOT NULL THEN
		--RAISE EXCEPTION 'lPosition = % ',lPosition;
		IF (pDirection = 1 AND lPosition > lMinPosition) THEN -- MoveUP
			
			SELECT INTO lUpdatedElemId id FROM pwt.tables WHERE move_position = lPosition - 1 AND document_id = pDocId;
			UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lUpdatedElemId = ANY(object_ids);
			
			UPDATE pwt.tables SET move_position = lPosition WHERE move_position = lPosition - 1 AND document_id = pDocId; --Update prev element
			
			SELECT INTO lUpdatedElemId id FROM pwt.tables WHERE id = pTableid AND document_id = pDocId;
			UPDATE pwt.tables SET move_position = lPosition - 1 WHERE id = pTableid AND document_id = pDocId; --Update current element
			UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lUpdatedElemId = ANY(object_ids);
			lRes.new_position := lPosition - 1;
			
		ELSIF (pDirection = 2 AND lPosition < lMaxPosition) THEN -- MoveDown
			
			SELECT INTO lUpdatedElemId id FROM pwt.tables WHERE move_position = lPosition + 1 AND document_id = pDocId;
			UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lUpdatedElemId = ANY(object_ids);
			
			UPDATE pwt.tables SET move_position = lPosition WHERE move_position = lPosition + 1 AND document_id = pDocId; --Update next element
			
			SELECT INTO lUpdatedElemId id FROM pwt.tables WHERE id = pTableid AND document_id = pDocId;
			UPDATE pwt.tables SET move_position = lPosition + 1 WHERE id = pTableid AND document_id = pDocId; --Update current element
			UPDATE pwt.citations SET is_dirty = true WHERE document_id = pDocId AND lUpdatedElemId = ANY(object_ids);
			lRes.new_position := lPosition + 1;
			
		END IF;
		lRes.result := 1;
	END IF;
	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spmovetable(integer, integer, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spmovetable(integer, integer, bigint) TO public;
GRANT EXECUTE ON FUNCTION pwt.spmovetable(integer, integer, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spmovetable(integer, integer, bigint) TO iusrpmt;
