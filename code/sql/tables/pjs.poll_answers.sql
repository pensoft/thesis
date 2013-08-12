DROP TABLE IF EXISTS pjs.poll_answers;

CREATE TABLE pjs.poll_answers (
  id bigserial PRIMARY KEY NOT NULL,
  answer_id int,
  document_review_round_users_form_id bigint NOT NULL REFERENCES pjs.document_review_round_users_form(id),
  poll_id int NOT NULL REFERENCES pjs.poll(id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE pjs.poll_answers OWNER TO postgres;
GRANT ALL ON TABLE pjs.poll_answers TO postgres;
GRANT ALL ON TABLE pjs.poll_answers TO pensoft;
GRANT ALL ON TABLE pjs.poll_answers TO public;