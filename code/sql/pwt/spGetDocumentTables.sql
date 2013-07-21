DROP TYPE ret_spGetDocumentTables CASCADE;

CREATE TYPE ret_spGetDocumentTables AS (
	instance_id bigint,
	pos varchar,
	fignum int,
	caption text
);

CREATE OR REPLACE FUNCTION spGetDocumentTables(
	pDocumentId bigint
)
  RETURNS SETOF ret_spGetDocumentTables AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentTables;				
		lTableObjectId bigint = 238;
		lTableNumberFieldId bigint = 489;
		lTableCaptionFieldId bigint = 482;		
	BEGIN		
		
		FOR lRes IN
			SELECT i.id, i.pos, t.value_int, a.value_str
			FROM pwt.document_object_instances i			
			JOIN pwt.instance_field_values t ON t.instance_id = i.id AND t.field_id = lTableNumberFieldId
			JOIN pwt.instance_field_values a ON a.instance_id = i.id AND a.field_id = lTableCaptionFieldId			
			WHERE i.document_id = pDocumentId AND i.object_id = lTableObjectId AND i.is_confirmed = true
		LOOP					
			RETURN NEXT lRes;
		END LOOP;
		
		RETURN;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentTables(
	pDocumentId bigint
) TO iusrpmt;
