DROP TABLE IF EXISTS pjs.document_review_round_user_states;

CREATE TABLE pjs.document_review_round_user_states(
	id serial PRIMARY KEY,
	name varchar
);

GRANT ALL ON TABLE pjs.document_review_round_user_states TO iusrpmt;

INSERT INTO pjs.document_review_round_user_states(name) VALUES('Confirmed');
INSERT INTO pjs.document_review_round_user_states(name) VALUES('Declined');