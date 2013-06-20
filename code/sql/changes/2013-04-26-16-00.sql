INSERT INTO pwt.actions(id, "name", display_name, eval_sql_function)
VALUES (100, 'Checklist taxon after creation', 'Checklist taxon after creation', 'spPerformChecklistTaxonAfterCreation');
ALTER SEQUENCE pwt.actions_id_seq RESTART WITH 101;

INSERT INTO pwt.object_actions (object_id, action_id, ord, pos)
VALUES (205, 100, 1, 5);

UPDATE pwt.fields SET
	type = 2
WHERE id IN (424, 425);

UPDATE pwt.template_objects SET
	xml_file_name = 'external_link.xml'
WHERE object_id = 39;

UPDATE pwt.template_objects SET
	xml_file_name = 'taxon.xml'
WHERE object_id = 205;

UPDATE pwt.template_objects SET
	xml_file_name = 'material_ttm_extant_terrestrial_extended_dc.xml'
WHERE object_id = 86;

UPDATE pwt.template_objects SET
	xml_node_name = 'checklist_taxon'
WHERE object_id = 205;

UPDATE pwt.document_template_objects SET
	xml_node_name = 'checklist_taxon'
WHERE object_id = 205;

INSERT INTO pwt.html_control_types(id, "name")
VALUES (46, 'File Upload Taxon');
ALTER SEQUENCE pwt.html_control_types_id_seq RESTART WITH 47;

/*
INSERT INTO pwt.object_container_details(
	container_id, item_id, ord, item_type)
VALUES (760, 476, 3,1);

UPDATE pwt.object_fields SET
	display_in_xml = 2,
	xml_node_name = 'taxon_file_upl'
WHERE field_id = 476;
*/

