DROP TYPE ret_spIndesignTemplates CASCADE;
CREATE TYPE ret_spIndesignTemplates AS (
	id int,
	name varchar,
	type int
);

CREATE OR REPLACE FUNCTION spIndesignTemplates(
	pOper int,
	pId int,
	pName varchar,
	pType int
)
  RETURNS ret_spIndesignTemplates AS
$BODY$
DECLARE
lRes ret_spIndesignTemplates;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO indesign_templates(name, type) VALUES (pName, pType);
		lId = currval('indesign_templates_id_seq');
	ELSE -- Update
		UPDATE indesign_templates SET
			name = pName,
			type = pType
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM indesign_templates WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy
	INSERT INTO indesign_templates (name, type) SELECT coalesce(name, '') || '_copy', type FROM indesign_templates WHERE id = pId;
	lId = currval('indesign_templates_id_seq');
	INSERT INTO indesign_template_details(indesign_templates_id, name, node_id, style, type, parent_path, new_parent, change_before, change_after)
		SELECT lId, name, node_id, style, type, parent_path, new_parent, change_before, change_after 
		FROM indesign_template_details WHERE indesign_templates_id = pId;
END IF;


SELECT INTO lRes id, name, type FROM indesign_templates WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spIndesignTemplates(
	pOper int,
	pId int,
	pName varchar,
	pType int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spIndesignTemplates(
	pOper int,
	pId int,
	pName varchar,
	pType int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spIndesignTemplates(
	pOper int,
	pId int,
	pName varchar,
	pType int
) TO iusrpmt;
