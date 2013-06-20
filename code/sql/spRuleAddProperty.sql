DROP TYPE ret_spRuleAddProperty CASCADE;
CREATE TYPE ret_spRuleAddProperty AS (
	rule_id int,
	property_id int,
	type_id int,
	property_modifier_id int,
	priority int
);


CREATE OR REPLACE FUNCTION spRuleAddProperty(
	pOper int,
	pRuleId int,
	pPropertyId int,
	pTypeId int,
	pModifierId int,
	pPriority int
)
  RETURNS ret_spRuleAddProperty AS
$BODY$
DECLARE
lRes ret_spRuleAddProperty;
lPlaceModifierType int;
lRegExpModifierType int;
lSourceModifierType int;
lModifierType int;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lPlaceModifierType = 1;
lRegExpModifierType = 2;
lSourceModifierType = 3;

IF pOper = 1 THEN -- Insert/Update
	IF pTypeId <> lSourceModifierType THEN 
		SELECT INTO lModifierType type_id FROM autotag_property_modifiers WHERE id = pModifierId;	
		IF lModifierType <> pTypeId THEN
			RAISE EXCEPTION 'admin.autotag_rules_properties.wrongModifier';
		END IF;
	END IF;
	
	IF pTypeId = lPlaceModifierType AND NOT EXISTS (SELECT * FROM place_rules WHERE id = pPropertyId) THEN
		RAISE EXCEPTION 'admin.autotag_rules_properties.wrongProperty';
	END IF;
	
	IF pTypeId = lRegExpModifierType AND NOT EXISTS (SELECT * FROM regular_expressions WHERE id = pPropertyId) THEN
		RAISE EXCEPTION 'admin.autotag_rules_properties.wrongProperty';
	END IF;
	
	IF pTypeId = lSourceModifierType AND NOT EXISTS (SELECT * FROM autotag_re_sources WHERE id = pPropertyId) THEN
		RAISE EXCEPTION 'admin.autotag_rules_properties.wrongProperty';
	END IF;
	
	IF EXISTS ( SELECT * FROM autotag_rules_properties WHERE rule_id = pRuleId AND property_id = pPropertyId ) THEN
		UPDATE autotag_rules_properties SET
			property_type_id = pTypeId,
			property_modifier_id = pModifierId,
			priority = pPriority
		WHERE rule_id = pRuleId AND property_id = pPropertyId;
	ELSE
		INSERT INTO autotag_rules_properties(rule_id, property_id, property_modifier_id, property_type_id, priority) VALUES (pRuleId, pPropertyId, pModifierId, pTypeId, pPriority);
			
	END IF;	
ELSEIF pOper = 3 THEN
	DELETE FROM autotag_rules_properties WHERE rule_id = pRuleId AND property_id = pPropertyId;

END IF;


SELECT INTO lRes rule_id, property_id, property_type_id, property_modifier_id, priority FROM autotag_rules_properties WHERE rule_id = pRuleId AND property_id = pPropertyId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION spRuleAddProperty(
	pOper int,
	pRuleId int,
	pPropertyId int,
	pTypeId int,
	pModifierId int,
	pPriority int
) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION spRuleAddProperty(
	pOper int,
	pRuleId int,
	pPropertyId int,
	pTypeId int,
	pModifierId int,
	pPriority int
) TO postgres84;
GRANT EXECUTE ON FUNCTION spRuleAddProperty(
	pOper int,
	pRuleId int,
	pPropertyId int,
	pTypeId int,
	pModifierId int,
	pPriority int
) TO iusrpmt;
