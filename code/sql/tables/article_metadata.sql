CREATE TABLE pjs.article_metadata(
	id serial PRIMARY KEY,
	document_id bigint NOT NULL REFERENCES pjs.documents(id),
	title varchar,
	abstract text,
	keywords text,
	authors text,
	fulltext text
);

GRANT ALL ON TABLE pjs.article_metadata TO iusrpmt;