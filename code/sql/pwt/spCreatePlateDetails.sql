DROP TYPE ret_spCreatePlateDetails CASCADE;
CREATE TYPE ret_spCreatePlateDetails AS (
	result int
);

CREATE OR REPLACE FUNCTION spCreatePlateDetails(
	pPlateInstanceId bigint,
	pPlateType int,
	pUid integer
)
  RETURNS ret_spCreatePlateDetails AS
$BODY$
DECLARE
	lRes ret_spCreatePlateDetails;
	lPlateType int;
	lPlateWrapperObjectId bigint = 235;
	lPlateCreationRuleId int = 12;
	lPlateWrapperInstanceId bigint;
	lRecord record;
	lPlateTypeFieldId int = 485;	
BEGIN 
	lRes.result = 1;
	
	IF coalesce(pPlateType, 0) > 0 THEN
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
			RAISE EXCEPTION 'pwt.plateDetailsHaveAlreadyBeenCreated';
		END LOOP;
		PERFORM spChangePlateType(pPlateInstanceId, pPlateType, pUid);
	ELSE 
		RAISE EXCEPTION 'pwt.youHaveToSelectPlateTypeFirst';
	END IF;
	
	UPDATE pwt.instance_field_values SET
		is_read_only = true
	WHERE instance_id = pPlateInstanceId AND field_id = lPlateTypeFieldId;
	
	RETURN lRes;
 
END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
GRANT EXECUTE ON FUNCTION spCreatePlateDetails(
	pPlateInstanceId bigint,
	pPlateType int,
	pUid integer
) TO iusrpmt;
