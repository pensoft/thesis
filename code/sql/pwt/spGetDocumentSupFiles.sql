DROP TYPE ret_spGetDocumentSupFiles CASCADE;

CREATE TYPE ret_spGetDocumentSupFiles AS (
	instance_id bigint,
	pos varchar,
	title varchar,
	authors varchar,
	datatype varchar,
	description varchar
);

CREATE OR REPLACE FUNCTION spGetDocumentSupFiles(
	pDocumentId bigint
)
  RETURNS SETOF ret_spGetDocumentSupFiles AS
$BODY$
	DECLARE
		lRes ret_spGetDocumentSupFiles;				
		lSupFileObjectId bigint = 55;
		lTitleFieldId bigint = 214;
		lAuthorsFieldId bigint = 215;
		lDescFieldId bigint = 217;
		lDatatypeFieldId bigint = 216;
	BEGIN		
		
		FOR lRes IN
			SELECT i.id, i.pos, t.value_str, a.value_str, dt.value_str, de.value_str
			FROM pwt.document_object_instances i			
			JOIN pwt.instance_field_values t ON t.instance_id = i.id AND t.field_id = lTitleFieldId
			JOIN pwt.instance_field_values a ON a.instance_id = i.id AND a.field_id = lAuthorsFieldId
			JOIN pwt.instance_field_values de ON de.instance_id = i.id AND de.field_id = lDescFieldId
			JOIN pwt.instance_field_values dt ON dt.instance_id = i.id AND dt.field_id = lDatatypeFieldId
			WHERE i.document_id = pDocumentId AND i.object_id = lSupFileObjectId AND i.is_confirmed = true
		LOOP		
			RETURN NEXT lRes;
		END LOOP;
		
		RETURN;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetDocumentSupFiles(
	pDocumentId bigint
) TO iusrpmt;
