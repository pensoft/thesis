CREATE TABLE pjs.event_offset (
	id bigserial NOT NULL PRIMARY KEY,
	event_type_id int NOT NULL REFERENCES pjs.event_types(id),
	"offset" int NOT NULL DEFAULT 0,
	journal_id int REFERENCES journals(id),
	section_id int REFERENCES pwt.papertypes(id)
);
ALTER TABLE pjs.event_offset OWNER TO postgres;
GRANT ALL ON TABLE pjs.event_offset TO postgres;
GRANT ALL ON TABLE pjs.event_offset TO pensoft;
GRANT ALL ON TABLE pjs.event_offset TO public;
COMMENT ON COLUMN pjs.event_offset.event_id IS 'ID of the event';
COMMENT ON COLUMN pjs.event_offset.offset IS 'Offset for the current event - in days';
COMMENT ON COLUMN pjs.event_offset.journal_id IS 'Journal ID';
COMMENT ON COLUMN pjs.event_offset.section_id IS 'Section ID';

--CREATE UNIQUE INDEX event_journal_section_event_offset ON pjs.event_offset (event_id, journal_id, section_id);