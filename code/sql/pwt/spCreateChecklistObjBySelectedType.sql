DROP TYPE ret_spCreateChecklistObjBySelectedType CASCADE;
CREATE TYPE ret_spCreateChecklistObjBySelectedType AS (
	result int
);

CREATE OR REPLACE FUNCTION spCreateChecklistObjBySelectedType(
	pInstanceId bigint, -- id на wrapper-a
	pUid int
)
  RETURNS ret_spCreateChecklistObjBySelectedType AS
$BODY$
	DECLARE
		lRes ret_spCreateChecklistObjBySelectedType;	
		lChecklistTypeInstanceId bigint;
		lObjectId bigint;
		
		lChecklistSelectFieldId bigint;
		lChecklistSelectValue int;
		
		lCustomCreationRuleId bigint;
	BEGIN
		lChecklistSelectFieldId = 356;
		lCustomCreationRuleId = 9;
		
		SELECT INTO lChecklistTypeInstanceId parent_id 
			FROM pwt.document_object_instances
			WHERE id = pInstanceId;
		
		SELECT INTO lChecklistSelectValue value_int
		FROM pwt.instance_field_values 
		WHERE instance_id = lChecklistTypeInstanceId AND field_id = lChecklistSelectFieldId;
		
		SELECT INTO lObjectId result FROM spGetCustomCreateObject(lCustomCreationRuleId, ARRAY[lChecklistSelectValue]);
		
		IF coalesce(lObjectId, 0) <> 0 THEN
			 PERFORM spCreateNewInstance(pInstanceId, lObjectId, pUid);
		END IF;
		
		
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateChecklistObjBySelectedType(	
	pInstanceId bigint,
	pUid int
) TO iusrpmt;