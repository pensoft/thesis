-- Function: spDeleteDocumentFromIssue(bigint, bigint, bigint)

-- DROP FUNCTION spDeleteDocumentFromIssue(bigint, bigint, bigint);

CREATE OR REPLACE FUNCTION spDeleteDocumentFromIssue(
	pJournalId bigint,
	pIssueId bigint,
	pDocumentId bigint
)
  RETURNS integer AS
$BODY$
DECLARE
	lIssueId bigint;
	lDocumentId bigint;
	lStartPage integer;
	lEndPage integer;
	lDelIssueOrd integer;
	lRec record;
	lRes integer;
BEGIN
	lRes := 0;
	SELECT INTO lDocumentId, lEndPage, lDelIssueOrd id, start_page, issue_ord
	FROM pjs.documents 
	WHERE issue_id = pIssueId 
		AND journal_id = pJournalId
		AND id = pDocumentId;
	lEndPage := lEndPage - 1; -- shtoto dolu pribavqme 1
	IF lDocumentId IS NOT NULL THEN
		FOR lRec IN
			SELECT * FROM pjs.documents 
			WHERE issue_id = pIssueId 
				AND journal_id = pJournalId 
				AND issue_ord > lDelIssueOrd
			ORDER BY issue_ord ASC
		LOOP
			lEndPage := lEndPage + 1;
			IF lEndPage % 2 = 0 THEN
				lEndPage := lEndPage + 1;
			END IF;
			UPDATE pjs.documents 
			SET start_page = lEndPage, 
				end_page = lEndPage + number_of_pages - 1, 
				issue_ord = issue_ord - 1 
			WHERE id = lRec.id;
			SELECT INTO lStartPage, lEndPage start_page, end_page FROM pjs.documents WHERE id = lRec.id;
		END LOOP;
		UPDATE pjs.documents SET start_page = 1, end_page = 1, issue_id = NULL, issue_ord = 1 WHERE id = lDocumentId;
		lRes := 1;
	END IF;
	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spDeleteDocumentFromIssue(bigint, bigint, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spDeleteDocumentFromIssue(bigint, bigint, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION spDeleteDocumentFromIssue(bigint, bigint, bigint) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spDeleteDocumentFromIssue(bigint, bigint, bigint) TO pensoft;

