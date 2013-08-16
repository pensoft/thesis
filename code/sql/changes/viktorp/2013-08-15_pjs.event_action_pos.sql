CREATE TABLE pjs.event_action_pos (
	id serial PRIMARY KEY NOT NULL,
	event_id int NOT NULL REFERENCES pjs.event_types(id),
	action_id int NOT NULL REFERENCES pjs.event_actions(id),
	journal_id int NOT NULL REFERENCES journals(id),
	ord int NOT NULL 
);

ALTER TABLE pjs.event_action_pos OWNER TO postgres;
GRANT ALL ON TABLE pjs.event_action_pos TO postgres;
GRANT ALL ON TABLE pjs.event_action_pos TO pensoft;
GRANT ALL ON TABLE pjs.event_action_pos TO iusrpmt;