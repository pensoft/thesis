DROP TYPE ret_spCopyTemplateObjectTree CASCADE;
CREATE TYPE ret_spCopyTemplateObjectTree AS (
	result int
);

CREATE OR REPLACE FUNCTION spCopyTemplateObjectTree(
	pTemplateObjectId bigint
)
  RETURNS ret_spCopyTemplateObjectTree AS
$BODY$
DECLARE
	lRes ret_spCopyTemplateObjectTree;
	
	lObjectId bigint;
	lPos varchar;
	lTemplateId int;
	
	lNewId bigint;	
	lNewPos varchar;
	lIter int;
	lIsFake boolean;
	lDisplayName varchar;
	lDefaultMode int;
	lAllowedModes int[];
	
	lRecord record;
	lObject record;
BEGIN

	SELECT INTO lObjectId, lPos, lTemplateId 
		object_id, pos, template_id
	FROM template_objects 
	WHERE id = pTemplateObjectId;
	
	lIsFake = false;
	-- Вкарваме всички подобекти по толкова пъти, колкото е тяхното initial_occurrence
	lNewPos := 'AA';
	
	<<SubobjectLoop>>
	FOR lRecord IN (SELECT subobject_id, initial_occurrence FROM object_subobjects WHERE object_id = lObjectId ORDER BY ord ASC) LOOP
		lIter = 1;
		IF lRecord.initial_occurrence = 0 THEN
			lIsFake = true;
			lRecord.initial_occurrence = 1;
		ELSE
			lIsFake = false;
		END IF;
		
		SELECT INTO lObject * FROM pwt.objects WHERE id = lRecord.subobject_id;
		
		<<OccurrenceLoop>>
		FOR lIter IN 1 .. lRecord.initial_occurrence LOOP
			
			lNewId = nextval('template_objects_id_seq');
			INSERT INTO template_objects(id, template_id, object_id, display_in_tree, pos, is_fake, default_mode_id, default_new_mode_id, allowed_modes, display_name,
					allow_movement, allow_add, allow_remove, display_title_and_top_actions, display_default_actions,
					title_display_style, default_actions_type, displayed_actions_type, limit_new_object_creation) 
				VALUES (lNewId, lTemplateId, lRecord.subobject_id, lObject.default_display_in_tree, lPos || lNewPos, lIsFake, lObject.default_mode_id, lObject.default_new_mode_id, lObject.default_allowed_modes, lObject.default_display_name,
					lObject.default_allow_movement, lObject.default_allow_add, lObject.default_allow_remove, lObject.default_display_title_and_top_actions, lObject.default_display_default_actions,
					lObject.default_title_display_style, lObject.default_actions_type, lObject.default_displayed_actions_type, lObject.default_limit_new_object_creation
					);
			lNewPos := ForumGetNextOrd(lNewPos);
			
			PERFORM spCopyTemplateObjectTree(lNewId);			
		END LOOP OccurrenceLoop;
	END LOOP SubobjectLoop;
		
	
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCopyTemplateObjectTree(
	pTemplateObjectId bigint
) TO iusrpmt;
