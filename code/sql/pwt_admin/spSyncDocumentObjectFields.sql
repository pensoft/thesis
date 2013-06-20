DROP TYPE ret_spSyncDocumentObjectFields CASCADE;
CREATE TYPE ret_spSyncDocumentObjectFields AS (
	result int
);

CREATE OR REPLACE FUNCTION spSyncDocumentObjectFields(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int
)
  RETURNS ret_spSyncDocumentObjectFields AS
$BODY$
DECLARE
	lRes ret_spSyncDocumentObjectFields;
	--lSid int;
	lRecord record;
BEGIN
	--Delete all fields which have been removed
	DELETE FROM pwt.instance_field_values v
	USING pwt.document_object_instances i	
	WHERE v.instance_id = i.id AND i.object_id = pObjectId AND i.document_id = pDocumentId 
		AND v.field_id NOT IN (
			SELECT field_id 
			FROM pwt.object_fields 
			WHERE object_id = i.object_id
		);
	
	--Insert the new fields
	INSERT INTO pwt.instance_field_values(instance_id, field_id, document_id, 
			value_str, value_int, value_arr_int, value_arr_str, value_date, value_arr_date, is_read_only, data_src_id) 
	SELECT i.id, of.field_id, i.document_id,
			dv.value_str, dv.value_int, dv.value_arr_int, dv.value_arr_str, dv.value_date, dv.value_arr_date, of.is_read_only, of.data_src_id
	FROM pwt.document_object_instances i
	JOIN pwt.object_fields of ON of.object_id = i.object_id
	LEFT JOIN pwt.field_default_values dv ON dv.id = of.default_value_id
	LEFT JOIN pwt.instance_field_values v ON v.field_id = of.field_id AND v.instance_id = i.id	
	WHERE i.object_id = pObjectId AND v.field_id IS NULL AND i.document_id = pDocumentId;

	lRes.result = 1;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSyncDocumentObjectFields(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int
) TO iusrpmt;
