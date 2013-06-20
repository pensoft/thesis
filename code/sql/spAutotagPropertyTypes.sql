DROP TYPE ret_spAutotagPropertyTypes CASCADE;
CREATE TYPE ret_spAutotagPropertyTypes AS (
	id int,
	name varchar
);

CREATE OR REPLACE FUNCTION spAutotagPropertyTypes(
	pOper int,
	pId int,
	pName varchar
)
  RETURNS ret_spAutotagPropertyTypes AS
$BODY$
DECLARE
lRes ret_spAutotagPropertyTypes;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO autotag_property_types(name) VALUES (pName);
		lId = currval('autotag_property_types_id_seq');
	ELSE -- Update
		UPDATE autotag_property_types SET
			name = pName
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM autotag_property_types WHERE id = lId;

END IF;


SELECT INTO lRes id, name FROM autotag_property_types WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spAutotagPropertyTypes(
	pOper int,
	pId int,
	pName varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagPropertyTypes(
	pOper int,
	pId int,
	pName varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagPropertyTypes(
	pOper int,
	pId int,
	pName varchar
) TO iusrpmt;
