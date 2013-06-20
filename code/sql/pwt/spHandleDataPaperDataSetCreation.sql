DROP TYPE ret_spHandleDataPaperDataSetCreation CASCADE;
CREATE TYPE ret_spHandleDataPaperDataSetCreation AS (
	result int
);

CREATE OR REPLACE FUNCTION spHandleDataPaperDataSetCreation(
	pResourcesInstanceId bigint,
	pDataSetNum int,
	pUid int
)
  RETURNS ret_spHandleDataPaperDataSetCreation AS
$BODY$
DECLARE
	lRes ret_spHandleDataPaperDataSetCreation;
	lCurrentDataSets int;
	lDataSetObjectId bigint;
	lIter int;
	lRecord record;
	lParentObjectId bigint;
	lCurrentDataSetsNumFieldId bigint;
	lDataSetsNumFieldId bigint;
BEGIN	
	lDataSetObjectId = 141;
	lCurrentDataSetsNumFieldId = 404;
	lDataSetsNumFieldId = 342;
		
	SELECT INTO lRecord s.* 
	FROM pwt.object_subobjects s
	JOIN pwt.document_object_instances i ON i.object_id = s.object_id
	WHERE i.id = pResourcesInstanceId AND s.subobject_id = lDataSetObjectId;
	
	IF lRecord.min_occurrence > pDataSetNum THEN
		RAISE EXCEPTION 'pwt.youCantDeleteThisManyDataSets';
	ELSEIF lRecord.max_occurrence < pDataSetNum THEN
		RAISE EXCEPTION 'pwt.youCantHaveThisManyDataSets';
	END IF;
	
	--Ъпдейтваме полето за брой на колоните
	UPDATE pwt.instance_field_values SET value_int = pDataSetNum WHERE instance_id = pResourcesInstanceId AND field_id IN (lCurrentDataSetsNumFieldId, lDataSetsNumFieldId);
	
	SELECT INTO lCurrentDataSets count(*) FROM pwt.document_object_instances WHERE parent_id = pResourcesInstanceId AND object_id = lDataSetObjectId;
	lIter = lCurrentDataSets;
	IF lCurrentDataSets > pDataSetNum THEN -- Delete
		-- Махаме тези, които не ни трябват
		FOR lRecord IN 
			SELECT *  
			FROM pwt.document_object_instances 
			WHERE parent_id = pResourcesInstanceId AND object_id = lDataSetObjectId
			ORDER BY pos ASC
			OFFSET pDataSetNum
		LOOP
			PERFORM spRemoveInstance(lRecord.id, pUid);
		END LOOP;		
	ELSEIF lCurrentDataSets < pDataSetNum THEN -- Add
		
		WHILE lIter < pDataSetNum LOOP			
			PERFORM spCreateNewInstance(pResourcesInstanceId, lDataSetObjectId, pUid);
			lIter = lIter + 1;
		END LOOP;
	END IF;

	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spHandleDataPaperDataSetCreation(
	pResourcesInstanceId bigint,
	pDataSetNum int,
	pUid int
) TO iusrpmt;
