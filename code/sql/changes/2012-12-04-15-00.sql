UPDATE pwt.fields SET
	default_has_example_label = true,
	default_example_label = 'DD/MM/YYYY (e.g. 21/12/2012)'
WHERE type = 8;

UPDATE pwt.object_fields of1 SET
	has_example_label = f.default_has_example_label,
	example_label = f.default_example_label
FROM pwt.fields f
WHERE f.id = of1.field_id AND f.type = 8;

-- References
UPDATE pwt.template_objects SET 
	display_default_actions = false 
WHERE object_id = 21;

UPDATE pwt.document_template_objects SET 
	display_default_actions = false 
WHERE object_id = 21 AND display_default_actions = true;

-- Data paper - data set & data set column
UPDATE pwt.template_objects t SET 
	allowed_modes = ARRAY[1],
	default_mode_id = 1,
	default_new_mode_id = 1,	
	default_actions_type = 9,
	title_display_style = 2
WHERE t.object_id IN (141, 142);

UPDATE pwt.document_template_objects t SET 
	allowed_modes = ARRAY[1],
	default_mode_id = 1,
	default_new_mode_id = 1,	
	default_actions_type = 9,
	title_display_style = 2
WHERE t.object_id IN (141, 142);

UPDATE pwt.objects t SET 
	default_allowed_modes = ARRAY[1],
	default_mode_id = 1,
	default_new_mode_id = 1,	
	default_actions_type = 9,
	default_title_display_style = 2
WHERE t.id IN (141, 142);

SELECT * FROM spObjectSubobject(1, 274, 126, 141, 0, 100, 0, 8);
SELECT * FROM spObjectSubobject(1, 275, 141, 142, 0, 100, 0, 8);

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
	xml_node_name = replace(lower(xml_node_name), ',', '_')
WHERE position(',' in xml_node_name) > 0;

UPDATE pwt.template_objects dto SET
	xml_node_name = replace(lower(xml_node_name), '/', '')
WHERE position('/' in xml_node_name) > 0;

UPDATE pwt.data_src SET query = '
SELECT 0 as id, 0 as name
UNION
SELECT 1 as id, 1 as name
UNION
SELECT 2 as id, 2 as name
UNION
SELECT 3 as id, 3 as name
UNION
SELECT 4 as id, 4 as name
UNION
SELECT 5 as id, 5 as name
UNION
SELECT 6 as id, 6 as name
UNION
SELECT 7 as id, 7 as name
UNION
SELECT 8 as id, 8 as name
UNION
SELECT 9 as id, 9 as name
UNION
SELECT 10 as id, 10 as name
UNION
SELECT 11 as id, 11 as name
UNION
SELECT 12 as id, 12 as name
UNION
SELECT 13 as id, 13 as name
UNION
SELECT 14 as id, 14 as name
UNION
SELECT 15 as id, 15 as name
UNION
SELECT 16 as id, 16 as name
UNION
SELECT 17 as id, 17 as name
UNION
SELECT 18 as id, 18 as name
UNION
SELECT 19 as id, 19 as name
UNION
SELECT 20 as id, 20 as name
UNION
SELECT 21 as id, 21 as name
UNION
SELECT 22 as id, 22 as name
UNION
SELECT 23 as id, 23 as name
UNION
SELECT 24 as id, 24 as name
UNION
SELECT 25 as id, 25 as name
UNION
SELECT 26 as id, 26 as name
UNION
SELECT 27 as id, 27 as name
UNION
SELECT 28 as id, 28 as name
UNION
SELECT 29 as id, 29 as name
UNION
SELECT 30 as id, 30 as name
UNION
SELECT 31 as id, 31 as name
UNION
SELECT 32 as id, 32 as name
UNION
SELECT 33 as id, 33 as name
UNION
SELECT 34 as id, 34 as name
UNION
SELECT 35 as id, 35 as name
UNION
SELECT 36 as id, 36 as name
UNION
SELECT 37 as id, 37 as name
UNION
SELECT 38 as id, 38 as name
UNION
SELECT 39 as id, 39 as name
UNION
SELECT 40 as id, 40 as name
UNION
SELECT 41 as id, 41 as name
UNION
SELECT 42 as id, 42 as name
UNION
SELECT 43 as id, 43 as name
UNION
SELECT 44 as id, 44 as name
UNION
SELECT 45 as id, 45 as name
UNION
SELECT 46 as id, 46 as name
UNION
SELECT 47 as id, 47 as name
UNION
SELECT 48 as id, 48 as name
UNION
SELECT 49 as id, 49 as name
UNION
SELECT 50 as id, 50 as name
UNION
SELECT 51 as id, 51 as name
UNION
SELECT 52 as id, 52 as name
UNION
SELECT 53 as id, 53 as name
UNION
SELECT 54 as id, 54 as name
UNION
SELECT 55 as id, 55 as name
UNION
SELECT 56 as id, 56 as name
UNION
SELECT 57 as id, 57 as name
UNION
SELECT 58 as id, 58 as name
UNION
SELECT 59 as id, 59 as name
UNION
SELECT 60 as id, 60 as name
UNION
SELECT 61 as id, 61 as name
UNION
SELECT 62 as id, 62 as name
UNION
SELECT 63 as id, 63 as name
UNION
SELECT 64 as id, 64 as name
UNION
SELECT 65 as id, 65 as name
UNION
SELECT 66 as id, 66 as name
UNION
SELECT 67 as id, 67 as name
UNION
SELECT 68 as id, 68 as name
UNION
SELECT 69 as id, 69 as name
UNION
SELECT 70 as id, 70 as name
UNION
SELECT 71 as id, 71 as name
UNION
SELECT 72 as id, 72 as name
UNION
SELECT 73 as id, 73 as name
UNION
SELECT 74 as id, 74 as name
UNION
SELECT 75 as id, 75 as name
UNION
SELECT 76 as id, 76 as name
UNION
SELECT 77 as id, 77 as name
UNION
SELECT 78 as id, 78 as name
UNION
SELECT 79 as id, 79 as name
UNION
SELECT 80 as id, 80 as name
UNION
SELECT 81 as id, 81 as name
UNION
SELECT 82 as id, 82 as name
UNION
SELECT 83 as id, 83 as name
UNION
SELECT 84 as id, 84 as name
UNION
SELECT 85 as id, 85 as name
UNION
SELECT 86 as id, 86 as name
UNION
SELECT 87 as id, 87 as name
UNION
SELECT 88 as id, 88 as name
UNION
SELECT 89 as id, 89 as name
UNION
SELECT 90 as id, 90 as name
UNION
SELECT 91 as id, 91 as name
UNION
SELECT 92 as id, 92 as name
UNION
SELECT 93 as id, 93 as name
UNION
SELECT 94 as id, 94 as name
UNION
SELECT 95 as id, 95 as name
UNION
SELECT 96 as id, 96 as name
UNION
SELECT 97 as id, 97 as name
UNION
SELECT 98 as id, 98 as name
UNION
SELECT 99 as id, 99 as name
UNION
SELECT 100 as id, 100 as name
ORDER BY id ASC'
WHERE id IN (34, 38);


UPDATE pwt.fields SET
	default_control_type = 5
WHERE id = 289;

UPDATE pwt.object_fields of SET
	control_type = f.default_control_type
FROM pwt.fields f
WHERE f.id = of.field_id AND f.id = 289;


UPDATE pwt.fields SET
	default_control_type = 3
WHERE id IN (291, 290);

UPDATE pwt.object_fields of SET
	control_type = f.default_control_type
FROM pwt.fields f
WHERE f.id = of.field_id AND f.id IN (291, 290);
