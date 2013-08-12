ALTER TABLE pwt.html_control_types ADD COLUMN tags_to_keep varchar;
UPDATE pwt.html_control_types SET
	tags_to_keep = 'b, i, u, strong, em, sup, sub, p, ul, ol, li, table, tr, td, tbody, th, reference-citation, fig-citation, tbls-citation, sup-files-citation'
WHERE is_html = true;