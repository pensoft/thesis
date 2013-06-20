ALTER TABLE pwt.msg ADD COLUMN revision_id bigint REFERENCES pwt.document_revisions(id);

UPDATE pwt.msg SET 
	revision_id = spGetDocumentLatestCommentRevisionId(document_id, 0)
WHERE revision_id IS NULL;

DELETE FROM pwt.msg
WHERE spGetDocumentLatestCommentRevisionId(document_id, 0) IS NULL;

ALTER TABLE pwt.msg ALTER COLUMN revision_id SET NOT NULL;

