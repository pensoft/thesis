CREATE TABLE auto_numerate_rules(
	id serial PRIMARY KEY,
	name varchar NOT NULL,
	xpath varchar,
	attribute_name varchar,
	starting_value int DEFAULT 0 NOT NULL
);

GRANT ALL ON TABLE auto_numerate_rules TO iusrpmt;
GRANT ALL ON TABLE auto_numerate_rules_id_seq TO iusrpmt;