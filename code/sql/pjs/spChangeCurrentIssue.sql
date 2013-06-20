-- Function: spChangeCurrentIssue(bigint, bigint)

-- DROP FUNCTION spChangeCurrentIssue(bigint, bigint);

CREATE OR REPLACE FUNCTION spChangeCurrentIssue(
	pIssueId bigint,
	pJournalId bigint
)
  RETURNS integer AS
$BODY$
DECLARE
	lRes bigint;
BEGIN
	lRes := 0;
	IF EXISTS (SELECT id FROM pjs.journal_issues WHERE id = pIssueId AND journal_id = pJournalId) THEN
		UPDATE pjs.journal_issues SET is_current = FALSE WHERE journal_id = pJournalId;
		UPDATE pjs.journal_issues SET is_current = TRUE WHERE id = pIssueId AND journal_id = pJournalId;
		lRes := pIssueId;
	END IF;
	
	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spChangeCurrentIssue(bigint, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spChangeCurrentIssue(bigint, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION spChangeCurrentIssue(bigint, bigint) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spChangeCurrentIssue(bigint, bigint) TO pensoft;

