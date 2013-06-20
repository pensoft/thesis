CREATE TABLE articles(
	id serial PRIMARY KEY,
	createuid int REFERENCES usr(id),
	createdate timestamp DEFAULT CURRENT_TIMESTAMP,
	lastmod timestamp DEFAULT CURRENT_TIMESTAMP,
	title text,
	author text,
	xml_content text
);

GRANT ALL ON TABLE articles TO iusrpmt;