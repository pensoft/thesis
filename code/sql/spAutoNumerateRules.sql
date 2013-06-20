DROP TYPE ret_spAutoNumerateRules CASCADE;
CREATE TYPE ret_spAutoNumerateRules AS (
	id int,
	name varchar,	
	xpath varchar,
	attribute_name varchar,
	starting_value int
);

CREATE OR REPLACE FUNCTION spAutoNumerateRules(
	pOper int,
	pId int,
	pName varchar,
	pXPath varchar,
	pAttributeName varchar,
	pStartingValue int
)
  RETURNS ret_spAutoNumerateRules AS
$BODY$
DECLARE
lRes ret_spAutoNumerateRules;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO auto_numerate_rules(name, xpath, attribute_name, starting_value) VALUES (pName, pXPath, pAttributeName, pStartingValue);
		lId = currval('auto_numerate_rules_id_seq');
	ELSE -- Update
		UPDATE auto_numerate_rules SET
			name = pName,
			xpath = pXPath,
			attribute_name = pAttributeName,
			starting_value = pStartingValue
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM auto_numerate_rules WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes id, name, xpath, attribute_name, starting_value FROM auto_numerate_rules WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spAutoNumerateRules(
	pOper int,
	pId int,
	pName varchar,
	pXPath varchar,
	pAttributeName varchar,
	pStartingValue int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spAutoNumerateRules(
	pOper int,
	pId int,
	pName varchar,
	pXPath varchar,
	pAttributeName varchar,
	pStartingValue int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spAutoNumerateRules(
	pOper int,
	pId int,
	pName varchar,
	pXPath varchar,
	pAttributeName varchar,
	pStartingValue int
) TO iusrpmt;
