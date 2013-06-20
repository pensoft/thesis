DROP TYPE ret_spExportReport CASCADE;
CREATE TYPE ret_spExportReport AS (
	result int
);

CREATE OR REPLACE FUNCTION spExportReport(
	pExportId int,
	pHasErrors int,	
	pReportMsg varchar
)
  RETURNS ret_spExportReport AS
$BODY$
DECLARE
lRes ret_spExportReport;

BEGIN

UPDATE export_common SET
	is_uploaded = 1,
	upload_time = CURRENT_TIMESTAMP,
	upload_has_errors = pHasErrors,
	upload_msg = pReportMsg
WHERE id = pExportId;


lRes.result = 1;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spExportReport(
	pExportId int,
	pHasErrors int,
	pReportMsg varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spExportReport(
	pExportId int,
	pHasErrors int,
	pReportMsg varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spExportReport(
	pExportId int,
	pHasErrors int,
	pReportMsg varchar
) TO iusrpmt;
