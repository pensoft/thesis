DROP TYPE ret_spXmlAttributes CASCADE;
CREATE TYPE ret_spXmlAttributes AS (
	id int,
	node_id int,
	name varchar,	
	createdate timestamp
);

CREATE OR REPLACE FUNCTION spXmlAttributes(
	pOper int,
	pId int,
	pNodeId int,
	pName varchar
)
  RETURNS ret_spXmlAttributes AS
$BODY$
DECLARE
lRes ret_spXmlAttributes;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update
	IF EXISTS ( SELECT * FROM xml_attributes WHERE name = pName AND node_id = pNodeId AND id <> pID ) THEN
		RAISE EXCEPTION 'admin.xml_attributes.such_attribute_exists';
	END IF;
	IF lId IS NULL THEN --Insert
		INSERT INTO xml_attributes(node_id, name) VALUES (pNodeId, pName);
		lId = currval('xml_attributes_id_seq');
	ELSE -- Update
		UPDATE xml_attributes SET
			node_id = pNodeId,
			name = pName
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM xml_attributes WHERE id = lId;

END IF;


SELECT INTO lRes id, node_id, name, createdate FROM xml_attributes WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spXmlAttributes(
	pOper int,
	pId int,
	pNodeId int,
	pName varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spXmlAttributes(
	pOper int,
	pId int,
	pNodeId int,
	pName varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spXmlAttributes(
	pOper int,
	pId int,
	pNodeId int,
	pName varchar
) TO iusrpmt;
