SET search_path TO public, pwt
;

INSERT INTO pwt.objects(
	id, "name", createuid, lastmoduid, default_display_name, default_mode_id, default_allowed_modes, default_display_title_and_top_actions,
	default_new_mode_id, default_actions_type, default_displayed_actions_type,default_limit_new_object_creation  
)

VALUES (201, 'Reference single citation', 8, 8, 'Citation', 1, ARRAY[1, 2], false,
	1, 3, 2, false);
ALTER SEQUENCE pwt.objects_id_seq RESTART WITH 201;

SELECT * FROM spFields(1, null, 'Reference citation pages figures', 2, 'Pages, figures', 2, 1, 0, 8);
-- 461
SELECT * FROM spFields(1, null, 'Reference citation notes', 2, 'Notes', 2, 1, 0, 8);
-- 462

SELECT * FROM spObjectFields(1, null, 201, 461, 'Pages, figures', 2, 1, 0, 8);
SELECT * FROM spObjectFields(1, null, 201, 462, 'Notes', 2, 1, 0, 8);
INSERT INTO pwt.object_subobjects(object_id, subobject_id, min_occurrence, max_occurrence, initial_occurrence)
VALUES (201, 178, 1, 1, 1);

INSERT INTO pwt.object_containers(id, object_id, mode_id, ord, type, name)
VALUES (729, 201, 1, 1, 2, 'Reference single citation items holder');
ALTER SEQUENCE pwt.object_containers_id_seq RESTART WITH 730;

INSERT INTO pwt.object_container_details(container_id, item_id, ord, item_type, css_class)
VALUES (729, 178, 1, 2, null), 
	(729, 461, 2, 1, 'oneThirdWidth floatLeft marginTop35'), 
	(729, 462, 3, 1, 'oneThirdWidth floatLeft marginTop35 lastItem'),
	(704, 201, 1, 2, null);
	
SELECT * FROM spObjectSubobject(3, 339, null, null, null, null, null, null);
SELECT * FROM spObjectSubobject(1, null, 187, 201, 1, 99999999, 1, 8);

UPDATE pwt.template_objects t SET 
	display_title_and_top_actions = false	
WHERE t.object_id IN (187);

UPDATE pwt.object_fields dto SET
	xml_node_name = translate(lower(label), ' ', '_')
WHERE coalesce(xml_node_name, '') = '';

UPDATE pwt.object_fields dto SET
	xml_node_name = translate(lower(xml_node_name), '()', '')
WHERE position('(' in xml_node_name) > 0 OR position(')' in xml_node_name) > 0;

UPDATE pwt.object_fields dto SET
	xml_node_name = replace(lower(xml_node_name), '&', 'and')
WHERE position('&' in xml_node_name) > 0;

UPDATE pwt.object_fields dto SET
	xml_node_name = replace(lower(xml_node_name), '/', '')
WHERE position('/' in xml_node_name) > 0;


UPDATE pwt.template_objects dto SET
	xml_node_name = translate(lower(display_name), ' -', '_')
WHERE coalesce(xml_node_name, '') = '';

UPDATE pwt.template_objects dto SET
	xml_node_name = translate(lower(xml_node_name), '()', '')
WHERE position('(' in xml_node_name) > 0 OR position(')' in xml_node_name) > 0;

UPDATE pwt.template_objects dto SET
	xml_node_name = replace(lower(xml_node_name), '&', 'and')
WHERE position('&' in xml_node_name) > 0;

UPDATE pwt.template_objects dto SET
	xml_node_name = replace(lower(xml_node_name), '/', '')
WHERE position('/' in xml_node_name) > 0;