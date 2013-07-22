DROP TYPE ret_spGetDocumentFigures CASCADE;

CREATE TYPE ret_spGetDocumentFigures AS (
	instance_id bigint,
	pos varchar,
	fignum int,
	is_plate int
);

CREATE OR REPLACE FUNCTION spGetDocumentFigures(
	pDocumentId bigint
)
  RETURNS SETOF ret_spGetDocumentFigures AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentFigures;				
		lFigureObjectId bigint = 221;
		lFigNumberFieldId bigint = 489;
		lFigTypeFieldId bigint = 488;
		lFieldPlateType int = 2;
		lIsPlate int;
	BEGIN		
		
		FOR lRes IN
			SELECT i.id, i.pos, t.value_int, (CASE WHEN a.value_int = lFieldPlateType THEN 1 ELSE 0 END) as is_plate
			FROM pwt.document_object_instances i			
			JOIN pwt.instance_field_values t ON t.instance_id = i.id AND t.field_id = lFigNumberFieldId
			JOIN pwt.instance_field_values a ON a.instance_id = i.id AND a.field_id = lFigTypeFieldId			
			WHERE i.document_id = pDocumentId AND i.object_id = lFigureObjectId AND i.is_confirmed = true
		LOOP					
			RETURN NEXT lRes;
		END LOOP;
		
		RETURN;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentFigures(
	pDocumentId bigint
) TO iusrpmt;
