-- Function: spDeleteJournalIssue(bigint, bigint)

-- DROP FUNCTION spDeleteJournalIssue(bigint, bigint);

CREATE OR REPLACE FUNCTION spDeleteJournalIssue(
	pIssueId bigint,
	pJournalId bigint
)
  RETURNS integer AS
$BODY$
DECLARE
	lIsCurrent boolean;
	lIssueId bigint;
	lRes bigint;
BEGIN
	SELECT INTO lIsCurrent, lIssueId, lRes is_current, id, previewpicid FROM pjs.journal_issues WHERE id = pIssueId AND journal_id = pJournalId;
	IF lIssueId IS NOT NULL THEN
		UPDATE pjs.documents SET issue_id = NULL WHERE issue_id = pIssueId;
		DELETE FROM pjs.journal_issues WHERE id = pIssueId;
		-- If deleted issue is current we must make last back issue current
		IF lIsCurrent = TRUE THEN
			SELECT INTO lIssueId id FROM pjs.journal_issues 
			WHERE is_published = TRUE 
				AND journal_id = pJournalId
			ORDER BY date_published DESC
			LIMIT 1;
			UPDATE pjs.journal_issues SET is_current = TRUE WHERE id = lIssueId;
		END IF;
	END IF;
	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spDeleteJournalIssue(bigint, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spDeleteJournalIssue(bigint, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION spDeleteJournalIssue(bigint, bigint) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spDeleteJournalIssue(bigint, bigint) TO pensoft;

