INSERT INTO pjs.event_types(name) VALUES('Author Submitted Ready for Copy / Layout Editing');
INSERT INTO pjs.event_offset(event_type_id, "offset") VALUES(103, 5);
INSERT INTO pjs.email_task_definitions(name, event_type_id, is_automated, subject, content_template, recipients) 
VALUES('Author Submitted Ready for Copy / Layout Editing',103,TRUE,'Author Submitted Ready for Copy / Layout Editing','Dear {first_name} {last_name}, author submitted version ready for copy / layout editing<br><br> {site_url}',ARRAY[1]);