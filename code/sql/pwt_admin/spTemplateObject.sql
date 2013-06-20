DROP TYPE ret_spTemplateObject CASCADE;
CREATE TYPE ret_spTemplateObject AS (
	id bigint,
	template_id int,
	object_id bigint,	
	display_in_tree int,
	pos varchar,
	level int,
	allow_movement int,
	allow_add int,
	allow_remove int,
	display_name varchar,
	display_title_and_top_actions int,
	display_default_actions int,
	default_mode_id int,
	default_new_mode_id int,
	allowed_modes int[],
	title_display_style int, 
	default_actions_type int,
	displayed_actions_type int,
	limit_new_object_creation int
);

CREATE OR REPLACE FUNCTION spTemplateObject(
	pOper int,
	pId bigint,
	pTemplateId int,
	pObjectId bigint,
	pDisplayInLeftcol int,
	pAllowMove int,
	pAllowAdd int,
	pAllowRemove int,
	pDisplayTitleAndTopActions int,
	pDisplayDefaultActions int,
	pDisplayName varchar,
	pDefaultMode int,
	pDefaultNewMode int,
	pAllowedModes int[],
	pTitleDisplayStyle int,
	pDefaultActionsType int,
	pDisplayedActionsType int,
	pLimitNewObjectCreation int,
	pUid int
)
  RETURNS ret_spTemplateObject AS
$BODY$
DECLARE
lRes ret_spTemplateObject;
--lSid int;
lPos varchar;
lId bigint;
lPrevObjectId bigint;
lTemplateId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		-- Добавяме обекта на 1во ниво
		-- Взимаме максималната позиция и я увеличаваме с 1 степен (примерно AC -> AD)
		SELECT INTO lPos pos FROM template_objects WHERE template_id = pTemplateId AND char_length(pos) = 2 ORDER BY pos DESC LIMIT 1;
		
		IF lPos IS NULL THEN
			lPos := 'AA';
		ELSE
			lPos := ForumGetNextOrd(lPos);
		END IF;
		
		INSERT INTO template_objects(template_id, object_id, display_in_tree, pos, allow_add, allow_movement, allow_remove,
			display_title_and_top_actions, display_default_actions, display_name, default_mode_id, default_new_mode_id, allowed_modes, title_display_style, default_actions_type,
			displayed_actions_type, limit_new_object_creation
			) 
			VALUES (pTemplateId, pObjectId, pDisplayInLeftcol::boolean, lPos, pAllowAdd::boolean, pAllowMove::boolean, pAllowRemove::boolean, 
				pDisplayTitleAndTopActions::boolean, pDisplayDefaultActions::boolean, pDisplayName, pDefaultMode, pDefaultNewMode, pAllowedModes, pTitleDisplayStyle, pDefaultActionsType,
				pDisplayedActionsType, pLimitNewObjectCreation::boolean
			);
		lId = currval('template_objects_id_seq');
		PERFORM spCopyTemplateObjectTree(lId);
		
	ELSE -- Update
		SELECT INTO lPrevObjectId, lPos object_id, pos
		FROM template_objects 
		WHERE id = pId;
		
		-- Ще може да променяме object_id-то само на 1то ниво
		IF char_length(lPos) > 2 THEN
			pObjectId = lPrevObjectId;
		END IF;
		
		-- Не променяме template_id ид-то
		UPDATE template_objects SET
			display_in_tree = pDisplayInLeftcol::boolean,
			object_id = pObjectId,
			allow_movement = pAllowMove::boolean,
			allow_add = pAllowAdd::boolean,
			allow_remove = pAllowRemove::boolean,
			display_name = pDisplayName,
			display_title_and_top_actions = pDisplayTitleAndTopActions::boolean,
			display_default_actions = pDisplayDefaultActions::boolean,
			default_mode_id = pDefaultMode,
			default_new_mode_id = pDefaultNewMode,
			allowed_modes = pAllowedModes,
			title_display_style = pTitleDisplayStyle,
			default_actions_type = pDefaultActionsType,
			displayed_actions_type = pDisplayedActionsType,
			limit_new_object_creation = pLimitNewObjectCreation::boolean
		WHERE id = pId;
		
		IF lPrevObjectId <> pObjectId THEN -- Трябва да изтрием дървото надолу и да го вкараме наново
			DELETE FROM template_objects WHERE template_id = pTemplateId AND substring(pos, 1, char_length(lPos)) = lPos AND char_length(pos) > char_length(lPos);
			PERFORM spCopyTemplateObjectTree(lId);
		END IF;
		
		
	END IF;
	
	UPDATE templates SET
		lastmoduid = pUid,
		lastmoddate = now()
	WHERE id = pTemplateId;
ELSEIF pOper = 3 THEN -- Delete
	--Трием цялото дърво надолу
	SELECT INTO lPos, lTemplateId pos, template_id FROM template_objects WHERE id = lId;
	
	IF char_length(lPos) > 2 THEN
		RAISE EXCEPTION 'pwt_admin.templates.canDeleteObjectsOnlyFromRootLevel';
	END IF;
	
	DELETE FROM template_objects WHERE template_id = lTemplateId AND substring(pos, 1, char_length(lPos)) = lPos;
	UPDATE templates SET
		lastmoduid = pUid,
		lastmoddate = now()
	WHERE id = pTemplateId;
END IF;


SELECT INTO lRes id, template_id, object_id, display_in_tree::int, pos, char_length(pos) / 2, allow_movement::int, allow_add::int, allow_remove::int, 
	display_name, display_title_and_top_actions::int, display_default_actions::int, default_mode_id, default_new_mode_id, allowed_modes, title_display_style, default_actions_type,
	displayed_actions_type, limit_new_object_creation::int
FROM template_objects WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spTemplateObject(
	pOper int,
	pId bigint,
	pTemplateId int,
	pObjectId bigint,
	pDisplayInLeftcol int,
	pAllowMove int,
	pAllowAdd int,
	pAllowRemove int,
	pDisplayTitleAndTopActions int,
	pDisplayDefaultActions int,
	pDisplayName varchar,
	pDefaultMode int,
	pDefaultNewMode int,
	pAllowedModes int[],
	pTitleDisplayStyle int,
	pDefaultActionsType int,
	pDisplayedActionsType int,
	pLimitNewObjectCreation int,
	pUid int
) TO iusrpmt;
