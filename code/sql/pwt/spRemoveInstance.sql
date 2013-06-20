DROP TYPE ret_spRemoveInstance CASCADE;
CREATE TYPE ret_spRemoveInstance AS (
	result int,
	parent_instance_id bigint,
	display_in_tree int,
	container_id bigint
);

CREATE OR REPLACE FUNCTION spRemoveInstance(
	pInstanceId bigint,	
	pUid int
)
  RETURNS ret_spRemoveInstance AS
$BODY$
	DECLARE
		lRes ret_spRemoveInstance;			
		lParentObjectId bigint;
		lParentPos varchar;
		lAllowedMinCount int;
		lCurrentInstanceCntOfThisType int;
		lDocumentId int;
		lObjectId bigint;
		lParentInstanceId bigint;
		lCurrentPos varchar;
		lRecord record;
		lContainerObjectType int;
	BEGIN
		lContainerObjectType = 2;
	
		
		SELECT INTO lParentInstanceId, lParentObjectId, lParentPos, lObjectId, lDocumentId, lCurrentPos, lRes.display_in_tree
			p.id, p.object_id, p.pos, i.object_id, i.document_id, i.pos, i.display_in_tree::int
		FROM pwt.document_object_instances i		
		JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND char_length(p.pos) = char_length(i.pos) - 2 AND p.pos = substring(i.pos, 1, char_length(p.pos))
		WHERE i.id = pInstanceId;
		
		lRes.parent_instance_id = lParentInstanceId;
				
		SELECT INTO lRes.container_id  c.id
		FROM pwt.object_container_details cd
		JOIN pwt.object_containers c ON c.id = cd.container_id
		WHERE cd.item_id = lObjectId AND cd.item_type = lContainerObjectType AND c.object_id = lParentObjectId;
		
		
		SELECT INTO lCurrentInstanceCntOfThisType count(*) 
		FROM pwt.document_object_instances 
		WHERE object_id = lObjectId AND char_length(pos) = char_length(lParentPos) + 2 AND substring(pos, 1, char_length(lParentPos)) = lParentPos AND document_id = lDocumentId;
		
		SELECT INTO lAllowedMinCount min_occurrence
		FROM pwt.object_subobjects
		WHERE object_id = lParentObjectId AND subobject_id = lObjectId;
			
		IF lAllowedMinCount >= lCurrentInstanceCntOfThisType THEN
			RAISE EXCEPTION 'pwt.instance.youCantDeleteMoreInstancesOfThisType';
		END IF;
		
		-- Изпълняваме beforeDelete action-ите
		FOR lRecord IN 
			SELECT * FROM pwt.document_object_instances
			WHERE document_id = lDocumentId AND char_length(pos) >= char_length(lCurrentPos) AND substring(pos, 1, char_length(lCurrentPos)) = lCurrentPos				
			ORDER BY char_length(pos) DESC, pos DESC
		LOOP
			PERFORM spPerformInstanceBeforeDeleteActions(lRecord.id, pUid);
		END LOOP;
		
		PERFORM spHandleInstanceCommentsBeforeDelete(pInstanceId, pUid);
		
		-- Взимаме наново позицията понеже може да се е сменила след beforeDelete action-ите
		SELECT INTO lCurrentPos
			i.pos
		FROM pwt.document_object_instances i				
		WHERE i.id = pInstanceId;
		
		-- Трием всички инстанси надолу
		FOR lRecord IN 
			SELECT * FROM pwt.document_object_instances
			WHERE document_id = lDocumentId AND char_length(pos) >= char_length(lCurrentPos) AND substring(pos, 1, char_length(lCurrentPos)) = lCurrentPos				
			ORDER BY char_length(pos) DESC, pos DESC
		LOOP
			--1-во трием цитациите
			DELETE FROM pwt.citations WHERE instance_id = lRecord.id;
			--2-ро трием field-овете
			DELETE FROM pwt.instance_field_values WHERE instance_id = lRecord.id;
			--Трием самия инстанс
			DELETE FROM pwt.document_object_instances WHERE id = lRecord.id;
			
		END LOOP;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spRemoveInstance(
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
