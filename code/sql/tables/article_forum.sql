DROP TABLE IF EXISTS pjs.article_forum;

CREATE TABLE pjs.article_forum(
	id serial PRIMARY KEY,
	article_id bigint NOT NULL REFERENCES pjs.documents(id),
	message text NOT NULL,
	state int NOT NULL DEFAULT 0,
	createuid int NOT NULL,
	approveuid int,
	createdate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	approve_date timestamp
);

GRANT ALL ON TABLE pjs.article_forum TO iusrpmt;
COMMENT ON COLUMN pjs.article_forum.article_id IS 'Article ID (document_id)';
COMMENT ON COLUMN pjs.article_forum.state IS 'Message State. 0 - unapproved, 1 - approved, 2 - rejected';
COMMENT ON COLUMN pjs.article_forum.createdate IS 'Create date of the message';
COMMENT ON COLUMN pjs.article_forum.approve_date IS 'Approve date of the message';
COMMENT ON COLUMN pjs.article_forum.createuid IS 'Message creator';
COMMENT ON COLUMN pjs.article_forum.approveuid IS 'User who approved this message';