ALTER TABLE pjs.document_version_types ADD COLUMN is_readonly boolean DEFAULT false;
UPDATE pjs.document_version_types SET 
	is_readonly = true
WHERE id IN (1, 10);

/*
	Modified sp's
	
	spCheckIfPjsVersionIsReadonly
	pjs.spResolveComment
	pjs.spDeleteComment
	pjs.spNewCommentReply
	pjs.spCommentEdit
	pjs.spNewComment
*/
