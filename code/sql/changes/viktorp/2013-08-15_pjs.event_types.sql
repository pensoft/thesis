ALTER TABLE pjs.event_types ADD COLUMN type int REFERENCES pjs.event_rel_types(id);