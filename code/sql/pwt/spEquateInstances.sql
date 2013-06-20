DROP TYPE ret_spEquateInstances CASCADE;
CREATE TYPE ret_spEquateInstances AS (
	result int
);

CREATE OR REPLACE FUNCTION spEquateInstances(
	pInstanceFromId bigint,
	pInstanceToId bigint,
	pUid int
)
  RETURNS ret_spEquateInstances AS
$BODY$
	DECLARE
		lRes ret_spEquateInstances;					
		lObjectIdFrom bigint;
		lObjectIdTo bigint;
		lRecord record;
		lRecord2 record;
		lSubInstanceId bigint;
		lProcessedSubInstanceIds bigint[];
	BEGIN
		SELECT INTO lObjectIdFrom
			object_id
		FROM pwt.document_object_instances 
		WHERE id = pInstanceFromId;
		
		SELECT INTO lObjectIdTo
			object_id
		FROM pwt.document_object_instances 
		WHERE id = pInstanceToId;
		
		IF lObjectIdFrom <> lObjectIdTo THEN
			RAISE EXCEPTION 'pwt.cannotEquateInstancesWithDifferentObjectId';
		END IF;
		
		-- Update instance fields
		UPDATE pwt.instance_field_values i SET
			value_str = i1.value_str,
			value_int = i1.value_int,
			value_arr_int = i1.value_arr_int,
			value_arr_str = i1.value_arr_str,
			value_date = i1.value_date,
			value_arr_date = i1.value_arr_date,
			is_read_only = i1.is_read_only,
			data_src_id = i1.data_src_id
		FROM pwt.instance_field_values i1 
		WHERE i1.instance_id = pInstanceFromId AND i1.field_id = i.field_id
		AND i.instance_id = pInstanceToId;
		
		-- Equate all subobjects (of From)
		FOR lRecord IN 
			SELECT *, (SELECT count(*)
				FROM pwt.document_object_instances 
				WHERE parent_id = pInstanceFromId 
				AND object_id = i.object_id AND pos < i.pos
			) + 1 as object_idx
			FROM pwt.document_object_instances i
			WHERE parent_id = pInstanceFromId 
			ORDER BY object_id, pos ASC
		LOOP
			SELECT INTO lRecord2 
				*
			FROM pwt.document_object_instances i
			WHERE parent_id = pInstanceToId AND object_id = lRecord.object_id 
			ORDER BY pos ASC
			LIMIT 1 OFFSET lRecord.object_idx - 1;
			
			lSubInstanceId = lRecord2.id;
			IF lSubInstanceId IS NULL THEN
				-- If the subobject doesnt exist in pInstanceToId - we create it
				SELECT INTO lSubInstanceId 
					new_instance_id
				FROM spCreateNewInstance(pInstanceToId, lRecord.object_id, pUid);
			END IF;
			
			lProcessedSubInstanceIds = array_prepend(lSubInstanceId, lProcessedSubInstanceIds);	
			-- Equate the 2 subobjects
			PERFORM spEquateInstances(lRecord.id , lSubInstanceId , pUid);
		END LOOP;
		
		-- Remove subobjects which dont exist in From
		FOR lRecord IN 
			SELECT *
			FROM pwt.document_object_instances 
			WHERE parent_id = pInstanceToId
				AND id <> ALL (lProcessedSubInstanceIds)
		LOOP
			PERFORM spRemoveInstance(lRecord.id, pUid);
		END LOOP;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spEquateInstances(
	pInstanceFromId bigint,
	pInstanceToId bigint,
	pUid int
) TO iusrpmt;
