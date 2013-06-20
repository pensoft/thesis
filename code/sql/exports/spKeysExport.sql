DROP TYPE ret_spKeysExport CASCADE;
CREATE TYPE ret_spKeysExport AS (
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

CREATE OR REPLACE FUNCTION spKeysExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pCreateUid int
)
  RETURNS ret_spKeysExport AS
$BODY$
DECLARE
lRes ret_spKeysExport;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO keys_export(title, article_id, createuid, createdate, lastmod, xml) 
				VALUES (pTitle, pArticleId, pCreateUid, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, pXml);
		lId = currval('export_common_id_seq');
	ELSE -- Update
		UPDATE keys_export SET
			title = pTitle,
			article_id = pArticleId,
			lastmod = CURRENT_TIMESTAMP,
			xml = pXml
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM keys_export WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes 
	id, title, article_id, createuid, createdate, lastmod, xml, is_uploaded, upload_time, upload_has_errors, upload_msg, is_generated, has_results 
FROM keys_export 
WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spKeysExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pCreateUid int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spKeysExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pCreateUid int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spKeysExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pCreateUid int
) TO iusrpmt;
