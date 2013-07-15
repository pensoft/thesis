UPDATE pwt.template_objects SET 
	xml_file_name = 'taxonomic_coverage_taxa.xml' 
WHERE template_id IN (4, 5) AND object_id = 191;

INSERT INTO pwt.html_control_types(name) VALUES ('File Upload Taxonomic Coverage Taxa');

INSERT INTO pwt.object_containers(object_id, mode_id, ord, type, name) VALUES (119, 1, 3, 1, 'Taxonomic coverage upload taxa holder');
INSERT INTO pwt.object_container_details(container_id, item_id, ord, item_type) VALUES (780, 481, 1, 1);
INSERT INTO pwt.object_container_details(container_id, item_id, ord, item_type) VALUES (780, 35, 2, 3);

UPDATE pwt.object_fields SET xml_node_name = 'file_upload_taxa' WHERE field_id = 481;