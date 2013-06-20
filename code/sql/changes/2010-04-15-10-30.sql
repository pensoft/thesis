ALTER TABLE indesign_templates ADD COLUMN type int;
UPDATE indesign_templates SET type = 1;

ALTER TABLE indesign_template_details ADD COLUMN type int;
UPDATE indesign_template_details SET type = 1;

ALTER TABLE indesign_template_details RENAME COLUMN replacement TO style;

ALTER TABLE xml_sync_details ADD COLUMN sync_column_default_value varchar;