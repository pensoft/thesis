CREATE TABLE wiki_login(
	id serial PRIMARY KEY,
	username varchar,
	password varchar
);

GRANT ALL ON TABLE wiki_login TO iusrpmt;

INSERT INTO wiki_login(username, password) VALUES ('ZooKeys', '123123123');

INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES (53, '/dict/wiki_login/', 'Wiki login', 3, 13, 1);
ALTER SEQUENCE secsites_id_seq RESTART WITH 54;


INSERT INTO secgrpacc(gid, sid, type) VALUES (2, 53, 6);

ALTER TABLE wiki_export ADD COLUMN wiki_username_id int REFERENCES wiki_login(id);
UPDATE wiki_export SET wiki_username_id = 1;


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
		INSERT INTO wiki_export(title, article_id, createuid, createdate, lastmod, xml, wiki_username_id) VALUES (pTitle, pArticleId, pCreateUid, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, pXml, pWikiUsernameId);
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


SELECT INTO lRes id, title, article_id, createuid, createdate, lastmod, xml, is_uploaded, upload_time, upload_has_errors, upload_msg, wiki_username_id FROM wiki_export WHERE id = lId;


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
) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spWikiExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pWikiUsernameId int,
	pCreateUid int
) TO postgres;
GRANT EXECUTE ON FUNCTION spWikiExport(
	pOper int,
	pId int,
	pTitle varchar,
	pArticleId int,
	pXml varchar,
	pWikiUsernameId int,
	pCreateUid int
) TO iusrpmt;



DROP TYPE ret_spWikiLogin CASCADE;
CREATE TYPE ret_spWikiLogin AS (
	id int,
	username varchar,
	password varchar
);

CREATE OR REPLACE FUNCTION spWikiLogin(
	pOper int,
	pId int,
	pUsername varchar,
	pPassword varchar
)
  RETURNS ret_spWikiLogin AS
$BODY$
DECLARE
lRes ret_spWikiLogin;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO wiki_login(username, password) VALUES (pUsername, pPassword);
		lId = currval('wiki_login_id_seq');
	ELSE -- Update
		UPDATE wiki_login SET
			username = pUsername,
			password = pPassword
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM wiki_login WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes id, username, password FROM wiki_login WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spWikiLogin(
	pOper int,
	pId int,
	pUsername varchar,
	pPassword varchar
) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION spWikiLogin(
	pOper int,
	pId int,
	pUsername varchar,
	pPassword varchar
) TO postgres;
GRANT EXECUTE ON FUNCTION spWikiLogin(
	pOper int,
	pId int,
	pUsername varchar,
	pPassword varchar
) TO iusrpmt;
