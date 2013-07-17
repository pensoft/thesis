INSERT INTO pjs.event_types(name) VALUES('Author Submitted Ready for Copy / Layout Editing');
INSERT INTO pjs.event_offset(event_type_id, "offset") VALUES(103, 5);
INSERT INTO pjs.email_task_definitions(name, event_type_id, is_automated, subject, content_template, recipients) 
VALUES('Author Submitted Ready for Copy / Layout Editing',103,TRUE,'Author Submitted Ready for Copy / Layout Editing','Dear {first_name} {last_name}, author submitted version ready for copy / layout editing<br><br> {site_url}',ARRAY[1]);

INSERT INTO pjs.event_types(name) VALUES('SE Accept Reviewer Invitation');
INSERT INTO pjs.event_offset(event_type_id, "offset") VALUES(104, 2);
INSERT INTO pjs.email_task_definitions(name, event_type_id, is_automated, subject, content_template, recipients) 
VALUES('SE -> Reviewer Accept -> SE',104,TRUE,'Reviewer Accepted Invitation','Dear {first_name} {last_name}, reviewer accepted to do the review<br><br> {site_url}',ARRAY[3]),
('SE -> Review Acceptance Acknowledgement -> NR',104,FALSE,'SE accept your invitation','Dear {first_name} {last_name}, SE accepted your invitation<br><br> {site_url}',ARRAY[7]);

UPDATE pjs.email_task_definitions SET recipients = ARRAY[5] WHERE id = 27;