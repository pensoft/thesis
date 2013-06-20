-- Function: spSaveJournalIssue(bigint, bigint, integer, integer, integer, integer, character varying, character varying, character varying, numeric, integer)

-- DROP FUNCTION spSaveJournalIssue(bigint, bigint, integer, integer, integer, integer, character varying, character varying, character varying, numeric, integer);

CREATE OR REPLACE FUNCTION spSaveJournalIssue(
	pIssueId bigint,
	pJournalId bigint, 
	pVolume integer,
	pNumber integer,
	pYear integer,
	pIsRegularIssue integer,
	pSpecial_issue_editors character varying,
	pTitle character varying,
	pDescription character varying,
	pPrice numeric,
	pIsActive integer
)
  RETURNS integer AS
$BODY$
DECLARE
	lIsRegular boolean;
	lIsActive boolean;
	lRes bigint;
BEGIN
	IF pIsRegularIssue = 1 THEN
		lIsRegular := TRUE;
	ELSE
		lIsRegular := FALSE;
	END IF;
	IF pIsActive = 1 THEN
		lIsActive := TRUE;
	ELSE
		lIsActive := FALSE;
	END IF;
	IF pIssueId IS NOT NULL THEN -- Update Issue
		UPDATE pjs.journal_issues 
			SET
				name = coalesce(pTitle, name),
				description = coalesce(pDescription, description),
				is_regular_issue = coalesce(pIsRegularIssue::boolean, false),
				number = coalesce(pNumber, number),
				volume = coalesce(pVolume, volume),
				year = coalesce(pYear, year),
				price = coalesce(pPrice, price),
				special_issue_editors = coalesce(pSpecial_issue_editors, special_issue_editors),
				is_active = coalesce(pIsActive::boolean, false)
			WHERE id = pIssueId;
		lRes := pIssueId;
	ELSE -- Insert New Issue
		INSERT INTO pjs.journal_issues (journal_id, name, description, is_regular_issue, number, volume, year, price, special_issue_editors, is_active)
								VALUES(pJournalId, pTitle, pDescription, lIsRegular, pNumber, pVolume, pYear, coalesce(pPrice, 0), pSpecial_issue_editors, lIsActive);
		lRes := currval('pjs.journal_issues_id_seq');
	END IF;
	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spSaveJournalIssue(bigint, bigint, integer, integer, integer, integer, character varying, character varying, character varying, numeric, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spSaveJournalIssue(bigint, bigint, integer, integer, integer, integer, character varying, character varying, character varying, numeric, integer) TO postgres;
GRANT EXECUTE ON FUNCTION spSaveJournalIssue(bigint, bigint, integer, integer, integer, integer, character varying, character varying, character varying, numeric, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spSaveJournalIssue(bigint, bigint, integer, integer, integer, integer, character varying, character varying, character varying, numeric, integer) TO pensoft;

