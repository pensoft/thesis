ALTER TABLE pjs.document_review_rounds ADD COLUMN deadline_date timestamp;
ALTER TABLE pjs.document_review_round_users ADD COLUMN deadline_date timestamp;
ALTER TABLE pjs.document_user_invitations ADD COLUMN deadline_date timestamp;