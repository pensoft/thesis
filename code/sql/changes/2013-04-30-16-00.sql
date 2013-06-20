INSERT INTO pwt.object_default_actions_type(id, name) 
	VALUES (11, 'Only delete, duplicate, movement and edit(in popup) in viewmode top');
ALTER SEQUENCE pwt.object_default_actions_type_id_seq RESTART WITH 12;

INSERT INTO pwt.object_default_actions_type_details
(type_id, ord, pos, "mode", default_action_id)
SELECT 11, ord, pos, "mode", default_action_id
FROM pwt.object_default_actions_type_details
WHERE type_id = 10;

UPDATE pwt.objects SET
	default_actions_type = 11
WHERE id = 37;

UPDATE pwt.template_objects SET
	default_actions_type = 11
WHERE object_id = 37;

UPDATE pwt.document_template_objects SET
	default_actions_type = 11
WHERE object_id = 37;



INSERT INTO pwt.object_default_actions(id, "name", action_id) VALUES (22, 'Duplicate top', 113);
ALTER SEQUENCE pwt.object_default_actions_id_seq RESTART WITH 23;
INSERT INTO pwt.object_default_actions_type_details
(type_id, ord, pos, "mode", default_action_id) VALUES (11, 5, 1, 2, 22);