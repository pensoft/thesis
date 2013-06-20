DROP TYPE ret_spFixTemplateObjects CASCADE;
CREATE TYPE ret_spFixTemplateObjects AS (
	result int
);

CREATE OR REPLACE FUNCTION spFixTemplateObjects(
	pOper int,
	pObjectId bigint, -- Обекта, който се е променил
	pSubobjectId bigint -- Обекта, който е бил добавен/променен/изтрит като подобект
)
  RETURNS ret_spFixTemplateObjects AS
$BODY$
DECLARE
	lRes ret_spFixTemplateObjects;
	
	lObjectId bigint;
	lPos varchar;
	lTemplateId int;
	
	lNewId bigint;	
	lNewPos varchar;
	lIter int;
	lInitialOccurrence int;
	lIsFake boolean;
	lDisplayName varchar;
	lDefaultMode int;
	lAllowedModes int[];
	lRecord record;
	lObject record;
BEGIN
	-- В тази функция ще коригираме дървото на темплейтите след като подадения обект е променил подадения си подобект
	SELECT INTO lInitialOccurrence initial_occurrence FROM object_subobjects WHERE object_id = pObjectId AND subobject_id = pSubobjectId;
	
	IF pOper = 1 THEN -- Insert
		-- Вкарваме наново цялото дърво, колкото пъти е необходимо във всички темплейти, които имат обект pObjectId, независимо от нивото
		lIsFake = false;
		IF lInitialOccurrence = 0 THEN
			lInitialOccurrence = 1;
			lIsFake = true;	
		END IF;
		<<TemplateObjectLoop>>
		FOR lRecord IN (SELECT id, template_id, pos FROM template_objects WHERE object_id = pObjectId) LOOP
			lIter = 1;
			-- Добавяме го най-отдолу на parent обекта
			SELECT INTO lNewPos max(pos) FROM template_objects 
				WHERE template_id = lRecord.template_id AND substring(pos, 1, char_length(lRecord.pos)) = lRecord.pos
				AND char_length(pos) = char_length(lRecord.pos) + 2;
				
			IF lNewPos IS NULL THEN
				lNewPos := 'AA';
			ELSE
				lNewPos := ForumGetNextOrd(lNewPos);
			END IF;
			
			SELECT INTO lObject * FROM pwt.objects WHERE id = pSubobjectId;
			
			<<OccurrenceLoop>>
			FOR lIter IN 1 .. lInitialOccurrence LOOP
				
				lNewId = nextval('template_objects_id_seq');
				INSERT INTO template_objects(id, template_id, object_id, display_in_tree, pos, is_fake, default_mode_id, default_new_mode_id, allowed_modes, display_name,
						allow_movement, allow_add, allow_remove, display_title_and_top_actions, display_default_actions,
						title_display_style, default_actions_type, displayed_actions_type, limit_new_object_creation) 
					VALUES (lNewId, lRecord.template_id, pSubobjectId, lObject.default_display_in_tree, lRecord.pos || lNewPos, lIsFake, lObject.default_mode_id, lObject.default_new_mode_id, lObject.default_allowed_modes, lObject.default_display_name,
						lObject.default_allow_movement, lObject.default_allow_add, lObject.default_allow_remove, lObject.default_display_title_and_top_actions, lObject.default_display_default_actions,
						lObject.default_title_display_style, lObject.default_actions_type, lObject.default_displayed_actions_type, lObject.default_limit_new_object_creation);
				lNewPos := ForumGetNextOrd(lNewPos);
				
				PERFORM spCopyTemplateObjectTree(lNewId);			
			END LOOP OccurrenceLoop;
		END LOOP TemplateObjectLoop;
	ELSEIF pOper = 2 THEN -- Update
		-- Трием всички такива подобекти и вкарваме правилния брой отново
		PERFORM spFixTemplateObjects(3, pObjectId, pSubobjectId);
		PERFORM spFixTemplateObjects(1, pObjectId, pSubobjectId);
	ELSEIF pOper = 3 THEN -- Delete
		-- Трием подобектите от парент обектите
		DELETE FROM template_objects t
			USING template_objects t1
			JOIN template_objects t2 ON t2.object_id = pSubobjectId  AND t2.template_id = t1.template_id 
				AND substring(t2.pos, 1, char_length(t1.pos)) = t1.pos AND char_length(t2.pos) = char_length(t1.pos) + 2
		WHERE t1.object_id = pObjectId AND t.template_id = t2.template_id 
			AND substring(t.pos, 1, char_length(t2.pos)) = t2.pos;			
	END IF;
	
	
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spFixTemplateObjects(
	pOper int,
	pObjectId bigint,
	pSubobjectId bigint
) TO iusrpmt;
