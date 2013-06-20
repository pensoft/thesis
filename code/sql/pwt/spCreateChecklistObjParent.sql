DROP TYPE ret_spCreateChecklistObjParent CASCADE;
CREATE TYPE ret_spCreateChecklistObjParent AS (
	result int
);

CREATE OR REPLACE FUNCTION spCreateChecklistObjParent(
	pInstanceId bigint, 
	pUid int
)
RETURNS ret_spCreateChecklistObjParent AS
$BODY$
	DECLARE
		lRes ret_spCreateChecklistObjParent;	
		lCurrentInstanceCntOfThisType int;
		lAllowedMaxCount int;
		lObjectForAdd int;
		lObjectForAddParent int;
	BEGIN
		lObjectForAddParent = 129;
		lObjectForAdd = 130;
		
		UPDATE pwt.instance_field_values SET is_read_only = true WHERE instance_id = pInstanceId AND field_id = 356;
		
		-- Гледаме дали е възможно добавянето на този обект - дали няма прекалено много инстанси от този тип, към този parent
		SELECT INTO lCurrentInstanceCntOfThisType count(*) 
		FROM pwt.document_object_instances 
		WHERE object_id = lObjectForAdd AND parent_id = pInstanceId;
		
		SELECT INTO lAllowedMaxCount max_occurrence
		FROM pwt.object_subobjects
		WHERE object_id = lObjectForAddParent AND subobject_id = lObjectForAdd;
		
		-- RAISE NOTICE 'Max %, Current %', lAllowedMaxCount, lCurrentInstanceCntOfThisType;

		IF lAllowedMaxCount > lCurrentInstanceCntOfThisType THEN
			PERFORM spCreateNewInstance(pInstanceId, lObjectForAdd, pUid);
		END IF;
		
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateChecklistObjParent(	
	pInstanceId bigint,
	pUid int
) TO iusrpmt;