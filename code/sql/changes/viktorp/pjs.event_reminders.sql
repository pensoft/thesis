DROP TABLE IF EXISTS pjs.event_reminders;

CREATE TABLE pjs.event_reminders (
	id bigserial NOT NULL PRIMARY KEY,
	event_type_id int NOT NULL REFERENCES pjs.event_types(id),
	condition_sql varchar NOT NULL,
	journal_id int NOT NULL REFERENCES journals (id)
);
ALTER TABLE pjs.event_reminders OWNER TO postgres;
GRANT ALL ON TABLE pjs.event_reminders TO postgres;
GRANT ALL ON TABLE pjs.event_reminders TO pensoft;
GRANT ALL ON TABLE pjs.event_reminders TO public;
COMMENT ON COLUMN pjs.event_reminders.event_type_id IS 'ID of the event type';
--COMMENT ON COLUMN pjs.event_reminders.offset IS 'Offset for the current event(reminder) - in days';
COMMENT ON COLUMN pjs.event_reminders.condition_sql IS 'Condition that will be checked';
COMMENT ON COLUMN pjs.event_reminders.journal_id IS 'Journal ID';

--CREATE UNIQUE INDEX event_type_id_idx ON pjs.event_reminders(event_type_id);