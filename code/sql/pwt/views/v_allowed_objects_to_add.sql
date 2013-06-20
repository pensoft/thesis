DROP VIEW pwt.v_allowed_objects_to_add;
CREATE OR REPLACE VIEW pwt.v_allowed_objects_to_add AS 
	SELECT i.id as instance_id, v.object_id, v.display_name, v.parent_id
	FROM pwt.document_object_instances i
	JOIN pwt.v_distinct_document_template_objects v ON v.parent_id = i.document_template_object_id
	JOIN pwt.object_subobjects os ON os.object_id = i.object_id AND os.subobject_id = v.object_id
	
	WHERE v.allow_add = true
	AND (SELECT count(*) FROM pwt.document_object_instances WHERE parent_id = i.id AND object_id = v.object_id) < os.max_occurrence
	AND coalesce(
		(SELECT bool_or(i1.is_new AND dto1.limit_new_object_creation) 
		FROM pwt.document_object_instances i1
		JOIN pwt.document_template_objects dto1 ON dto1.id = i1.document_template_object_id
		WHERE i1.parent_id = i.id AND i1.object_id = v.object_id)
	, false) = false
	
	
   ;
   
   
GRANT ALL ON TABLE pwt.v_allowed_objects_to_add TO iusrpmt;
