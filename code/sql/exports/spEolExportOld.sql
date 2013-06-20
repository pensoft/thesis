DROP TYPE ret_spEolExport CASCADE;
CREATE TYPE ret_spEolExport AS (
	id int,
	title varchar,	
	journal_id int,
	issue_id int,
	createuid int,
	createdate timestamp,
	lastmod timestamp,
	xml varchar,
	appended_to_xml_file int,
	time_appended_to_xml_file timestamp
);

CREATE OR REPLACE FUNCTION spEolExport(
	pOper int,
	pId int,
	pTitle varchar,
	pJournalId int,
	pIssueId int,
	pXml varchar,
	pCreateUid int
)
  RETURNS ret_spEolExport AS
$BODY$
DECLARE
lRes ret_spEolExport;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO eol_export(title, journal_id, issue_id, createuid, createdate, lastmod, xml) VALUES (pTitle, pJournalId, pIssueId, pCreateUid, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, pXml);
		lId = currval('eol_export_id_seq');
	ELSE -- Update
		UPDATE eol_export SET
			title = pTitle,
			journal_id = pJournalId,
			issue_id = pIssueId,
			xml = pXml,
			lastmod = CURRENT_TIMESTAMP
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM eol_export WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes id, title, journal_id, issue_id, createuid, createdate, lastmod, xml, appended_to_xml_file, time_appended_to_xml_file FROM eol_export WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spEolExport(
	pOper int,
	pId int,
	pTitle varchar,
	pJournalId int,
	pIssueId int,
	pXml varchar,
	pCreateUid int
) TO iusrpmt;
