INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES (52, '/resources/wiki_export/', 'Wiki export', 3, 13, 1);
ALTER SEQUENCE secsites_id_seq RESTART WITH 53;

INSERT INTO secgrpacc(gid, sid, type) VALUES (2, 52, 6);

CREATE TABLE wiki_export
(
  id serial NOT NULL,
  title character varying,
  article_id integer,  
  createdate timestamp without time zone,
  lastmod timestamp without time zone,
  createuid integer,
  "xml" character varying,
  is_uploaded integer DEFAULT 0,
  upload_time timestamp without time zone,
  upload_has_errors integer DEFAULT 0,
  upload_msg text,
  CONSTRAINT wiki_export_pkey PRIMARY KEY (id),
  CONSTRAINT wiki_export_createuid_fkey FOREIGN KEY (createuid)
      REFERENCES usr (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE wiki_export OWNER TO postgres;
GRANT ALL ON TABLE wiki_export TO postgres;
GRANT ALL ON TABLE wiki_export TO iusrpmt;

DROP TYPE ret_spWikiExportReport CASCADE;
CREATE TYPE ret_spWikiExportReport AS (
	result int
);

CREATE OR REPLACE FUNCTION spWikiExportReport(
	pExportId int,
	pHasErrors int,	
	pReportMsg varchar
)
  RETURNS ret_spWikiExportReport AS
$BODY$
DECLARE
lRes ret_spWikiExportReport;

BEGIN

UPDATE wiki_export SET
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
ALTER FUNCTION spWikiExportReport(
	pExportId int,
	pHasErrors int,
	pReportMsg varchar
) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spWikiExportReport(
	pExportId int,
	pHasErrors int,
	pReportMsg varchar
) TO postgres;
GRANT EXECUTE ON FUNCTION spWikiExportReport(
	pExportId int,
	pHasErrors int,
	pReportMsg varchar
) TO iusrpmt;

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
	upload_msg text
);

CREATE OR REPLACE FUNCTION spWikiExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
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
		INSERT INTO wiki_export(title, article_id, createuid, createdate, lastmod, xml) VALUES (pTitle, pArticleId, pCreateUid, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, pXml);
		lId = currval('wiki_export_id_seq');
	ELSE -- Update
		UPDATE wiki_export SET
			title = pTitle,
			article_id = pArticleId,
			lastmod = CURRENT_TIMESTAMP,
			xml = pXml
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM wiki_export WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes id, title, article_id, createuid, createdate, lastmod, xml, is_uploaded, upload_time, upload_has_errors, upload_msg FROM wiki_export WHERE id = lId;


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
	pCreateUid int
) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spWikiExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pCreateUid int
) TO postgres;
GRANT EXECUTE ON FUNCTION spWikiExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pCreateUid int
) TO iusrpmt;
