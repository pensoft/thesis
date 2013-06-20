DROP TYPE ret_spSyncDocumentObject CASCADE;
CREATE TYPE ret_spSyncDocumentObject AS (
	result int
);

CREATE OR REPLACE FUNCTION spSyncDocumentObject(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int
)
  RETURNS ret_spSyncDocumentObject AS
$BODY$
DECLARE
	lRes ret_spSyncDocumentObject;
	--lSid int;
	lRecord record;
	lRecord2 record;
	lInstanceRecord record;
	lTemplateObjectId bigint;
	lCount int;
	lIsFake boolean;
	lCurrentCount int;
	lNextChildPos varchar;
	lParentPos varchar;
	lChildObjectId bigint;
	lParentId bigint;
	lChildTemplateObjectId bigint;
	lMinOccurrence int;
	lIter int;
BEGIN
	-- First sync the fields 
	PERFORM spSyncDocumentObjectFields(pObjectId, pDocumentId, pUid);
	
	<<ObjectsLoop>>
	FOR lRecord IN
		SELECT teo.*, p.object_id as parent_object_id
		FROM pwt.document_template_objects teo
		LEFT JOIN pwt.document_template_objects p ON p.id = teo.parent_id
		WHERE teo.document_id = pDocumentId AND teo.object_id = pObjectId
	LOOP
		lParentPos = lRecord.pos;
		lParentId = lRecord.id;
		SELECT INTO lNextChildPos 
			max(pos)
		FROM pwt.document_template_objects
		WHERE parent_id = lRecord.id;
		
		IF lNextChildPos IS NULL THEN
			lNextChildPos = lParentPos || 'AA';
		ELSE 
			lNextChildPos = lParentPos || ForumGetNextOrd(lNextChildPos);
		END IF;
		
		
		IF lRecord.parent_object_id IS NULL THEN -- Root object
			SELECT INTO lTemplateObjectId
				id
			FROM pwt.template_objects
			WHERE template_id = lRecord.template_id AND object_id = pObjectId AND char_length(pos) = 2;
		ELSE 
			SELECT INTO lTemplateObjectId 
				i.id
			FROM pwt.template_objects i
			JOIN pwt.template_objects p ON p.template_id = i.template_id AND p.pos = substring(i.pos, 1, char_length(p.pos)) AND char_length(p.pos) + 2 = char_length(i.pos)
			WHERE i.template_id = lRecord.template_id AND i.object_id = pObjectId AND p.object_id = lRecord.parent_object_id;
		END IF;
		
		IF lTemplateObjectId IS NULL THEN
			RAISE EXCEPTION 'pwt.noSuchElementInTemplateObjects';
		END IF;
		
		UPDATE pwt.document_template_objects dto SET
			display_in_tree = dto.display_in_tree,
			is_fake = dto.is_fake,
			allow_movement = dto.allow_movement,
			allow_add = dto.allow_add,
			allow_remove = dto.allow_remove,
			display_title_and_top_actions = dto.display_title_and_top_actions,
			display_name = dto.display_name,
			default_mode_id = dto.default_mode_id,
			default_new_mode_id = dto.default_new_mode_id,
			allowed_modes = dto.allowed_modes,
			display_default_actions = dto.display_default_actions,
			title_display_style = dto.title_display_style,
			xml_node_name = dto.xml_node_name,
			display_object_in_xml = dto.display_object_in_xml,
			generate_xml_id = dto.generate_xml_id,
			default_actions_type = dto.default_actions_type,
			displayed_actions_type = dto.displayed_actions_type,
			limit_new_object_creation = dto.limit_new_object_creation,
			view_xpath_sel = dto.view_xpath_sel,
			view_xsl_templ_mode = dto.view_xsl_templ_mode,
			create_in_popup = dto.create_in_popup
		FROM pwt.template_objects teo
		WHERE teo.id = lTemplateObjectId AND dto.id = lParentId;
		
		
		<<SubObjectLoop>>
		FOR lRecord2 IN
			SELECT DISTINCT ON (i.object_id) i.*, s.min_occurrence, s.max_occurrence, s.initial_occurrence, p.pos as parent_pos
			FROM pwt.template_objects i
			JOIN pwt.template_objects p ON p.template_id = i.template_id AND p.pos = substring(i.pos, 1, char_length(p.pos)) AND char_length(p.pos) + 2 = char_length(i.pos)
			JOIN pwt.object_subobjects s ON s.object_id = p.object_id AND s.subobject_id = i.object_id
			WHERE i.template_id = lRecord.template_id AND p.id = lTemplateObjectId
			ORDER BY i.object_id ASC, i.pos ASC
		LOOP
			lChildTemplateObjectId = lRecord2.id;			
			lCount = lRecord2.initial_occurrence;
			lIsFake = false;
			lChildObjectId = lRecord2.object_id;
			lMinOccurrence = lRecord2.min_occurrence;
			IF lCount = 0 THEN
				lIsFake = true;
				lCount = 1;
			END IF;
			
			-- Sync the child object
			PERFORM spSyncDocumentObject(lChildObjectId, pDocumentId, pUid);
			
			SELECT INTO lCurrentCount
				count(*)
			FROM pwt.document_template_objects teo			
			WHERE parent_id = lParentId AND teo.object_id = lRecord2.object_id;
			
			-- Update the existing document template objects
			UPDATE pwt.document_template_objects SET
				display_in_tree = lRecord2.display_in_tree,
				is_fake = lRecord2.is_fake,
				allow_movement = lRecord2.allow_movement,
				allow_add = lRecord2.allow_add,
				allow_remove = lRecord2.allow_remove,
				display_title_and_top_actions = lRecord2.display_title_and_top_actions,
				display_name = lRecord2.display_name,
				default_mode_id = lRecord2.default_mode_id,
				default_new_mode_id = lRecord2.default_new_mode_id,
				allowed_modes = lRecord2.allowed_modes,
				display_default_actions = lRecord2.display_default_actions,
				title_display_style = lRecord2.title_display_style,
				xml_node_name = lRecord2.xml_node_name,
				display_object_in_xml = lRecord2.display_object_in_xml,
				generate_xml_id = lRecord2.generate_xml_id,
				default_actions_type = lRecord2.default_actions_type,
				displayed_actions_type = lRecord2.displayed_actions_type,
				limit_new_object_creation = lRecord2.limit_new_object_creation,
				view_xpath_sel = lRecord2.view_xpath_sel,
				view_xsl_templ_mode = lRecord2.view_xsl_templ_mode,
				create_in_popup = lRecord2.create_in_popup
			WHERE parent_id = lParentId AND object_id = lRecord2.object_id;
			
			-- Insert the missing document template objects
			<<lOccurrenceLoop>>
			WHILE lCurrentCount < lCount LOOP
				INSERT INTO pwt.document_template_objects(document_id, template_id, object_id, pos, display_in_tree, is_fake, allow_movement, allow_add, allow_remove, 
					display_title_and_top_actions, display_name, default_mode_id, default_new_mode_id, allowed_modes, display_default_actions, title_display_style,
					xml_node_name, display_object_in_xml, generate_xml_id, default_actions_type, displayed_actions_type, limit_new_object_creation, view_xpath_sel, view_xsl_templ_mode,
					template_object_id, create_in_popup, parent_id
				)
				SELECT pDocumentId, t.template_id, t.object_id, overlay(t.pos placing lNextChildPos from 1 for char_length(lNextChildPos)), t.display_in_tree, t.is_fake, t.allow_movement, t.allow_add, t.allow_remove, 
					t.display_title_and_top_actions, t.display_name, t.default_mode_id, t.default_new_mode_id, t.allowed_modes, t.display_default_actions, t.title_display_style,
					t.xml_node_name, t.display_object_in_xml, t.generate_xml_id, t.default_actions_type, t.displayed_actions_type, t.limit_new_object_creation, t.view_xpath_sel, t.view_xsl_templ_mode,
					t.id, t.create_in_popup, lParentId
				FROM pwt.template_objects t
				JOIN pwt.template_objects c ON c.template_id = t.template_id AND c.pos = substring(t.pos, 1, char_length(c.pos)) AND char_length(c.pos) <= char_length(t.pos) 
				JOIN pwt.template_objects p ON p.template_id = c.template_id AND p.pos = substring(c.pos, 1, char_length(p.pos)) AND char_length(p.pos) + 2 = char_length(c.pos) 
				WHERE t.template_id = lRecord.template_id AND p.id = lTemplateObjectId AND c.id = lChildTemplateObjectId;
								
				lNextChildPos = lParentPos || ForumGetNextOrd(lNextChildPos);
				lCurrentCount = lCurrentCount + 1;
			END LOOP lOccurrenceLoop;
			
			-- If the min occurrence has decreased - remove the unnecessary rows
			IF lCurrentCount > lCount THEN
				DELETE 
				FROM pwt.document_template_objects c
				USING pwt.document_template_objects p 
				WHERE p.document_id = c.document_id AND p.pos = substring(c.pos, 1, char_length(p.pos)) AND char_length(p.pos) <= char_length(c.pos) 
					AND p.id IN (
						SELECT id 
						FROM pwt.document_template_objects
						WHERE parent_id = lParentId AND object_id = lChildObjectId 
						ORDER BY pos ASC OFFSET lCount
					)
				;
								
			END IF;
			
			-- Create the required instances
			<<InstancesLoop>>
			FOR lInstanceRecord IN
				SELECT * 
				FROM pwt.document_object_instances
				WHERE document_template_object_id = lParentId
			LOOP
				SELECT INTO lCurrentCount
					count(*)
				FROM pwt.document_object_instances teo			
				WHERE parent_id = lInstanceRecord.id AND teo.object_id = lRecord2.object_id;
				
				<<SubInstancesLoop>>
				WHILE lCurrentCount < lMinOccurrence LOOP
					PERFORM spCreateNewInstance(lInstanceRecord.id, lRecord2.object_id, pUid);
					lCurrentCount = lCurrentCount + 1;
				END LOOP SubInstancesLoop;
				
				
			END LOOP InstancesLoop;					


		END LOOP SubObjectLoop;
		
		-- Remove the unnecessary subobjects
		<<lRemovedSubobjects>>
		FOR lRecord2 IN
			SELECT *
			FROM pwt.document_template_objects
			WHERE parent_id = lParentId AND object_id NOT IN (
				SELECT DISTINCT i.object_id
				FROM pwt.template_objects i
				JOIN pwt.template_objects p ON p.template_id = i.template_id AND p.pos = substring(i.pos, 1, char_length(p.pos)) AND char_length(p.pos) + 2 = char_length(i.pos)
				JOIN pwt.object_subobjects s ON s.object_id = p.object_id AND s.subobject_id = i.object_id
				WHERE i.template_id = lRecord.template_id AND p.id = lTemplateObjectId
				ORDER BY i.object_id ASC
			)
		LOOP
			-- Remove the unnecessary instances
			<<UnnecessarryInstancesLoop>>
			FOR lInstanceRecord IN
				SELECT * 
				FROM pwt.document_object_instances
				WHERE document_template_object_id = lRecord2.id
			LOOP
				PERFORM spRemoveInstance(lInstanceRecord.id, pUid);
			END LOOP UnnecessarryInstancesLoop;	
			
			DELETE 
			FROM pwt.document_template_objects c
			USING pwt.document_template_objects p 
			WHERE p.id = lRecord2.id AND p.document_id = c.document_id AND p.pos = substring(c.pos, 1, char_length(p.pos)) AND char_length(p.pos) <= char_length(c.pos) ;
		END LOOP lRemovedSubobjects;
		
		
	END LOOP ObjectsLoop;
	
	lRes.result = 1;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSyncDocumentObject(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int
) TO iusrpmt;
