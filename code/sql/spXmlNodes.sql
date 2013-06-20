DROP TYPE ret_spXmlNodes CASCADE;
CREATE TYPE ret_spXmlNodes AS (
	id int,
	name varchar,	
	createdate timestamp,
	autotag_annotate_show int
);

CREATE OR REPLACE FUNCTION spXmlNodes(
	pOper int,
	pId int,
	pName varchar,
	pAutotagAnnotateShow int
)
  RETURNS ret_spXmlNodes AS
$BODY$
DECLARE
lRes ret_spXmlNodes;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update
	IF EXISTS ( SELECT * FROM xml_nodes WHERE name = pName AND id <> pId ) THEN
		RAISE EXCEPTION 'admin.xml_nodes.such_node_exists';
	END IF;
	IF lId IS NULL THEN --Insert
		INSERT INTO xml_nodes(name, autotag_annotate_show) VALUES (pName, pAutotagAnnotateShow);
		lId = currval('xml_nodes_id_seq');
	ELSE -- Update
		UPDATE xml_nodes SET
			name = pName,
			autotag_annotate_show = pAutotagAnnotateShow
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM xml_nodes WHERE id = lId;

END IF;


SELECT INTO lRes id, name, createdate, autotag_annotate_show FROM xml_nodes WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spXmlNodes(
	pOper int,
	pId int,
	pName varchar,
	pAutotagAnnotateShow int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spXmlNodes(
	pOper int,
	pId int,
	pName varchar,
	pAutotagAnnotateShow int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spXmlNodes(
	pOper int,
	pId int,
	pName varchar,
	pAutotagAnnotateShow int
) TO iusrpmt;
