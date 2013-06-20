ALTER TABLE journals ADD COLUMN keys_apikey character varying;

DROP TYPE ret_spJournals CASCADE;
CREATE TYPE ret_spJournals AS (
	id int,
	name varchar,
	pensoft_id int,
	pensoft_title varchar,
	xml_file_name varchar,
	title_abrev varchar,
	issn_print varchar,
	issn_online varchar,
	publisher varchar,
	keys_apikey varchar
);

CREATE OR REPLACE FUNCTION spJournals(
	pOper int,
	pId int,
	pName varchar,
	pPensoftId int,
	pPensoftTitle varchar,
	pXmlFileName varchar,
	pTitleAbrev varchar,
	pIssnPrint varchar,
	pIssnOnline varchar,
	pPublisher varchar,
	pApiKey varchar
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
		INSERT INTO journals(
			name, pensoft_id, pensoft_title, xml_file_name,
			title_abrev, issn_print, issn_online, publisher, keys_apikey
		) VALUES (
			pName, pPensoftId, pPensoftTitle, pXmlFileName,
			pTitleAbrev, pIssnPrint, pIssnOnline, pPublisher, pApiKey
		);
		lId = currval('journals_id_seq');
	ELSE -- Update
		UPDATE journals SET
			name = pName,
			pensoft_id = pPensoftId,
			pensoft_title = pPensoftTitle,
			xml_file_name = pXmlFileName,
			title_abrev = pTitleAbrev,
			issn_print = pIssnPrint,
			issn_online = pIssnOnline,
			publisher = pPublisher, 
			keys_apikey = pApiKey
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM journals WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes 
	id, name, pensoft_id, pensoft_title, xml_file_name, title_abrev, issn_print, issn_online, publisher, keys_apikey 
FROM journals 
WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spJournals(
	pOper int,
	pId int,
	pName varchar,
	pPensoftId int,
	pPensoftTitle varchar,
	pXmlFileName varchar,
	pTitleAbrev varchar,
	pIssnPrint varchar,
	pIssnOnline varchar,
	pPublisher varchar,
	pApiKey varchar
) TO iusrpmt;
