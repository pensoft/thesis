DROP TYPE ret_spAutotagRules CASCADE;
CREATE TYPE ret_spAutotagRules AS (
	id int,
	name varchar
);

CREATE OR REPLACE FUNCTION spAutotagRules(
	pOper int,
	pId int,
	pName varchar
)
  RETURNS ret_spAutotagRules AS
$BODY$
DECLARE
lRes ret_spAutotagRules;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO autotag_rules(name) VALUES (pName);
		lId = currval('autotag_rules_id_seq');
	ELSE -- Update
		UPDATE autotag_rules SET
			name = pName
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM autotag_rules WHERE id = lId;

ELSEIF pOper = 4 THEN --Copy
	INSERT INTO autotag_rules(name) SELECT coalesce(name, '') || '_copy' FROM autotag_rules WHERE id = lId;
	lId = currval('autotag_rules_id_seq');
	
	INSERT INTO autotag_rules_properties(rule_id, property_id, property_modifier_id, property_type_id, priority) 
		SELECT lId, property_id, property_modifier_id, property_type_id, priority
		FROM autotag_rules_properties WHERE rule_id = pId;
	

END IF;


SELECT INTO lRes id, name FROM autotag_rules WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spAutotagRules(
	pOper int,
	pId int,
	pName varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagRules(
	pOper int,
	pId int,
	pName varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spAutotagRules(
	pOper int,
	pId int,
	pName varchar
) TO iusrpmt;
