CREATE TABLE pjs.event_actions (
	id serial PRIMARY KEY NOT NULL,
	eval_code varchar,
	eval_sql_function varchar
);

ALTER TABLE pjs.event_actions OWNER TO postgres;
GRANT ALL ON TABLE pjs.event_actions TO postgres;
GRANT ALL ON TABLE pjs.event_actions TO pensoft;
GRANT ALL ON TABLE pjs.event_actions TO iusrpmt;