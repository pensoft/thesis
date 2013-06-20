DROP TYPE ret_spXmlSyncTemplates CASCADE;
CREATE TYPE ret_spXmlSyncTemplates AS (
	id int,
	name varchar
);

CREATE OR REPLACE FUNCTION spXmlSyncTemplates(
	pOper int,
	pId int,
	pName varchar
)
  RETURNS ret_spXmlSyncTemplates AS
$BODY$
DECLARE
lRes ret_spXmlSyncTemplates;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO xml_sync_templates(name) VALUES (pName);
		lId = currval('xml_sync_templates_id_seq');
	ELSE -- Update
		UPDATE xml_sync_templates SET
			name = pName
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM xml_sync_templates WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy
	
END IF;


SELECT INTO lRes id, name FROM xml_sync_templates WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spXmlSyncTemplates(
	pOper int,
	pId int,
	pName varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spXmlSyncTemplates(
	pOper int,
	pId int,
	pName varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spXmlSyncTemplates(
	pOper int,
	pId int,
	pName varchar
) TO iusrpmt;
