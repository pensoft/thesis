DROP TYPE ret_spXmlSyncTypes CASCADE;
CREATE TYPE ret_spXmlSyncTypes AS (
	id int,
	name varchar
);

CREATE OR REPLACE FUNCTION spXmlSyncTypes(
	pOper int,
	pId int,
	pName varchar
)
  RETURNS ret_spXmlSyncTypes AS
$BODY$
DECLARE
lRes ret_spXmlSyncTypes;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO xml_sync_types(name) VALUES (pName);
		lId = currval('xml_sync_types_id_seq');
	ELSE -- Update
		UPDATE xml_sync_types SET
			name = pName
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM xml_sync_types WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy
	
END IF;


SELECT INTO lRes id, name FROM xml_sync_types WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spXmlSyncTypes(
	pOper int,
	pId int,
	pName varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spXmlSyncTypes(
	pOper int,
	pId int,
	pName varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spXmlSyncTypes(
	pOper int,
	pId int,
	pName varchar
) TO iusrpmt;
