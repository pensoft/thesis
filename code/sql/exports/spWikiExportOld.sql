DROP TYPE ret_spWikiExport CASCADE;
CREATE TYPE ret_spWikiExport AS (
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
	wiki_username_id int
);

CREATE OR REPLACE FUNCTION spWikiExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pWikiUsernameId int,
	pCreateUid int
)
  RETURNS ret_spWikiExport AS
$BODY$
DECLARE
lRes ret_spWikiExport;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO wiki_export(title, article_id, createuid, createdate, lastmod, xml, wiki_username_id) 
				VALUES (pTitle, pArticleId, pCreateUid, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, pXml, pWikiUsernameId);
		lId = currval('wiki_export_id_seq');
	ELSE -- Update
		UPDATE wiki_export SET
			title = pTitle,
			article_id = pArticleId,
			lastmod = CURRENT_TIMESTAMP,
			xml = pXml,
			wiki_username_id = pWikiUsernameId
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM wiki_export WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes id, title, article_id, createuid, createdate, lastmod, xml, is_uploaded, upload_time, upload_has_errors, upload_msg, wiki_username_id 
FROM wiki_export 
WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spWikiExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pWikiUsernameId int,
	pCreateUid int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spWikiExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pWikiUsernameId int,
	pCreateUid int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spWikiExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pWikiUsernameId int,
	pCreateUid int
) TO iusrpmt;
