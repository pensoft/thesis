DROP VIEW pwt.v_distinct_document_template_objects;
CREATE OR REPLACE VIEW pwt.v_distinct_document_template_objects AS 
	SELECT DISTINCT ON (dto.document_id, dto.parent_id, dto.object_id) dto.*, o.name as object_name
	FROM pwt.document_template_objects dto
	JOIN pwt.objects o ON o.id = dto.object_id
	ORDER BY dto.document_id, dto.parent_id, dto.object_id, dto.pos ASC
   ;
   
   
GRANT ALL ON TABLE pwt.v_distinct_document_template_objects TO iusrpmt;
