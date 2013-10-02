UPDATE pwt.template_objects SET
	limit_new_object_creation = false
WHERE object_id IN (22);

UPDATE pwt.document_template_objects SET
	limit_new_object_creation = false
WHERE object_id IN (22);

/*
UPDATE pwt.object_fields SET
	control_type = 37
WHERE object_id IN (25, 26, 27, 28, 30, 31)
*/
UPDATE pwt.template_objects SET	
	allowed_modes = ARRAY[1, 2],
	default_mode_id = 2
WHERE object_id  IN (25, 26, 27, 28, 29, 30, 31);

UPDATE pwt.document_template_objects SET	
	allowed_modes = ARRAY[1, 2],
	default_mode_id = 2
WHERE object_id  IN (25, 26, 27, 28, 29, 30, 31);

UPDATE pwt.objects SET	
	default_allowed_modes = ARRAY[1, 2],
	default_mode_id = 2
WHERE id  IN (25, 26, 27, 28, 29, 30, 31);

SET search_path TO public, pwt;

SELECT * FROM spObjectFields(
	1,
	null,
	37,
	209,
	'Type status',
	1,
	0,
	0,
	8
);

SELECT * FROM spObjectFields(
	1,
	null,
	37,
	249,
	'Search DarwinCore term',
	9,
	1,
	0,
	8
);

UPDATE data_src SET 
	query = 'SELECT DISTINCT ON(ifv.field_id) 
		ifv.field_id as id, ifv.field_id, i1.id as instance_id, of.has_help_label::int as has_help_label, of.help_label, of.label, of.label as name,	
		i.id as tabbed_element_instance_id
	FROM pwt.document_object_instances i 
	JOIN pwt.document_object_instances i1 ON i1.document_id = i.document_id AND substring(i1.pos, 1, char_length(i.pos)) = i.pos
	JOIN pwt.instance_field_values ifv ON ifv.instance_id = i1.id
	JOIN pwt.object_fields of ON of.field_id = ifv.field_id AND of.object_id = i1.object_id
	WHERE i.id = {instance_id} AND ifv.field_id NOT IN (209, 249) AND of.label ILIKE ''%{value}%''
	ORDER BY ifv.field_id'
WHERE id = 24;

UPDATE pwt.object_fields SET 
	data_src_id = 24,
	xml_node_name = 'search_dc_term',
	autocomplete_row_templ = 'item.label + (
	item.has_help_label > 0 ? ''<div class="P-Autocomplete-Row-Desc">\
		<div class="P-Input-Help">\
			<div class="P-Baloon-Holder">\
				<div class="P-Baloon-Arrow"></div>\
				<div class="P-Baloon-Top"></div>\
				<div class="P-Baloon-Middle">\
					<div class="P-Baloon-Content">\
						'' + item.help_label + ''\
					</div>\
					<div class="P-Clear"></div>\
				</div>\
				<div class="P-Baloon-Bottom"></div>\
			</div>\
		</div>\
	</div>'' : "")',
	autocomplete_onselect= 'var item = ui.item;
						scrollToTabbedElementField(item.tabbed_element_instance_id, item.field_id);
						$( "#{field_html_identifier}_autocomplete" ).val( '''' );
						$( "#{field_html_identifier}" ).val( '''' );
						return false;'
WHERE field_id = 249 AND object_id = 37;

UPDATE pwt.object_fields SET 
	data_src_id = 10,
	xml_node_name = 'type_status',
	api_allow_null = false
WHERE field_id = 209 AND object_id = 37;

SELECT *, spSyncDocumentObjectFieldsRecursive(
	37,
	id,
	8
)
FROM pwt.documents;


UPDATE pwt.instance_field_values v SET 
	data_src_id = 10
FROM pwt.document_object_instances i
WHERE v.field_id = 209 AND i.object_id = 37 AND i.id = v.instance_id;

UPDATE pwt.instance_field_values v SET 
	data_src_id = 24
FROM pwt.document_object_instances i
WHERE v.field_id = 249 AND i.object_id = 37 AND i.id = v.instance_id;

SELECT * FROM pwt.object_fields 
WHERE field_id = 209;


ALTER TABLE pwt.object_containers ADD COLUMN is_tabbed_mode_container boolean DEFAULT false;

INSERT INTO pwt.object_containers(object_id, mode_id, ord, type, name, css_class, is_tabbed_mode_container)
VALUES (37, 1, 4, 2, 'TTM type & search fields holder', null, true);

INSERT INTO pwt.object_container_details(container_id, item_id, ord, item_type, css_class)
VALUES (801, 209, 1, 1, null), (801, 249, 2, 1, null);

DELETE FROM pwt.object_container_details WHERE container_id = 208;

/*
SELECT * FROM spSyncDocumentObjectFieldsRecursive(
	37,
	2840,
	8
);
*/
-- Update the type of the materials
UPDATE pwt.instance_field_values v SET
	value_int = v1.value_int
FROM pwt.document_object_instances i 
JOIN pwt.document_object_instances i1 ON i1.parent_id = i.id AND i1.object_id = 83
JOIN pwt.instance_field_values v1 ON v1.instance_id = i1.id
WHERE i.id = v.instance_id AND v.field_id = 209  AND v1.field_id = v.field_id AND i.object_id = 37;

-- Remove the fields from priority / extended
SELECT * FROM spObjectFields(
	3,
	706,
	null,
	null,
	null,
	null,
	null,
	null,
	8
);

SELECT * FROM spObjectFields(
	3,
	384,
	null,
	null,
	null,
	null,
	null,
	null,
	8
);

SELECT * FROM spObjectFields(
	3,
	707,
	null,
	null,
	null,
	null,
	null,
	null,
	8
);

SELECT * FROM spObjectFields(
	3,
	385,
	null,
	null,
	null,
	null,
	null,
	null,
	8
);

GRANT ALL ON pwt.object_container_tabbed_item_details TO iusrpmt;

SELECT *, spSyncDocumentObjectFieldsRecursive(
	37,
	id,
	8
)
FROM pwt.documents;

DELETE FROM pwt.object_container_tabbed_item_details
WHERE object_container_tabbed_item_id = 1 AND object_id = 84;

INSERT INTO pwt.object_container_tabbed_item_details(pos, object_id, object_container_tabbed_item_id)
VALUES (3, 31, 1), (4, 30, 1), (5, 28, 1), (6, 27, 1), (7, 26, 1), (8, 25, 1), (9, 29, 1);

INSERT INTO pwt.html_control_types(name, is_html, tags_to_keep)	
	VALUES ('Material field editor', true, 'tn, tn-part, a, b, i, u, strong, em, sup, sub');
	
/*
UPDATE pwt.object_fields SET
	control_type = 54
WHERE object_id IN (25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36);
*/

/*
	Modified sps
	
	pwt.spGetInstanceCitations
*/

/*

SELECT *, spSyncDocumentObjectFieldsRecursive(
	194,
	id,
	8
)
FROM pwt.documents WHERE template_id = 5;

SELECT *, spSyncDocumentObjectRoot(
	239,
	id,
	8
)
FROM pwt.documents WHERE template_id = 5;



SELECT *, spReorderDocumentRootTemplateObjects(	
	id,
	8
)
FROM pwt.documents WHERE template_id = 5;

*/
