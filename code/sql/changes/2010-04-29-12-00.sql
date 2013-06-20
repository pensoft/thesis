CREATE TABLE auto_numerate_rules(
	id serial PRIMARY KEY,
	name varchar NOT NULL,
	xpath varchar,
	attribute_name varchar,
	starting_value int DEFAULT 0 NOT NULL
);

GRANT ALL ON TABLE auto_numerate_rules TO iusrpmt;
GRANT ALL ON TABLE auto_numerate_rules_id_seq TO iusrpmt;

INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES 
	(44, '/resources/auto_numerate_rules/', 'Auto Numerate Rules', 2, 1, 1);	

INSERT INTO secgrpacc (sid, gid, type) VALUES 	
	(44, 2, 6);
	
ALTER SEQUENCE secsites_id_seq RESTART WITH 45;

ALTER TABLE indesign_template_details ADD COLUMN parent_path varchar;