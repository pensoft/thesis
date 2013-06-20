CREATE TABLE indesign_templates (
	id serial PRIMARY KEY,
	name varchar
);

GRANT ALL ON indesign_templates TO iusrpmt;
GRANT ALL ON indesign_templates_id_seq TO iusrpmt;

CREATE TABLE indesign_template_details (
	id serial PRIMARY KEY,
	indesign_templates_id int REFERENCES indesign_templates(id) ON DELETE CASCADE,
	name varchar,
	node_id int REFERENCES xml_nodes(id) ON DELETE CASCADE,
	replacement varchar
);

GRANT ALL ON indesign_template_details TO iusrpmt;
GRANT ALL ON indesign_template_details_id_seq TO iusrpmt;

INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES 
	(42, '/resources/indesign_templates/', 'Indesign templates', 3, 13, 1);	

INSERT INTO secgrpacc (sid, gid, type) VALUES 	
	(42, 2, 6);
	
INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES 
	(43, '/resources/articles/publishing/', '*DTD', 3, 1, 1);	

INSERT INTO secgrpacc (sid, gid, type) VALUES 	
	(43, 2, 6);
	
ALTER SEQUENCE secsites_id_seq RESTART WITH 44;