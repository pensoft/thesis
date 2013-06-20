ALTER TABLE xml_nodes ADD COLUMN format_tag int DEFAULT 0;

CREATE SEQUENCE autotag_properties_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
ALTER TABLE autotag_properties_id_seq OWNER TO postgres84;


CREATE TABLE place_rules (
	id int NOT NULL DEFAULT nextval('autotag_properties_id_seq'::regclass) PRIMARY KEY,
	name varchar,
	xpath varchar
);

GRANT ALL ON place_rules TO iusrpmt;

CREATE TABLE regular_expressions(
	id int NOT NULL DEFAULT nextval('autotag_properties_id_seq'::regclass) PRIMARY KEY,
	name varchar,
	expression varchar,
	replacement varchar,
	groupsupdepth varchar
);

GRANT ALL ON regular_expressions TO iusrpmt;

CREATE TABLE autotag_property_types(
	id serial PRIMARY KEY,
	name varchar
);

GRANT ALL ON autotag_property_types TO iusrpmt;

INSERT INTO autotag_property_types(id, name) VALUES (1, 'place'), (2, 'regexp');
ALTER SEQUENCE autotag_property_types_id_seq RESTART WITH 3;

CREATE TABLE autotag_property_modifiers(
	id serial PRIMARY KEY,
	type_id int REFERENCES autotag_property_types(id) ON UPDATE CASCADE ON DELETE CASCADE,
	name varchar
);

GRANT ALL ON autotag_property_modifiers TO iusrpmt;

INSERT INTO autotag_property_modifiers(id, name, type_id) VALUES (1, 'AND', 1), (2, 'OR', 1), (3, 'NOT', 1), (4, 'Positive match', 2), (5, 'Negative match', 2);
ALTER SEQUENCE autotag_property_modifiers_id_seq RESTART WITH 6;

CREATE TABLE autotag_rules(
	id serial PRIMARY KEY,
	name varchar
);

GRANT ALL ON autotag_rules TO iusrpmt;

CREATE TABLE autotag_rules_properties(
	rule_id int REFERENCES autotag_rules(id) ON UPDATE CASCADE ON DELETE CASCADE,
	property_type_id int REFERENCES autotag_property_types(id) ON UPDATE CASCADE ON DELETE CASCADE,
	property_id int,
	property_modifier_id int REFERENCES autotag_property_modifiers(id) ON UPDATE CASCADE ON DELETE CASCADE,
	priority integer NOT NULL DEFAULT 0
);

GRANT ALL ON autotag_rules_properties TO iusrpmt;