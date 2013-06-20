CREATE TABLE xml_nodes (
	id serial PRIMARY KEY,
	name varchar,
	createdate timestamp DEFAULT CURRENT_TIMESTAMP
);
GRANT ALL ON TABLE xml_nodes TO iusrpmt;