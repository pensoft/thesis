ALTER TABLE pwt.field_types ADD COLUMN is_array boolean DEFAULT FALSE;
UPDATE pwt.field_types SET
	is_array = true
WHERE id IN (6, 7, 9);

ALTER TABLE pwt.html_control_types ADD COLUMN is_html boolean DEFAULT FALSE;
UPDATE pwt.html_control_types SET
	is_html = true
WHERE id IN (3, 37, 43, 5, 33, 36, 34, 35);	