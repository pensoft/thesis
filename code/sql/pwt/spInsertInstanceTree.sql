DROP TYPE IF EXISTS ret_spInsertInstanceTree CASCADE;
CREATE TYPE ret_spInsertInstanceTree AS (
	instance_id bigint
);

CREATE OR REPLACE FUNCTION spInsertInstanceTree(
	pParentInstanceId bigint
)
  RETURNS ret_spInsertInstanceTree AS
$BODY$
DECLARE
	lRes ret_spInsertInstanceTree;
	
	lObjectId bigint;
	lPos varchar;
	lTemplateId int;
	
	lNewId bigint;	
	lNewPos varchar;
	lIter int;
	
	lRecord record;
BEGIN

	SELECT INTO lObjectId, lPos
		object_id, pos
	FROM pwt.document_object_instances 
	WHERE id = pParentInstanceId;
	
	-- Вкарваме всички подобекти по толкова пъти, колкото е тяхното min_occurrence
	lNewPos := 'AA';
	
	<<SubobjectLoop>>
	FOR lRecord IN (SELECT subobject_id, min_occurrence FROM object_subobjects WHERE object_id = lObjectId AND min_occurrence > 0) LOOP
		lIter = 1;
		<<OccurrenceLoop>>
		FOR lIter IN 1 .. lRecord.min_occurrence LOOP
			
			lNewId = nextval('pwt.document_object_instances_id_seq');
			INSERT INTO template_objects(id, template_id, object_id, display_in_tree, pos, display_err) VALUES (lNewId, lTemplateId, lRecord.subobject_id, false, lPos || lNewPos, false);
			lNewPos := ForumGetNextOrd(lNewPos);
			
			PERFORM spInsertInstanceTree(lNewId);			
		END LOOP OccurrenceLoop;
	END LOOP SubobjectLoop;
		
	
	lRes.instance_id = lNewId;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spInsertInstanceTree(
	pParentInstanceId bigint
) TO iusrpmt;
