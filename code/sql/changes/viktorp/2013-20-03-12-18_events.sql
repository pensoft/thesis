INSERT INTO pjs.event_types ("name") VALUES ('Journal Manager Created User');
INSERT INTO pjs.event_types ("name") VALUES ('SE Created');
INSERT INTO pjs.event_types ("name") VALUES ('Reviewer Created');


INSERT INTO pjs.email_task_definitions("name", event_type_id, subject, content_template, recipients) VALUES('Journal Manager Created User', 100, 'New registration', 'Dear {first_name} {last_name}, you were registered.<br>Password: {upass}<br><br> {site_url}', ARRAY[7]);
INSERT INTO pjs.email_task_definitions("name", event_type_id, subject, content_template, recipients) VALUES('SE Created', 101, 'You were registered as Section Editor', 'Dear {first_name} {last_name}, you were registered as Section Editor.<br>Password: {upass}<br><br> {site_url}', ARRAY[7]);
INSERT INTO pjs.email_task_definitions("name", event_type_id, subject, content_template, recipients) VALUES('Reviewer Created', 102, 'You were registered as Reviewer', 'Dear {first_name} {last_name}, you were registered as Reviewer.<br>Password: {upass}<br><br> {site_url}', ARRAY[7]);







