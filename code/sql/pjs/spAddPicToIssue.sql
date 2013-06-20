-- Function: spAddPicToIssue(bigint, bigint)

-- DROP FUNCTION spAddPicToIssue(bigint, bigint);

CREATE OR REPLACE FUNCTION spAddPicToIssue(
	pPicId bigint,
	pIssueId bigint
)
  RETURNS integer AS
$BODY$
DECLARE
	lIssueId bigint;
	lOldPicId bigint;
BEGIN
	SELECT INTO lIssueId, lOldPicId id, previewpicid FROM pjs.journal_issues WHERE id = pIssueId;
	IF lIssueId IS NOT NULL THEN
		
		UPDATE pjs.journal_issues SET previewpicid = pPicId WHERE id = pIssueId;
		
	END IF;
	RETURN lOldPicId;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION spAddPicToIssue(bigint, bigint) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spAddPicToIssue(bigint, bigint) TO postgres;
GRANT EXECUTE ON FUNCTION spAddPicToIssue(bigint, bigint) TO iusrpmt;
GRANT EXECUTE ON FUNCTION spAddPicToIssue(bigint, bigint) TO pensoft;

