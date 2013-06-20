DROP TYPE ret_spUploadExport CASCADE;

CREATE TYPE ret_spUploadExport AS (
	export_id int,
	export_type int
);

CREATE OR REPLACE FUNCTION spUploadExport(
	pExportId int,
	pPid int
)
  RETURNS ret_spUploadExport AS
$BODY$
DECLARE
	lRes ret_spUploadExport;
	lUploadStarted int;
	lIsUploaded int;
	lUploadPid int;
BEGIN
	SELECT INTO lRes
			e.id, e.type_id
		FROM export_common e		
	WHERE e.id = pExportId;

	SELECT INTO lUploadStarted, lIsUploaded, lUploadPid 			
		upload_started, is_uploaded, upload_pid
	FROM export_common e		
	WHERE e.id = pExportId;
	
	IF (lUploadStarted = 1 AND lUploadPid <> pPid ) OR lIsUploaded = 1 THEN
		RAISE EXCEPTION 'admin.exports.exportIsBeingUploadedOrIsAlreadyUploaded';
	ELSE
		UPDATE export_common SET 
			upload_started = 1,
			upload_pid = pPid
		WHERE id = pExportId;
	END IF;
	
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spUploadExport(
	pExportId int,
	pPid int
) TO iusrpmt;
