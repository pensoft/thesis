ALTER TABLE pjs.document_review_round_users_form DROP CONSTRAINT document_review_round_users_form_pkey;
ALTER TABLE pjs.document_review_round_users_form ADD COLUMN id bigserial PRIMARY KEY NOT NULL;