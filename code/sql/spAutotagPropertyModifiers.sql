DROP TYPE ret_spAutotagPropertyModifiers CASCADE;
CREATE TYPE ret_spAutotagPropertyModifiers AS (
	id int,
	name varchar
);

CREATE OR REPLACE FUNCTION spAutotagPropertyModifiers(
	pOper int,
	pId int,
	pName varchar
)
  RETURNS ret_spAutotagPropertyModifiers AS
$BODY$
DECLARE
lRes ret_spAutotagPropertyModifiers;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO autotag_property_modifiers(name) VALUES (pName);
		lId = currval('autotag_property_modifiers_id_seq');
	ELSE -- Update
		UPDATE autotag_property_modifiers SET
			name = pName
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM autotag_property_modifiers WHERE id = lId;

END IF;


SELECT INTO lRes id, name FROM autotag_property_modifiers WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spAutotagPropertyModifiers(
	pOper int,
	pId int,
	pName varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagPropertyModifiers(
	pOper int,
	pId int,
	pName varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagPropertyModifiers(
	pOper int,
	pId int,
	pName varchar
) TO iusrpmt;
