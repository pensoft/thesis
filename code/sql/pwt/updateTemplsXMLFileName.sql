CREATE OR REPLACE FUNCTION pwt.updateTemplsXMLFileName()
  RETURNS int AS
$BODY$
	DECLARE
		lRecord record;
	BEGIN
	
		FOR lRecord IN
			SELECT
				object_id, 
				xml_file_name 
			from pwt.template_objects 
			where xml_file_name is not null 
			GROUP BY object_id, xml_file_name 
		LOOP
			UPDATE pwt.template_objects SET xml_file_name = lRecord.xml_file_name WHERE object_id = lRecord.object_id;
		END LOOP;

		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.updateTemplsXMLFileName() TO iusrpmt;
