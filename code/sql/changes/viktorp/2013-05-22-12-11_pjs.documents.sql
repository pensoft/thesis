ALTER TABLE pjs.documents RENAME COLUMN community_public_due_date TO panel_duedate;
ALTER TABLE pjs.documents ADD COLUMN public_duedate timestamp;