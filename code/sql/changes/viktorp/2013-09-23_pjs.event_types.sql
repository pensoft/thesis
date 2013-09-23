INSERT INTO pjs.event_types(name) VALUES('Send Email Notification');
INSERT INTO pjs.email_task_definitions(name, event_type_id, is_automated, subject, content_template, cc) 
VALUES('JM -> Send Email Notification -> U', 106, FALSE, null, null, 'preprint@pensoft.net');

ALTER TABLE pjs.email_task_details DROP CONSTRAINT email_task_details_role_id_fkey;
ALTER TABLE pjs.email_task_details ALTER COLUMN role_id DROP NOT NULL;
