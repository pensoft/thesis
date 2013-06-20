INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES 
	(49, '/resources/eol_export/', 'Eol Export', 3, 12, 1);	

INSERT INTO secgrpacc (sid, gid, type) VALUES 	
	(49, 2, 6);

INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES 
	(50, '/dict/journals/', 'Journals', 3, 12, 1);	

INSERT INTO secgrpacc (sid, gid, type) VALUES 	
	(50, 2, 6);

ALTER SEQUENCE secsites_id_seq RESTART WITH 51;


CREATE TABLE eol_export
(
  id serial NOT NULL,
  title character varying,
  journal_id integer,
  issue_id integer,
  createdate timestamp without time zone,
  lastmod timestamp without time zone,
  createuid integer,
  "xml" character varying,
  appended_to_xml_file integer DEFAULT 0,
  time_appended_to_xml_file timestamp without time zone,
  CONSTRAINT eol_export_pkey PRIMARY KEY (id),
  CONSTRAINT eol_export_createuid_fkey FOREIGN KEY (createuid)
      REFERENCES usr (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
GRANT ALL ON TABLE eol_export TO iusrpmt;

CREATE TABLE journals(
	id serial NOT NULL,
	"name" character varying,
	pensoft_id integer NOT NULL,
	pensoft_title character varying NOT NULL,
	xml_file_name character varying,
	CONSTRAINT journals_pkey PRIMARY KEY (id)
);
GRANT ALL ON TABLE journals TO iusrpmt;

DROP TYPE ret_spJournals CASCADE;
CREATE TYPE ret_spJournals AS (
	id int,
	name varchar,
	pensoft_id int,
	pensoft_title varchar,
	xml_file_name varchar
);

CREATE OR REPLACE FUNCTION spJournals(
	pOper int,
	pId int,
	pName varchar,
	pPensoftId int,
	pPensoftTitle varchar,
	pXmlFileName varchar
)
  RETURNS ret_spJournals AS
$BODY$
DECLARE
lRes ret_spJournals;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO journals(name, pensoft_id, pensoft_title, xml_file_name) VALUES (pName, pPensoftId, pPensoftTitle, pXmlFileName);
		lId = currval('journals_id_seq');
	ELSE -- Update
		UPDATE journals SET
			name = pName,
			pensoft_id = pPensoftId,
			pensoft_title = pPensoftTitle,
			xml_file_name = pXmlFileName
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM journals WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes id, name, pensoft_id, pensoft_title, xml_file_name FROM journals WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spJournals(
	pOper int,
	pId int,
	pName varchar,
	pPensoftId int,
	pPensoftTitle varchar,
	pXmlFileName varchar
) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spJournals(
	pOper int,
	pId int,
	pName varchar,
	pPensoftId int,
	pPensoftTitle varchar,
	pXmlFileName varchar
) TO postgres;
GRANT EXECUTE ON FUNCTION spJournals(
	pOper int,
	pId int,
	pName varchar,
	pPensoftId int,
	pPensoftTitle varchar,
	pXmlFileName varchar
) TO iusrpmt;





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
ALTER FUNCTION spEolExport(
	pOper int,
	pId int,
	pTitle varchar,
	pJournalId int,
	pIssueId int,
	pXml varchar,
	pCreateUid int
) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spEolExport(
	pOper int,
	pId int,
	pTitle varchar,
	pJournalId int,
	pIssueId int,
	pXml varchar,
	pCreateUid int
) TO postgres;
GRANT EXECUTE ON FUNCTION spEolExport(
	pOper int,
	pId int,
	pTitle varchar,
	pJournalId int,
	pIssueId int,
	pXml varchar,
	pCreateUid int
) TO iusrpmt;
