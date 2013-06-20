ALTER TABLE pjs.email_task_definitions DROP COLUMN default_template_id;
DROP TABLE pjs.email_templates;
ALTER TABLE pjs.email_task_definitions ADD COLUMN subject varchar NOT NULL;
ALTER TABLE pjs.email_task_definitions ADD COLUMN content_template varchar NOT NULL;
ALTER TABLE pjs.email_task_definitions ALTER COLUMN journal_id DROP NOT NULL;
ALTER TABLE pjs.email_task_definitions DROP COLUMN recipients;
ALTER TABLE pjs.email_task_definitions ADD COLUMN recipients integer[];

INSERT INTO pjs.email_task_definitions(name, event_type_id, is_automated, recipients, subject, content_template) VALUES
	('Manuscript Submitted - to Editors', 1, TRUE, ARRAY[1], 'Manuscript Submitted', 'Dear {firstname} {lastname}, <br> a manuscript was submitted'),
	('Manuscript Submitted - to Author', 1, TRUE, ARRAY[2], 'Manuscript Submitted', 'Dear author {firstname} {lastname}, <br> a manuscript was submitted'),
	('SE Assign(non peer)', 2, TRUE, ARRAY[3], 'SE Assigned', 'Dear {firstname} {lastname}, <br> you were assigned as SE (document in non peer review)'),
	('SE Assign(closed peer)', 20, TRUE, ARRAY[3], 'SE Assigned', 'Dear {firstname} {lastname}, <br> you were assigned as SE (document in non peer review)'),
	('SE Assign(community review)', 21, TRUE, ARRAY[3], 'SE Assigned', 'Dear {firstname} {lastname}, <br> you were assigned as SE (document in community review)'),
	('SE Assign(public review)', 22, TRUE, ARRAY[3], 'SE Assigned', 'Dear {firstname} {lastname}, <br> you were assigned as SE (document in public review)'),
	('SE Changed',4,TRUE, ARRAY[7], 'SE Changed','Dear {first_name} {last_name}, you were removed as SE for this document'),
	('SE Accept With Minor Changes',23,TRUE, ARRAY[2], 'SE Accept With Minor Changes', 'Dear {first_name} {last_name}, SE decided to accepted with Minor corrections your document'),
	('SE Accept With Major Changes',24,TRUE, ARRAY[2], 'SE Accept With Major Changes','Dear {first_name} {last_name}, SE decided to accepted with Major corrections your document'),
	('SE Reject',25,TRUE, ARRAY[2], 'SE Reject','Dear {first_name} {last_name}, SE decided to Reject your document'),
	('SE Reject, Nicely',26,TRUE, ARRAY[2], 'SE Reject Nicely', 'Dear {first_name} {last_name}, SE decided to Reject nicely your document'),
	('SE Accept for copy editing',28,TRUE, ARRAY[2], 'SE Accept for Copy Editing', 'Dear {first_name} {last_name}, SE decided to Accept your document to copy editing'),
	('SE Accept for layout editing',27,TRUE, ARRAY[2], 'SE Accept for Layout Editing', 'Dear {first_name} {last_name}, SE decided to Accept your document to layout editing'), 
	('Reviewer Invited',3,TRUE, ARRAY[7], 'Reviewer Invited', 'Dear {first_name} {last_name}, you were invited for reviewing a document'), 
	('Reviewer Accepted',6,TRUE, ARRAY[3], 'Reviewer Accepted', 'Dear {first_name} {last_name}, reviewer accepted your invitation'),
	('Reviewer Declined',8,TRUE, ARRAY[3], 'Reviewer Declined', 'Dear {first_name} {last_name}, reviewer declined your invitation'),
	('All Reviews Declined',9,TRUE, ARRAY[3], 'All Reviews Declined', 'Dear {first_name} {last_name}, all reviews were declined'),
	('All Reviews Submitted',10,TRUE, ARRAY[3], 'All Reviews Submitted', 'Dear {first_name} {last_name}, all reviews were submitted'),
	('Reviewer Canceled By SE',7,TRUE, ARRAY[3], 'Reviewer Canceled By SE', 'Dear {first_name} {last_name}, reviewer was canceled by you'),
	('Reviewer Submitted',12,TRUE, ARRAY[3], 'Reviewer Submitted', 'Dear {first_name} {last_name}, review was submitted'),
	('Author Revision 1 Uploaded',29,TRUE, ARRAY[3], 'Author Revision 1 Uploaded', 'Dear {first_name} {last_name}, author has submitted new version for review round 2'),
	('Author Revision 2 Uploaded',30,TRUE, ARRAY[3], 'Author Revision 2 Uploaded', 'Dear {first_name} {last_name}, author has submitted new version for review round 3'),
	('Author Submitted Ready for Layout',14,TRUE, ARRAY[1], 'Author Submitted Ready for Layout', 'Dear {first_name} {last_name}, author submitted version ready for layout'),
	('Author Submitted Ready for Copy Editing',15,TRUE, ARRAY[1], 'Author Submitted Ready for Copy Editing', 'Dear {first_name} {last_name}, author submitted version ready for copy editing'),
	('LE Assigned',16,TRUE, ARRAY[5], 'LE Assigned', 'Dear {first_name} {last_name}, you were assigned as LE'),
	('CE Assigned',17,TRUE, ARRAY[4], 'CE Assigned', 'Dear {first_name} {last_name}, you were assigned as CE'),
	('Copy Editor Changed',34,TRUE, ARRAY[7], 'CE Change', 'Dear {first_name} {last_name}, you were removed as CE'),
	('Reviewer Invited For Round 2',35,TRUE, ARRAY[7], 'Reviewer Invited For Round 2', 'Dear {first_name} {last_name}, you were invited for reviewing a document for round 2');