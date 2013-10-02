DROP TYPE ret_spSyncMaterialFields CASCADE;
CREATE TYPE ret_spSyncMaterialFields AS (
	result int
);

CREATE OR REPLACE FUNCTION spSyncMaterialFields(
	pSpecificFieldsInstanceId bigint, -- InstanceId на Primary/Extended специфичния инстанс
	pUid int
)
  RETURNS ret_spSyncMaterialFields AS
$BODY$
	DECLARE
		lRes ret_spSyncMaterialFields;	
		
		lMaterialInstanceId bigint;
		lMaterialObjectId bigint;
	BEGIN
		lRes.result = 1;
		lMaterialObjectId = 37;
		
		-- Взимаме ид-то на инстанса на материала, който е парент на специфичния подаден инстанс
		SELECT INTO lMaterialInstanceId i.id 
		FROM pwt.document_object_instances i
		JOIN pwt.document_object_instances i1 ON i1.id = pSpecificFieldsInstanceId AND substring(i1.pos, 1, char_length(i.pos)) = i.pos AND i1.document_id = i.document_id
		WHERE i.object_id = lMaterialObjectId;
		
		-- RAISE NOTICE 'Material id %', lMaterialInstanceId;
		IF lMaterialInstanceId IS NULL THEN
			return lRes;
		END IF;
		
		-- След това ъпдейтваме всички полета надолу в йерархията, които съвпадат с полета на специфичния инстанс
		UPDATE pwt.instance_field_values v SET
			value_int = v1.value_int,
			value_str = v1.value_str,
			value_arr_int = v1.value_arr_int,
			value_arr_str = v1.value_arr_str,
			value_date = v1.value_date,
			value_arr_date = v1.value_arr_date
		FROM pwt.instance_field_values v1		
		JOIN pwt.document_object_instances i2 ON i2.id = v1.instance_id
		JOIN pwt.document_object_instances i1 ON i1.id = pSpecificFieldsInstanceId AND substring(i2.pos, 1, char_length(i1.pos)) = i1.pos
			AND i1.document_id = i2.document_id
		JOIN pwt.document_object_instances i ON true
		JOIN pwt.document_object_instances p ON p.id = lMaterialInstanceId
			AND i.document_id = p.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
			AND char_length(p.pos) < char_length(i.pos)
		WHERE i.id = v.instance_id AND v1.field_id = v.field_id
			AND substring(i.pos, 1, char_length(i1.pos)) <> i1.pos;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSyncMaterialFields(	
	pSpecificFieldsInstanceId bigint,
	pUid int
) TO iusrpmt;
