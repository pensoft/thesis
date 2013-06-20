DROP VIEW pwt.v_document_template_objects_xml_parent;
CREATE OR REPLACE VIEW pwt.v_document_template_objects_xml_parent AS 
	SELECT DISTINCT ON (dto.document_id, dto.id) dto.id as child_doc_templ_object_id, p.*, dto.document_id as real_doc_id
	FROM pwt.document_template_objects dto
	LEFT JOIN pwt.document_template_objects p1 ON p1.pos = substring(dto.pos, 1, char_length(p1.pos)) AND p1.display_object_in_xml = 2 AND char_length(p1.pos) < char_length(dto.pos)
		AND p1.document_id = dto.document_id
	LEFT JOIN pwt.document_template_objects p ON p.pos = substring(dto.pos, 1, char_length(p.pos)) AND p.display_object_in_xml = 1 AND char_length(p.pos) < char_length(dto.pos)
		AND p.document_id = dto.document_id
	WHERE p1.id IS NULL
	ORDER BY dto.document_id, dto.id, p.pos DESC
   ;
   
   
GRANT ALL ON TABLE pwt.v_document_template_objects_xml_parent TO iusrpmt;
