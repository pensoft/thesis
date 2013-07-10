DROP TYPE IF EXISTS ret_spGetDocumentFiguresAndTables CASCADE;

CREATE TYPE ret_spGetDocumentFiguresAndTables AS (
	object_id bigint,
	object_type int,
	position int,
	is_plate int,
	plate_id int
);

CREATE OR REPLACE FUNCTION spGetDocumentFiguresAndTables(
	pDocumentId bigint
)
  RETURNS SETOF ret_spGetDocumentFiguresAndTables AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentFiguresAndTables;		
		lRecord record;
	BEGIN		
		FOR lRecord IN
			SELECT id, plate_id, move_position
			FROM pwt.media
			WHERE document_id = pDocumentId
		LOOP
			lRes.object_id = lRecord.id;
			lRes.object_type = 1;
			lRes.position = lRecord.move_position;
			lRes.plate_id = lRecord.plate_id;
			IF (lRes.plate_id IS NOT NULL) THEN
				lRes.is_plate = 1;
			ELSE
				lRes.is_plate = 0;
			END IF;
			RETURN NEXT lRes;
		END LOOP;
		
		FOR lRecord IN
			SELECT id, move_position
			FROM pwt.tables
			WHERE document_id = pDocumentId
		LOOP
			lRes.object_id = lRecord.id;
			lRes.object_type = 2;
			lRes.is_plate = 0;
			lRes.position = lRecord.move_position;
			RETURN NEXT lRes;
		END LOOP;
		
		RETURN;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentFiguresAndTables(
	pDocumentId bigint
) TO iusrpmt;