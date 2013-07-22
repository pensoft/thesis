DROP TYPE ret_spDeletePlateDetails CASCADE;
CREATE TYPE ret_spDeletePlateDetails AS (
	result int
);

CREATE OR REPLACE FUNCTION spDeletePlateDetails(
	pPlateInstanceId bigint,
	pUid integer
)
  RETURNS ret_spDeletePlateDetails AS
$BODY$
DECLARE
	lRes ret_spDeletePlateDetails;
	lPlateType int;
	lPlateWrapperObjectId bigint = 235;
	lPlateCreationRuleId int = 12;
	lPlateTypeFieldId int = 485;	
	lPlateWrapperInstanceId bigint;
	lRecord record;
BEGIN 
	lRes.result = 1;
	
	SELECT INTO lPlateWrapperInstanceId 
		id
	FROM pwt.document_object_instances
	WHERE parent_id = pPlateInstanceId AND object_id = lPlateWrapperObjectId;
	
	IF coalesce(lPlateWrapperInstanceId, 0) = 0 THEN
		RAISE EXCEPTION 'pwt.wrongPlateStructure';
	END IF;	
	
	
	FOR lRecord IN
		SELECT i.id
		FROM pwt.document_object_instances i
		WHERE parent_id = lPlateWrapperInstanceId AND object_id IN (
				SELECT object_id 
				FROM pwt.custom_object_creation_combinations
				WHERE custom_object_creation_id = lPlateCreationRuleId 
			UNION
				SELECT default_object_id 
				FROM pwt.custom_object_creation
				WHERE id = lPlateCreationRuleId 
		)
	LOOP
		PERFORM spRemoveInstance(lRecord.id, pUid);
	END LOOP;
	
	UPDATE pwt.instance_field_values SET
		value_int = null,
		is_read_only = false
	WHERE instance_id = pPlateInstanceId AND field_id = lPlateTypeFieldId;
	
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spDeletePlateDetails(
	pPlateInstanceId bigint,
	pUid integer
) TO iusrpmt;
