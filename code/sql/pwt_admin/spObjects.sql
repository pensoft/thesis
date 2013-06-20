DROP TYPE ret_spObjects CASCADE;
CREATE TYPE ret_spObjects AS (
	id bigint,
	name varchar,
	default_display_name varchar,
	default_mode_id int,
	default_new_mode_id int,
	default_allowed_modes int[],
	default_display_in_tree int,
	default_allow_movement int,
	default_allow_add int,
	default_allow_remove int,
	default_display_title_and_top_actions int,
	default_display_default_actions int,
	default_title_display_style int,
	default_actions_type int,
	default_displayed_actions_type int,
	default_limit_new_object_creation int
);

CREATE OR REPLACE FUNCTION spObjects(
	pOper int,
	pId bigint,
	pName varchar,
	pDefaultDisplayName varchar,
	pDefaultMode int,
	pDefaultNewMode int,
	pDefaultAllowedModes int[],
	pDefaultDisplayInTree int,
	pDefaultAllowMovement int,
	pDefaultAllowAdd int,
	pDefaultAllowRemove int,
	pDefaultDisplayTitleAndTopActions int,
	pDefaultDisplayDefaultActions int,
	pDefaultTitleDisplayStyle int,
	pDefaultActionsType int,
	pDefaultDisplayedActionsType int,
	pDefaultLimitNewObjectCreation int,
	pUid int
)
  RETURNS ret_spObjects AS
$BODY$
DECLARE
lRes ret_spObjects;
--lSid int;
lCurTime timestamp;
lId bigint;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO objects(name, createuid, lastmoduid, default_display_name, default_mode_id, default_new_mode_id, default_allowed_modes,
				default_display_in_tree, default_allow_movement, default_allow_add, default_allow_remove, default_display_title_and_top_actions, default_display_default_actions,
				default_title_display_style, default_actions_type, default_displayed_actions_type, default_limit_new_object_creation) 
			VALUES (pName, pUid, pUid, pDefaultDisplayName, pDefaultMode, pDefaultNewMode, pDefaultAllowedModes,
				pDefaultDisplayInTree::boolean, pDefaultAllowMovement::boolean, pDefaultAllowAdd::boolean, pDefaultAllowRemove::boolean, pDefaultDisplayTitleAndTopActions::boolean, pDefaultDisplayDefaultActions::boolean,
				pDefaultTitleDisplayStyle, pDefaultActionsType, pDefaultDisplayedActionsType, pDefaultLimitNewObjectCreation::boolean
			);
		lId = currval('objects_id_seq');
	ELSE -- Update
		UPDATE objects SET
			name = pName,			
			lastmoduid = pUid,
			lastmoddate = now(),
			default_display_name = pDefaultDisplayName,
			default_mode_id = pDefaultMode,
			default_new_mode_id = pDefaultNewMode,
			default_allowed_modes = pDefaultAllowedModes,
			default_display_in_tree = pDefaultDisplayInTree::boolean, 
			default_allow_movement = pDefaultAllowMovement::boolean, 
			default_allow_add = pDefaultAllowAdd::boolean, 
			default_allow_remove = pDefaultAllowRemove::boolean, 
			default_display_title_and_top_actions = pDefaultDisplayTitleAndTopActions::boolean,
			default_display_default_actions = pDefaultDisplayDefaultActions::boolean,
			default_title_display_style = pDefaultTitleDisplayStyle,
			default_actions_type = pDefaultActionsType, 
			default_displayed_actions_type = pDefaultDisplayedActionsType,
			default_limit_new_object_creation = pDefaultLimitNewObjectCreation::boolean
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN -- Delete
	

END IF;


SELECT INTO lRes id, name, default_display_name, default_mode_id, default_new_mode_id, default_allowed_modes,
	default_display_in_tree::int, default_allow_movement::int, default_allow_add::int, default_allow_remove::int, default_display_title_and_top_actions::int, default_display_default_actions::int,
	default_title_display_style, default_actions_type, default_displayed_actions_type, default_limit_new_object_creation::int
FROM objects WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spObjects(
	pOper int,
	pId bigint,
	pName varchar,
	pDefaultDisplayName varchar,
	pDefaultMode int,
	pDefaultNewMode int,
	pDefaultAllowedModes int[],
	pDefaultDisplayInTree int,
	pDefaultAllowMovement int,
	pDefaultAllowAdd int,
	pDefaultAllowRemove int,
	pDefaultDisplayTitleAndTopActions int,
	pDefaultDisplayDefaultActions int,
	pDefaultTitleDisplayStyle int,
	pDefaultActionsType int,
	pDefaultDisplayedActionsType int,
	pDefaultLimitNewObjectCreation int,
	pUid int
) TO iusrpmt;
