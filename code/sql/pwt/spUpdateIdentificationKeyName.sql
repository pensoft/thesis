--DROP TYPE ret_spUpdateIdentificationKeyName CASCADE;
CREATE TYPE ret_spUpdateIdentificationKeyName AS (
	result int
);

CREATE OR REPLACE FUNCTION spUpdateIdentificationKeyName(
	pIdentKeyInstanceId bigint, -- InstanceId на IdentKey нейма
	pUid int
)
  RETURNS ret_spUpdateIdentificationKeyName AS
$BODY$
	DECLARE
		lRes ret_spUpdateIdentificationKeyName;	
		
		lIdentKeyInstanceId bigint;
		
		lIdentKeyTitleFieldId bigint;
		lIdentKeyObjectId bigint;
		
		lName varchar;
		lTempStr varchar;
	BEGIN
		lIdentKeyTitleFieldId = 31;
		lIdentKeyObjectId = 23;
		
		-- Взимаме името
		SELECT INTO lTempStr value_str FROM pwt.instance_field_values WHERE instance_id = pIdentKeyInstanceId AND field_id = lIdentKeyTitleFieldId;
		
		lName = coalesce(lTempStr, '');
		
		-- Стрипваме таговете
		SELECT INTO lName * FROM public.spStripTags(lName);
		
		-- Взимаме ид-то на IdentKey-a
		SELECT INTO lIdentKeyInstanceId i.id 
		FROM pwt.document_object_instances i
		JOIN pwt.document_object_instances n ON n.document_id = i.document_id AND i.pos = substring(n.pos, 1, char_length(i.pos))
		WHERE i.object_id = lIdentKeyObjectId AND n.id = pIdentKeyInstanceId;
		
		-- Ъпдейтваме името му
		UPDATE pwt.document_object_instances SET
			display_name = lName 
		WHERE id = lIdentKeyInstanceId;
		
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spUpdateIdentificationKeyName(	
	pIdentKeyInstanceId bigint,
	pUid int
) TO iusrpmt;
