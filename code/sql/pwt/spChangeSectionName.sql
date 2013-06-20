CREATE OR REPLACE FUNCTION pwt.spChangeSectionName(
	pSectionInstanceId bigint, -- InstanceId на секцията
	pUid int
)
  RETURNS integer AS
$BODY$
	DECLARE
		lTitleFieldId bigint;
		lName varchar;
	BEGIN
		lTitleFieldId = 413;
		
		-- Взимаме името
		SELECT INTO lName value_str FROM pwt.instance_field_values WHERE instance_id = pSectionInstanceId AND field_id = lTitleFieldId;
		
		IF lName IS NOT NULL THEN
			-- Ъпдейтваме името му
			UPDATE pwt.document_object_instances SET
				display_name = lName 
			WHERE id = pSectionInstanceId;
		ELSE
			SELECT INTO lName dto.display_name 
			FROM pwt.document_object_instances di
			JOIN pwt.document_template_objects dto ON dto.id = di.document_template_object_id
			WHERE di.id = pSectionInstanceId;
			
			-- Ъпдейтваме името му с дефолтната стойност за тази секция
			UPDATE pwt.document_object_instances SET
				display_name = lName 
			WHERE id = pSectionInstanceId;
			-- Ъпдейтваме и полето с дефолтната стойност
			UPDATE pwt.instance_field_values SET value_str = lName WHERE instance_id = pSectionInstanceId AND field_id = lTitleFieldId;
		END IF;	

		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

ALTER FUNCTION pwt.spChangeSectionName(bigint, int) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spChangeSectionName(bigint, int) TO public;
GRANT EXECUTE ON FUNCTION pwt.spChangeSectionName(bigint, int) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spChangeSectionName(bigint, int) TO iusrpmt;