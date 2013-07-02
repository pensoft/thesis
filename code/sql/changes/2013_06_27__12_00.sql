CREATE TABLE pwt.object_cached_xml_types (
	id serial PRIMARY KEY,
	name varchar
);
GRANT ALL ON pwt.object_cached_xml_types TO iusrpmt;
INSERT INTO pwt.object_cached_xml_types(name) VALUES ('Fields only'), ('Whole subtree'), ('None');

ALTER TABLE pwt.document_object_instances ADD COLUMN cached_xml xml;
ALTER TABLE pwt.objects ADD column default_cached_xml_type int REFERENCES pwt.object_cached_xml_types(id) DEFAULT 1;
ALTER TABLE pwt.template_objects ADD column cached_xml_type int REFERENCES pwt.object_cached_xml_types(id) DEFAULT 1;
ALTER TABLE pwt.document_template_objects ADD column cached_xml_type int DEFAULT 1;
ALTER TABLE pwt.document_object_instances ALTER COLUMN is_modified SET DEFAULT true;
ALTER TABLE pwt.document_object_instances ADD COLUMN lastmod_date timestamp DEFAULT now();

UPDATE pwt.document_object_instances SET is_modified = true;
/*	
	UPDATE pwt.document_template_objects SET cached_xml_type = 2 WHERE document_id = 2474 AND char_length(pos) = 2;
	UPDATE pwt.document_template_objects o SET 
		cached_xml_type = 3 
	WHERE o.document_id = 2474 AND NOT EXISTS(SELECT * FROM pwt.object_fields WHERE object_id = o.object_id);
	UPDATE pwt.document_object_instances SET is_modified = true WHERE document_id = 2474;
	UPDATE pwt.documents SET xml_is_dirty = true WHERE id = 2474
*/
/*
CREATE INDEX document_object_instances_tree_idx ON pwt.document_object_instances(document_id, is_confirmed, display_in_tree);
CREATE INDEX document_object_instances_tree_with_pos_idx ON pwt.document_object_instances(document_id, is_confirmed, display_in_tree, pos);

CREATE INDEX document_object_instances_pos_idx ON pwt.document_object_instances(pos);
*/