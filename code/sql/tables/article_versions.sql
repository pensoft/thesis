CREATE TABLE article_versions (
	id serial PRIMARY KEY,
	createdate timestamp DEFAULT CURRENT_TIMESTAMP,
	article_id int REFERENCES articles(id) ON DELETE CASCADE ON UPDATE CASCADE,
	version int,
	xml_content text
);

GRANT ALL ON article_versions TO iusrpmt;
GRANT ALL ON article_versions_id_seq TO iusrpmt;

