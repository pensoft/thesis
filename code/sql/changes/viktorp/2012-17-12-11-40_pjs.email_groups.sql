INSERT INTO pjs.email_groups(name, sql) VALUES
	('Journal Editors', 'SELECT uid, 2 as role_id FROM journal_users WHERE journal_id = {journal_id} AND role_id = 2'),
	('Submitting Author', 'SELECT submitting_author_id, 11 as role_id FROM pjs.documents WHERE id = {document_id}'),
	('Section Editors', 'SELECT uid, 3 as role_id FROM pjs.document_users WHERE document_id = {document_id} AND role_id = 3'),
	('Copy Editor', 'SELECT uid, 9 as role_id FROM pjs.document_users WHERE document_id = {document_id} AND role_id = 9'),
	('Layout Editor', 'SELECT uid, 8 as role_id FROM pjs.document_users WHERE document_id = {document_id} AND role_id = 8'),
	('Submitting Author & Co-Author', '
		SELECT submitting_author_id, 11 as role_id FROM pjs.documents WHERE id = {document_id} 
		UNION 
		SELECT uid, 11 as role_id FROM pjs.document_users WHERE document_id = {document_id} AND role_id = 11 AND co_author = 1'),
	('Reviewer Invitation', 'SELECT {uid} as uid, {role_id} as role_id'),