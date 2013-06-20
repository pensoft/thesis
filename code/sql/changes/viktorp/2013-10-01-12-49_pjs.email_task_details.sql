ALTER TABLE pjs.email_task_details ADD COLUMN role_id int REFERENCES pjs.user_role_types(id);
ALTER TABLE pjs.email_task_details ALTER COLUMN role_id SET NOT NULL;