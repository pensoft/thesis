ALTER TABLE export_types ADD COLUMN format_xml int DEFAULT 1;
UPDATE export_types SET format_xml = 0 WHERE id = 2;



DROP TYPE ret_spGenerateExport CASCADE;

CREATE TYPE ret_spGenerateExport AS (
	article_id int,
	xsl_file varchar,
	results_xpath_expr varchar,
	format_xml int
);

CREATE OR REPLACE FUNCTION spGenerateExport(
	pExportId int,
	pSaveToDb int,	
	pStrict int,
	pPid int
)
  RETURNS ret_spGenerateExport AS
$BODY$
DECLARE
	lRes ret_spGenerateExport;
	lGenerateStarted int;
	lIsGenerated int;
	lGeneratePid int;
BEGIN
	SELECT INTO lRes
			e.article_id, t.xsl_file, t.results_xpath_expr, t.format_xml
		FROM export_common e
		JOIN export_types t ON t.id = e.type_id
	WHERE e.id = pExportId;

	IF pSaveToDb > 0 AND pStrict > 0 THEN 
		SELECT INTO lGenerateStarted, lIsGenerated, lGeneratePid 			
			generating_started, is_generated, generate_pid
		FROM export_common e		
		WHERE e.id = pExportId;
		
		IF (lGenerateStarted = 1 AND lGeneratePid <> pPid ) OR lIsGenerated = 1 THEN
			RAISE EXCEPTION 'admin.exports.exportIsBeingGeneratedOrIsAlreadyGenerated';
		ELSE
			UPDATE export_common SET 
				generating_started = 1,
				generate_pid = pPid
			WHERE id = pExportId;
		END IF;
	ELSEIF pSaveToDb > 0 THEN
		UPDATE export_common SET 
			generating_started = 1,
			generate_pid = pPid
		WHERE id = pExportId;
	END IF;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGenerateExport(
	pExportId int,
	pSaveToDb int,	
	pStrict int,
	pPid int
) TO iusrpmt;
