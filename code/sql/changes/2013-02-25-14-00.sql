ALTER TABLE pwt.document_object_instances ADD COLUMN is_confirmed boolean DEFAULT true;

INSERT INTO pwt.actions(id, display_name, name, eval_sql_function) 
	VALUES (95, 'Mark instance as unconfirmed', 'Mark instance as unconfirmed', 'pwt.spMarkInstanceAsUnconfirmed'), 
		(96, 'Mark instance as confirmed', 'Mark instance as confirmed', 'pwt.spMarkInstanceAsConfirmed');
ALTER SEQUENCE pwt.actions_id_seq RESTART WITH 97;

/*
-- References
INSERT INTO pwt.object_actions(object_id, action_id, ord, pos, execute_in_modes) VALUES (95, 95, 2, 5, ARRAY[1]);
INSERT INTO pwt.object_actions(object_id, action_id, ord, pos, execute_in_modes) VALUES (95, 96, 3, 4, ARRAY[1]);

-- Supp files -- only confirm on save (the instance can be created not in popup)
INSERT INTO pwt.object_actions(object_id, action_id, ord, pos, execute_in_modes) VALUES (55, 96, 1, 4, ARRAY[1]);

*/