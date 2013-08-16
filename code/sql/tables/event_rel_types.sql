DROP TABLE IF EXISTS pjs.event_rel_types CASCADE;

CREATE TABLE pjs.event_rel_types (
	id serial PRIMARY KEY NOT NULL,
	name character varying(128),
	journal_id int NOT NULL REFERENCES journals(id)
);

ALTER TABLE pjs.event_rel_types OWNER TO postgres;
GRANT ALL ON TABLE pjs.event_rel_types TO postgres;
GRANT ALL ON TABLE pjs.event_rel_types TO pensoft;
GRANT ALL ON TABLE pjs.event_rel_types TO iusrpmt;