DROP TYPE ret_spEolExport CASCADE;
CREATE TYPE ret_spEolExport AS (
	id int,
	title varchar,	
	article_id int,
	createuid int,
	createdate timestamp,
	lastmod timestamp,
	xml varchar,
	is_uploaded integer,
	upload_time timestamp,
	upload_has_errors integer,
	upload_msg text,
	is_generated int,
	has_results int
);

CREATE OR REPLACE FUNCTION spEolExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
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
		INSERT INTO eol_export(title, article_id, createuid, createdate, lastmod, xml) 
				VALUES (pTitle, pArticleId, pCreateUid, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, pXml);
		lId = currval('export_common_id_seq');
	ELSE -- Update
		UPDATE eol_export SET
			title = pTitle,
			article_id = pArticleId,
			lastmod = CURRENT_TIMESTAMP,
			xml = pXml
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM eol_export WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes 
	id, title, article_id, createuid, createdate, lastmod, xml, is_uploaded, upload_time, upload_has_errors, upload_msg, is_generated, has_results 
FROM eol_export 
WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spEolExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pCreateUid int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spEolExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pCreateUid int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spEolExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pCreateUid int
) TO iusrpmt;
