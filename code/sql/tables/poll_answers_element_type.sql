DROP TABLE IF EXISTS pjs.poll_answers_element_type;

CREATE TABLE pjs.poll_answers_element_type(
	id serial PRIMARY KEY,
	name varchar NOT NULL
);

GRANT ALL ON TABLE pjs.poll_answers_element_type TO iusrpmt;

INSERT INTO pjs.poll_answers_element_type(name) VALUES('Reviewer poll');
INSERT INTO pjs.poll_answers_element_type(name) VALUES('AOF comment poll');