DROP VIEW pwt.v_template_objects_xml_parent;
CREATE OR REPLACE VIEW pwt.v_template_objects_xml_parent AS 
	SELECT DISTINCT ON (dto.template_id, dto.id) dto.id as child_doc_templ_object_id, p.*, dto.template_id as real_template_id
	FROM pwt.template_objects dto
	LEFT JOIN pwt.template_objects p1 ON p1.pos = substring(dto.pos, 1, char_length(p1.pos)) AND p1.display_object_in_xml = 2 AND char_length(p1.pos) < char_length(dto.pos)
		AND p1.template_id = dto.template_id
	LEFT JOIN pwt.template_objects p ON p.pos = substring(dto.pos, 1, char_length(p.pos)) AND p.display_object_in_xml = 1 AND char_length(p.pos) < char_length(dto.pos)
		AND p.template_id = dto.template_id
	WHERE p1.id IS NULL
	ORDER BY dto.template_id, dto.id, p.pos DESC
   ;
   
   
GRANT ALL ON TABLE pwt.v_template_objects_xml_parent TO iusrpmt;
