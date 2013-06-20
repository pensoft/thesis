DROP TYPE ret_spIndesignTemplateDetails CASCADE;


CREATE TYPE ret_spIndesignTemplateDetails AS (
	id int,
	template_id int,
	name varchar,	
	node_id int,
	style varchar,
	type int,
	parent_path varchar,
	new_parent int,
	change_before int,
	change_after int,
	special int
);

CREATE OR REPLACE FUNCTION spIndesignTemplateDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pNodeId int,
	pStyle varchar,
	pType int,
	pParentPath varchar,
	pNewParent int,
	pChangeBefore int,
	pChangeAfter int,
	pSpecial int
)
  RETURNS ret_spIndesignTemplateDetails AS
$BODY$
DECLARE
lRes ret_spIndesignTemplateDetails;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO indesign_template_details(indesign_templates_id, name, node_id, style, type, parent_path, new_parent, change_before, change_after, special) VALUES ( pTemplateId, pName, pNodeId, pStyle, pType, pParentPath, pNewParent, pChangeBefore, pChangeAfter, pSpecial);
		lId = currval('indesign_template_details_id_seq');
	ELSE -- Update
		UPDATE indesign_template_details SET
			name = pName,
			indesign_templates_id = pTemplateId, 
			node_id = pNodeId,
			style = pStyle,
			type = pType,
			parent_path = pParentPath,
			new_parent = pNewParent,
			change_before = pChangeBefore,
			change_after = pChangeAfter,
			special = pSpecial
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM indesign_template_details WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy
	
END IF;


SELECT INTO lRes id, indesign_templates_id, name, node_id, style, type, parent_path, new_parent, change_before, change_after, special FROM indesign_template_details WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spIndesignTemplateDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pNodeId int,
	pStyle varchar,
	pType int,
	pParentPath varchar,
	pNewParent int,
	pChangeBefore int,
	pChangeAfter int,
	pSpecial int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spIndesignTemplateDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pNodeId int,
	pStyle varchar,
	pType int,
	pParentPath varchar,
	pNewParent int,
	pChangeBefore int,
	pChangeAfter int,
	pSpecial int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spIndesignTemplateDetails(
	pOper int,
	pId int,
	pTemplateId int,
	pName varchar,
	pNodeId int,
	pStyle varchar,
	pType int,
	pParentPath varchar,
	pNewParent int,
	pChangeBefore int,
	pChangeAfter int,
	pSpecial int
) TO iusrpmt;
