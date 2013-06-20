CREATE TABLE xml_attributes (
	id serial PRIMARY KEY,
	node_id int REFERENCES xml_nodes(id) ON UPDATE NO ACTION ON DELETE CASCADE,
	name varchar,
	createdate timestamp DEFAULT CURRENT_TIMESTAMP
);
GRANT ALL ON TABLE xml_attributes TO iusrpmt;