ALTER TABLE pwt.document_object_instances ADD COLUMN display_err boolean DEFAULT FALSE;
ALTER TABLE pwt.template_objects ADD COLUMN display_err boolean DEFAULT FALSE;
ALTER TABLE pwt.document_template_objects ADD COLUMN display_err boolean DEFAULT FALSE;

UPDATE pwt.document_object_instances SET display_err = display_in_tree;
UPDATE pwt.template_objects SET display_err = display_in_tree;
UPDATE pwt.document_template_objects SET display_err = display_in_tree;

-- authors
UPDATE pwt.template_objects set display_err = TRUE WHERE object_id = 8;
UPDATE pwt.document_object_instances SET display_err = TRUE WHERE object_id = 8;
UPDATE pwt.document_template_objects SET display_err = TRUE WHERE object_id = 8;

-- reference
UPDATE pwt.template_objects set display_err = TRUE WHERE object_id = 95;
UPDATE pwt.document_object_instances SET display_err = TRUE WHERE object_id = 95;
UPDATE pwt.document_template_objects SET display_err = TRUE WHERE object_id = 95;

-- material
UPDATE pwt.template_objects set display_err = TRUE WHERE object_id = 37;
UPDATE pwt.document_object_instances SET display_err = TRUE WHERE object_id = 37;
UPDATE pwt.document_template_objects SET display_err = TRUE WHERE object_id = 37;
