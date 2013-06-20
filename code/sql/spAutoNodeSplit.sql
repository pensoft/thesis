DROP TYPE ret_spAutoNodeSplit CASCADE;
CREATE TYPE ret_spAutoNodeSplit AS (
	id int,
	name varchar,	
	xpath varchar,
	climb_up int
);

CREATE OR REPLACE FUNCTION spAutoNodeSplit(
	pOper int,
	pId int,
	pName varchar,
	pXPath varchar,
	pClimbUp int
)
  RETURNS ret_spAutoNodeSplit AS
$BODY$
DECLARE
lRes ret_spAutoNodeSplit;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO node_split(name, xpath, climb_up) VALUES (pName, pXPath, pClimbUp);
		lId = currval('node_split_id_seq');
	ELSE -- Update
		UPDATE node_split SET
			name = pName,
			xpath = pXPath,
			climb_up = pClimbUp
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM node_split WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes id, name, xpath, climb_up FROM node_split WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spAutoNodeSplit(
	pOper int,
	pId int,
	pName varchar,
	pXPath varchar,
	pClimbUp int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spAutoNodeSplit(
	pOper int,
	pId int,
	pName varchar,
	pXPath varchar,
	pClimbUp int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spAutoNodeSplit(
	pOper int,
	pId int,
	pName varchar,
	pXPath varchar,
	pClimbUp int
) TO iusrpmt;
