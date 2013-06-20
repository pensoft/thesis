DROP TYPE ret_spTemplates CASCADE;
CREATE TYPE ret_spTemplates AS (
	id int,
	name varchar,
	state int
);

CREATE OR REPLACE FUNCTION spTemplates(
	pOper int,
	pId int,
	pName varchar,
	pState int,
	pUid int
)
  RETURNS ret_spTemplates AS
$BODY$
DECLARE
lRes ret_spTemplates;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO templates(name, state, createuid, lastmoduid) VALUES (pName, pState, pUid, pUid);
		lId = currval('templates_id_seq');
	ELSE -- Update
		UPDATE templates SET
			name = pName,	
			state = pState,
			lastmoduid = pUid,
			lastmoddate = now()
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN -- Delete
	

END IF;


SELECT INTO lRes id, name, state
FROM templates WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spTemplates(
	pOper int,
	pId int,
	pName varchar,
	pState int,
	pUid int
) TO iusrpmt;
