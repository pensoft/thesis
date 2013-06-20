DROP TYPE IF EXISTS pwt.ret_spGetObjectXMLTemplateFileName CASCADE;
CREATE TYPE pwt.ret_spGetObjectXMLTemplateFileName AS (
	result varchar
);

CREATE OR REPLACE FUNCTION pwt.spGetObjectXMLTemplateFileName(
	pObjectId bigint,
	pTemplateId int
)
  RETURNS pwt.ret_spGetObjectXMLTemplateFileName AS
$BODY$
	DECLARE
		lRes pwt.ret_spGetObjectXMLTemplateFileName;	
				
		
	BEGIN
		
		IF (pObjectId IS NOT NULL) THEN
			SELECT INTO lRes.result 
				xml_file_name 
			FROM pwt.template_objects 
			WHERE object_id = pObjectId AND template_id = pTemplateId;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spGetObjectXMLTemplateFileName(
	pObjectId bigint,
	pTemplateId int
) TO iusrpmt;
