CREATE TABLE xml_sync_templates (
	id serial PRIMARY KEY,
	name varchar
);
GRANT ALL ON TABLE xml_sync_templates  TO iusrpmt;
GRANT ALL ON xml_sync_templates_id_seq TO iusrpmt;

ALTER TABLE articles ADD COLUMN xml_sync_template_id int REFERENCES xml_sync_templates(id) ON DELETE CASCADE;

CREATE TABLE xml_sync_types(
	id serial PRIMARY KEY,
	name varchar
);

GRANT ALL ON TABLE xml_sync_types  TO iusrpmt;
GRANT ALL ON xml_sync_types_id_seq TO iusrpmt;

INSERT INTO xml_sync_types (id, name) VALUES( 1, 'Sync table'), (2, 'Articles Column');
ALTER SEQUENCE xml_sync_types_id_seq RESTART WITH 3;

CREATE TABLE xml_sync_details(
	id serial PRIMARY KEY,
	xml_sync_templates_id int REFERENCES xml_sync_templates(id) ON DELETE CASCADE,	
	name varchar,
	xpath varchar,
	sync_type int REFERENCES xml_sync_types(id) ON DELETE CASCADE,	
	sync_column_name varchar
);
GRANT ALL ON xml_sync_details_id_seq TO iusrpmt;
GRANT ALL ON TABLE xml_sync_details  TO iusrpmt;

CREATE TABLE xml_sync(
	id serial,
	xml_sync_details_id int REFERENCES xml_sync_details(id) ON DELETE CASCADE,
	article_id int REFERENCES articles(id) ON DELETE CASCADE,
	data varchar
);
GRANT ALL ON xml_sync_id_seq TO iusrpmt;
GRANT ALL ON TABLE xml_sync  TO iusrpmt;

INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES 
	(39, '/resources/xml_sync_types/', 'XML Sync Types', 3, 12, 1),
	(40, '/resources/xml_sync_templates/', 'XML Sync Templates', 3, 11, 1);	

INSERT INTO secgrpacc (sid, gid, type) VALUES 
	(39, 2, 6),
	(40, 2, 6);
ALTER SEQUENCE secsites_id_seq RESTART WITH 42;