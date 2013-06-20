DROP TYPE ret_spGetInstanceAllowedObjectsToAdd CASCADE;

CREATE TYPE ret_spGetInstanceAllowedObjectsToAdd AS (
	instance_id bigint,
	object_id bigint,
	display_name varchar,
	parent_id bigint,
	create_in_popup boolean
);

CREATE OR REPLACE FUNCTION spGetInstanceAllowedObjectsToAdd(
	pInstanceId bigint
)
  RETURNS SETOF ret_spGetInstanceAllowedObjectsToAdd AS
$BODY$
	DECLARE
		lRes ret_spGetInstanceAllowedObjectsToAdd;		
		lRecord record;
	BEGIN
		SELECT INTO lRecord * FROM pwt.document_object_instances i WHERE i.id = pInstanceId;
		FOR lRes IN
			SELECT lRecord.id as instance_id, v.object_id, v.display_name, v.parent_id, v.create_in_popup
			FROM pwt.v_distinct_document_template_objects v
			JOIN pwt.object_subobjects os ON os.object_id = lRecord.object_id AND os.subobject_id = v.object_id
			
			WHERE v.parent_id = lRecord.document_template_object_id AND v.allow_add = true
			AND (
				SELECT count(*) 
				FROM pwt.document_object_instances
				WHERE parent_id = pInstanceId AND object_id = v.object_id AND (lRecord.is_confirmed = false OR is_confirmed = true)
			) < os.max_occurrence
			AND coalesce(
				(SELECT bool_or(i1.is_new AND dto1.limit_new_object_creation) 
				FROM pwt.document_object_instances i1
				JOIN pwt.document_template_objects dto1 ON dto1.id = i1.document_template_object_id
				WHERE i1.parent_id = pInstanceId AND i1.object_id = v.object_id  AND (lRecord.is_confirmed = false OR i1.is_confirmed = true))
			, false) = false
			
			
		LOOP
			
			RETURN NEXT lRes;
		END LOOP;
		
		RETURN;		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetInstanceAllowedObjectsToAdd(
	pInstanceId bigint
) TO iusrpmt;
