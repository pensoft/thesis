INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question1, id, 1 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question2, id, 2 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question3, id, 3 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question4, id, 4 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question5, id, 5 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question6, id, 6 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question7, id, 7 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question8, id, 8 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question9, id, 9 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question10, id, 10 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question11, id, 11 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question12, id, 12 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question13, id, 13 FROM pjs.document_review_round_users_form;
INSERT INTO pjs.poll_answers(answer_id, rel_element_id, poll_id) SELECT question14, id, 14 FROM pjs.document_review_round_users_form;

ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question1;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question2;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question3;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question4;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question5;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question6;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question7;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question8;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question9;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question10;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question11;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question12;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question13;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question14;
ALTER TABLE pjs.document_review_round_users_form DROP COLUMN question15;


GRANT ALL ON TABLE pjs.document_review_round_users_form_id_seq TO public;
GRANT ALL ON TABLE pjs.document_review_round_users_form_id_seq TO iusrpmt;