DROP TYPE ret_spPlaceRules CASCADE;
CREATE TYPE ret_spPlaceRules AS (
	id int,
	name varchar,	
	xpath varchar
);

CREATE OR REPLACE FUNCTION spPlaceRules(
	pOper int,
	pId int,
	pName varchar,
	pXpath varchar
)
  RETURNS ret_spPlaceRules AS
$BODY$
DECLARE
lRes ret_spPlaceRules;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO place_rules(name, xpath) VALUES (pName, pXpath);
		lId = currval('autotag_properties_id_seq');
	ELSE -- Update
		UPDATE place_rules SET
			name = pName,
			xpath = pXpath
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM place_rules WHERE id = lId;

END IF;


SELECT INTO lRes id, name, xpath FROM place_rules WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spPlaceRules(
	pOper int,
	pId int,
	pName varchar,
	pXpath varchar
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spPlaceRules(
	pOper int,
	pId int,
	pName varchar,
	pXpath varchar
) TO postgres84;
GRANT EXECUTE ON FUNCTION spPlaceRules(
	pOper int,
	pId int,
	pName varchar,
	pXpath varchar
) TO iusrpmt;
