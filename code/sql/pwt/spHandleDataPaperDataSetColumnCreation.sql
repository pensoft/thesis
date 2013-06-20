DROP TYPE ret_spHandleDataPaperDataSetColumnCreation CASCADE;
CREATE TYPE ret_spHandleDataPaperDataSetColumnCreation AS (
	result int
);

CREATE OR REPLACE FUNCTION spHandleDataPaperDataSetColumnCreation(
	pDataSetInstanceId bigint,
	pColumnsNum int,
	pUid int
)
  RETURNS ret_spHandleDataPaperDataSetColumnCreation AS
$BODY$
DECLARE
	lRes ret_spHandleDataPaperDataSetColumnCreation;
	lCurrentDataSets int;
	lColumnObjectId bigint;
	lIter int;
	lRecord record;
	lParentObjectId bigint;
	lCurrentColumnsNumFieldId bigint;
	lColumnsNumFieldId bigint;
BEGIN	
	lColumnObjectId = 142;
	lCurrentColumnsNumFieldId = 403;
	lColumnsNumFieldId = 400;
		
	SELECT INTO lRecord s.* 
	FROM pwt.object_subobjects s
	JOIN pwt.document_object_instances i ON i.object_id = s.object_id
	WHERE i.id = pDataSetInstanceId AND s.subobject_id = lColumnObjectId;
	
	IF lRecord.min_occurrence > pColumnsNum THEN
		RAISE EXCEPTION 'pwt.youCantDeleteThisManyColumns';
	ELSEIF lRecord.max_occurrence < pColumnsNum THEN
		RAISE EXCEPTION 'pwt.youCantHaveThisManyColumns';
	END IF;
	
	--Ъпдейтваме полето за брой на колоните
	UPDATE pwt.instance_field_values SET value_int = pColumnsNum WHERE instance_id = pDataSetInstanceId AND field_id IN(lCurrentColumnsNumFieldId, lColumnsNumFieldId);
	
	SELECT INTO lCurrentDataSets count(*) FROM pwt.document_object_instances WHERE parent_id = pDataSetInstanceId AND object_id = lColumnObjectId;
	lIter = lCurrentDataSets;
	IF lCurrentDataSets > pColumnsNum THEN -- Delete
		-- Махаме тези, които не ни трябват
		FOR lRecord IN 
			SELECT *  
			FROM pwt.document_object_instances 
			WHERE parent_id = pDataSetInstanceId AND object_id = lColumnObjectId
			ORDER BY pos ASC
			OFFSET pColumnsNum
		LOOP
			PERFORM spRemoveInstance(lRecord.id, pUid);
		END LOOP;		
	ELSEIF lCurrentDataSets < pColumnsNum THEN -- Add
		
		WHILE lIter < pColumnsNum LOOP			
			PERFORM spCreateNewInstance(pDataSetInstanceId, lColumnObjectId, pUid);
			lIter = lIter + 1;
		END LOOP;
	END IF;

	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spHandleDataPaperDataSetColumnCreation(
	pDataSetInstanceId bigint,
	pColumnsNum int,
	pUid int
) TO iusrpmt;
