ALTER TABLE pwt.data_src ADD COLUMN placeholder varchar;

UPDATE pwt.template_objects t SET 
	allowed_modes = ARRAY[1, 2],
	default_mode_id = 2,
	default_new_mode_id = 1,	
	default_actions_type = 7,
	title_display_style = 2,
	create_in_popup = true
WHERE t.object_id IN (55);

UPDATE pwt.document_template_objects t SET 
	allowed_modes = ARRAY[1, 2],
	default_mode_id = 2,
	default_new_mode_id = 1,	
	default_actions_type = 7,
	title_display_style = 2,
	create_in_popup = true	
WHERE t.object_id IN (55);

/* Previous
UPDATE pwt.template_objects t SET 
	allowed_modes = ARRAY[1, 2],
	default_mode_id = 2,
	default_new_mode_id = 1,	
	default_actions_type = 1,
	title_display_style = 2,
	create_in_popup = false
WHERE t.object_id IN (55);
*/