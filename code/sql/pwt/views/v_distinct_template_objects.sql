DROP VIEW IF EXISTS pwt.v_distinct_template_objects;

CREATE OR REPLACE VIEW pwt.v_distinct_template_objects AS 
 SELECT DISTINCT ON (dto.template_id, dto.parent_id, dto.object_id) 
	dto.template_id, dto.object_id, dto.pos, dto.display_in_tree, dto.id, dto.is_fake, dto.allow_movement, 
	dto.allow_add, dto.allow_remove, dto.display_title_and_top_actions, dto.display_name, dto.default_mode_id, dto.allowed_modes, 
	dto.display_default_actions, dto.title_display_style, dto.xml_node_name, dto.default_new_mode_id, dto.display_object_in_xml, 
	dto.generate_xml_id, dto.parent_id, o.name AS object_name, dto.api_allow_null
   FROM pwt.template_objects dto
   JOIN pwt.objects o ON o.id = dto.object_id
  ORDER BY dto.template_id, dto.parent_id, dto.object_id, dto.pos;

ALTER TABLE pwt.v_distinct_template_objects OWNER TO postgres;
GRANT ALL ON TABLE pwt.v_distinct_template_objects TO postgres;
GRANT ALL ON TABLE pwt.v_distinct_template_objects TO iusrpmt;
GRANT ALL ON TABLE pwt.v_distinct_template_objects TO pensoft;

