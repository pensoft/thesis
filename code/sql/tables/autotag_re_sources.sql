CREATE TABLE autotag_re_sources(
	id int PRIMARY KEY DEFAULT nextval('autotag_properties_id_seq'::regclass),
	name varchar,
	source_xpath varchar NOT NULL
);

GRANT ALL ON TABLE autotag_re_sources TO iusrpmt;

CREATE TABLE autotag_re_variable_types(
	id serial PRIMARY KEY,
	name varchar
);

GRANT ALL ON TABLE autotag_re_variable_types TO iusrpmt;
GRANT ALL ON TABLE autotag_re_variable_types_id_seq TO iusrpmt;

INSERT INTO autotag_re_variable_types(id, name) VALUES (1, 'XPath variable'), (2, 'RegExpVariable');
ALTER SEQUENCE autotag_re_variable_types_id_seq RESTART WITH 3;


CREATE TABLE autotag_re_variables(
	id serial PRIMARY KEY,
	name varchar,
	variable_symbol varchar NOT NULL,
	source_id int REFERENCES autotag_re_sources(id) ON UPDATE CASCADE,
	variable_type int REFERENCES autotag_re_variable_types(id) ON UPDATE CASCADE,
	expression varchar,
	concat_multiple int DEFAULT 0,
	concat_separator varchar
);

GRANT ALL ON TABLE autotag_re_variables TO iusrpmt;
GRANT ALL ON TABLE autotag_re_variables_id_seq TO iusrpmt;