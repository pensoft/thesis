-- Function: spMoveDocumentUpDown(bigint, bigint, bigint, integer)

-- DROP FUNCTION spMoveDocumentUpDown(bigint, bigint, bigint, integer);

CREATE OR REPLACE FUNCTION spMoveDocumentUpDown(
	pJournalId bigint,
	pIssueId bigint,
	pDocumentId bigint,
	pDirection integer
)
  RETURNS integer AS
$BODY$
DECLARE
	lRec record;
	lRec2 record;
	lRes integer;
BEGIN
	lRes := 0;
	SELECT INTO lRec *
	FROM pjs.documents 
	WHERE issue_id = pIssueId 
		AND journal_id = pJournalId
		AND id = pDocumentId;
	
	IF lRec.id IS NOT NULL THEN
		IF pDirection = 1 THEN -- Move Up
			SELECT INTO lRec2 * 
			FROM pjs.documents 
			WHERE journal_id = pJournalId
				AND issue_id = pIssueId 
				AND issue_ord < lRec.issue_ord
			ORDER BY issue_ord DESC
			LIMIT 1;
			
			IF lRec2.id IS NOT NULL THEN
				UPDATE pjs.documents 
				SET start_page = lRec2.start_page, 
					end_page = lRec2.start_page + number_of_pages - 1, 
					issue_ord = lRec2.issue_ord
				WHERE id = lRec.id;
				
				-- Polzvame si napravo tazi promenliva za izchisleniq na start_page na vtori po red document
				lRec2.start_page := lRec2.start_page + lRec.number_of_pages;
				
				IF (lRec2.start_page) % 2 = 0 THEN
					lRec2.start_page := lRec2.start_page + 1;
				END IF;
					
				UPDATE pjs.documents 
				SET start_page = lRec2.start_page, 
					end_page = lRec2.start_page + number_of_pages -1, 
					issue_ord = lRec.issue_ord
				WHERE id = lRec2.id;
				
				lRes := 1;
			END IF;
			
		ELSEIF pDirection = 2 THEN -- Move Down
			SELECT INTO lRec2 * 
			FROM pjs.documents 
			WHERE journal_id = pJournalId
				AND issue_id = pIssueId 
				AND issue_ord > lRec.issue_ord
			ORDER BY issue_ord ASC
			LIMIT 1;
			
			IF lRec2.id IS NOT NULL THEN
				UPDATE pjs.documents 
				SET start_page = lRec.start_page, 
					end_page = lRec.start_page + number_of_pages - 1, 
					issue_ord = lRec.issue_ord
				WHERE id = lRec2.id;

				-- Polzvame si napravo tazi promenliva za izchisleniq na start_page na vtori po red document
				lRec.start_page := lRec.start_page + lRec2.number_of_pages;

				IF (lRec.start_page) % 2 = 0 THEN
					lRec.start_page := lRec.start_page + 1;
				END IF;
					
				UPDATE pjs.documents 
				SET start_page = lRec.start_page, 
					end_page = lRec.start_page + number_of_pages -1, 
					issue_ord = lRec2.issue_ord
				WHERE id = lRec.id;

				lRes := 1;
			END IF;
		END IF;
	END IF;
	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spMoveDocumentUpDown(bigint, bigint, bigint, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spMoveDocumentUpDown(bigint, bigint, bigint, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spMoveDocumentUpDown(bigint, bigint, bigint, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spMoveDocumentUpDown(bigint, bigint, bigint, integer) TO pensoft;