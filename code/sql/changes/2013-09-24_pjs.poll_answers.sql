ALTER TABLE pjs.poll_answers DROP CONSTRAINT poll_answers_document_review_round_users_form_id_fkey;
ALTER TABLE pjs.poll_answers RENAME COLUMN document_review_round_users_form_id TO rel_element_id;
ALTER TABLE pjs.poll_answers ADD COLUMN rel_element_type int REFERENCES pjs.poll_answers_element_type(id);
UPDATE pjs.poll_answers SET rel_element_type = 1;
ALTER TABLE pjs.poll_answers ALTER COLUMN rel_element_type SET NOT NULL;
ALTER TABLE pjs.article_forum ALTER COLUMN message DROP NOT NULL;