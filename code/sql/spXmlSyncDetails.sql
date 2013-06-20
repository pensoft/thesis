DROP TYPE ret_spXmlSyncDetails CASCADE;


CREATE TYPE ret_spXmlSyncDetails AS (
	id int,
	xml_sync_templates_id int,
	name varchar,
	xpath varchar,
	sync_type int,
	sync_column_name varchar,
	sync_column_default_value varchar
);

CREATE OR REPLACE FUNCTION spXmlSyncDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pXPath varchar,
	pSyncType int,
	pSyncColumnName varchar,
	pSyncColumnDefaultValue varchar
	
)
  RETURNS ret_spXmlSyncDetails AS
$BODY$
DECLARE
lRes ret_spXmlSyncDetails;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO xml_sync_details(xml_sync_templates_id, name, xpath, sync_type, sync_column_name, sync_column_default_value) VALUES ( pTemplateId, pName, pXPath, pSyncType, pSyncColumnName, pSyncColumnDefaultValue);
		lId = currval('xml_sync_details_id_seq');
	ELSE -- Update
		UPDATE xml_sync_details SET
			name = pName,
			xml_sync_templates_id = pTemplateId, 
			xpath = pXPath,
			sync_type = pSyncType,
			sync_column_name = pSyncColumnName,
			sync_column_default_value = pSyncColumnDefaultValue
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM xml_sync_details WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy
	
END IF;


SELECT INTO lRes id, xml_sync_templates_id, name, xpath, sync_type, sync_column_name, sync_column_default_value FROM xml_sync_details WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spXmlSyncDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pXPath varchar,
	pSyncType int,
	pSyncColumnName varchar,
	pSyncColumnDefaultValue varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spXmlSyncDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pXPath varchar,
	pSyncType int,
	pSyncColumnName varchar,
	pSyncColumnDefaultValue varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spXmlSyncDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pXPath varchar,
	pSyncType int,
	pSyncColumnName varchar,
	pSyncColumnDefaultValue varchar
) TO iusrpmt;
