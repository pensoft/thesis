INSERT INTO pwt.field_default_values(value_int, name) VALUES(1, 'Use license');
UPDATE pwt.object_fields SET default_value_id = 22 WHERE field_id = 311;
UPDATE pwt.instance_field_values SET value_int = 1 WHERE field_id = 311 AND value_int IS NULL;