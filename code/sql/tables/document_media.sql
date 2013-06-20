DROP TABLE IF EXISTS pjs.document_media;

CREATE TABLE pjs.document_media (
	id bigserial NOT NULL PRIMARY KEY,
	document_id int NOT NULL REFERENCES pjs.documents(id),
	title varchar NOT NULL,
	authors varchar,
	type varchar,
	description varchar,
	file_id int NOT NULL,
	filename varchar NOT NULL
);

GRANT ALL ON pjs.document_media TO iusrpmt;