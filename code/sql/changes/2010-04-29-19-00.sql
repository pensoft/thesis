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

INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES 
	(45, '/dict/', 'Dict data', 1, 10, 1);	

INSERT INTO secgrpacc (sid, gid, type) VALUES 	
	(45, 2, 6);

UPDATE secsites SET url = '/dict/autotag_property_types/' WHERE id = 35;
UPDATE secsites SET url = '/dict/autotag_property_modifiers/' WHERE id = 34;
UPDATE secsites SET url = '/dict/xml_sync_types/' WHERE id = 39;

UPDATE secsites SET url = '/resources/autotag_rules/regular_expressions/', ord = 4 WHERE id = 37;
UPDATE secsites SET url = '/resources/autotag_rules/place_rules/', ord = 4 WHERE id = 33;
UPDATE secsites SET ord = 2 WHERE id = 44;

INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES 
	(47, '/resources/autotag_rules/autotag_re_sources/', 'Autotag RE Sources', 1, 10, 1);	

INSERT INTO secgrpacc (sid, gid, type) VALUES 	
	(47, 2, 6);

ALTER SEQUENCE secsites_id_seq RESTART WITH 48;

INSERT INTO autotag_property_types(id, name) VALUES (3, 'source');
ALTER SEQUENCE autotag_property_types_id_seq RESTART WITH 4;