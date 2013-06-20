DROP TYPE ret_spAutotagReVariableTypes CASCADE;
CREATE TYPE ret_spAutotagReVariableTypes AS (
	id int,
	name varchar
);

CREATE OR REPLACE FUNCTION spAutotagReVariableTypes(
	pOper int,
	pId int,
	pName varchar
)
  RETURNS ret_spAutotagReVariableTypes AS
$BODY$
DECLARE
lRes ret_spAutotagReVariableTypes;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO autotag_re_variable_types(name) VALUES (pName);
		lId = currval('autotag_re_variable_types_id_seq');
	ELSE -- Update
		UPDATE autotag_re_variable_types SET
			name = pName
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM autotag_re_variable_types WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy
	
END IF;


SELECT INTO lRes id, name FROM autotag_re_variable_types WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spAutotagReVariableTypes(
	pOper int,
	pId int,
	pName varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagReVariableTypes(
	pOper int,
	pId int,
	pName varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagReVariableTypes(
	pOper int,
	pId int,
	pName varchar
) TO iusrpmt;
