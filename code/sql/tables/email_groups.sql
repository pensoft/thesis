DROP TABLE IF EXISTS pjs.email_groups;

CREATE TABLE pjs.email_groups(
	id serial PRIMARY KEY,
	name varchar(128),
	sql text NOT NULL
);

GRANT ALL ON TABLE pjs.email_groups TO iusrpmt;