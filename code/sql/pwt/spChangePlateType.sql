DROP TYPE ret_spChangePlateType CASCADE;
CREATE TYPE ret_spChangePlateType AS (
	result int
);

CREATE OR REPLACE FUNCTION spChangePlateType(
	pPlateInstanceId bigint, 
	pPlateType int,
	pUid integer
)
  RETURNS ret_spChangePlateType AS
$BODY$
DECLARE
	lRes ret_spChangePlateType;
	lPlateTypeFieldId int = 485;	
	lPlateCreationRuleId int = 12;
	lPlateWrapperObjectId bigint = 235;
	lPlateWrapperInstanceId bigint;
	lPlateTypeDataSrcId int;	
	lCheck boolean;
	lRecord record;
	lPlateObjectId int;
BEGIN 
	lRes.result = 1;
	
	SELECT INTO lPlateTypeDataSrcId
		data_src_id
	FROM  pwt.instance_field_values 
	WHERE instance_id = pPlateInstanceId AND field_id = lPlateTypeFieldId;
	
	SELECT INTO lCheck
		result
	FROM spCheckIfIdIsInDataSrc(pPlateType, lPlateTypeDataSrcId);
	
	IF coalesce(lCheck, false) <> true THEN
		RAISE EXCEPTION 'pwt.wrongPlateType';
	END IF;	
	
	SELECT INTO lPlateWrapperInstanceId 
		id
	FROM pwt.document_object_instances
	WHERE parent_id = pPlateInstanceId AND object_id = lPlateWrapperObjectId;
	
	IF coalesce(lPlateWrapperInstanceId, 0) = 0 THEN
		RAISE EXCEPTION 'pwt.wrongPlateStructure';
	END IF;	
	
	UPDATE pwt.instance_field_values SET
		value_int = pPlateType
	WHERE instance_id = pPlateInstanceId AND field_id = lPlateTypeFieldId;
	
	SELECT INTO lPlateObjectId  
		result 
	FROM spGetCustomCreateObject(lPlateCreationRuleId, ARRAY[pPlateType]);
	RAISE NOTICE 'PlateObj %', lPlateCreationRuleId;
	IF EXISTS (SELECT * 
		FROM pwt.document_object_instances
		WHERE parent_id = lPlateWrapperInstanceId AND object_id = lPlateObjectId
	) THEN
		RETURN lRes;
	END IF;

	-- Remove all the other instances(i.e. plate, video, image)
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
	
	PERFORM spCreateNewInstance(lPlateWrapperInstanceId, lPlateObjectId, pUid);
	
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spChangePlateType(
	pPlateInstanceId bigint, 
	pPlateType int,
	pUid integer
) TO iusrpmt;
