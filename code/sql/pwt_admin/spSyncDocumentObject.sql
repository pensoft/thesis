DROP TYPE ret_spSyncDocumentObject CASCADE;
CREATE TYPE ret_spSyncDocumentObject AS (
	result int,
	processed_objectids bigint[]
);

CREATE OR REPLACE FUNCTION spSyncDocumentObject(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int,
	pProcessedObjectIds bigint[] DEFAULT ARRAY[]::bigint[],
	pCreateTables int DEFAULT 1
)
  RETURNS ret_spSyncDocumentObject AS
$BODY$
DECLARE
	lRes ret_spSyncDocumentObject;
	--lSid int;
	lRecord record;
	lRecord2 record;
	lTempRecord record;
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
	lChildPosLength int;
BEGIN
	-- First sync the fields 
	PERFORM spSyncDocumentObjectFields(pObjectId, pDocumentId, pUid);
	pProcessedObjectIds = pProcessedObjectIds || pObjectId;
	
	IF pCreateTables = 1 THEN
		CREATE TEMP TABLE document_template_objects_ord(
			document_template_object_id bigint,
			pos varchar,
			id serial
		);
		CREATE TEMP TABLE document_objects_instances_ord(
			instance_id bigint,
			pos varchar,
			parent_pos varchar,
			id serial
		);
		
	END IF;
	
	<<ObjectsLoop>>
	FOR lRecord IN
		SELECT teo.*, p.object_id as parent_object_id
		FROM pwt.document_template_objects teo
		LEFT JOIN pwt.document_template_objects p ON p.id = teo.parent_id
		WHERE teo.document_id = pDocumentId AND teo.object_id = pObjectId
	LOOP
		lParentPos = lRecord.pos;
		lChildPosLength = char_length(lParentPos) + 2;
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
			RAISE EXCEPTION 'pwt.noSuchElementInTemplateObjects % % %', pObjectId, lRecord.parent_object_id, lRecord.parent_id;
		END IF;
		
		UPDATE pwt.document_template_objects dto SET
			display_in_tree = teo.display_in_tree,
			is_fake = teo.is_fake,
			allow_movement = teo.allow_movement,
			allow_add = teo.allow_add,
			allow_remove = teo.allow_remove,
			display_title_and_top_actions = teo.display_title_and_top_actions,
			display_name = teo.display_name,
			default_mode_id = teo.default_mode_id,
			default_new_mode_id = teo.default_new_mode_id,
			allowed_modes = teo.allowed_modes,
			display_default_actions = teo.display_default_actions,
			title_display_style = teo.title_display_style,
			xml_node_name = teo.xml_node_name,
			display_object_in_xml = teo.display_object_in_xml,
			generate_xml_id = teo.generate_xml_id,
			default_actions_type = teo.default_actions_type,
			displayed_actions_type = teo.displayed_actions_type,
			limit_new_object_creation = teo.limit_new_object_creation,
			view_xpath_sel = teo.view_xpath_sel,
			view_xsl_templ_mode = teo.view_xsl_templ_mode,
			create_in_popup = teo.create_in_popup
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
			
			-- Sync the child object if it has not already been synced
			IF NOT (pProcessedObjectIds @> ARRAY[lChildObjectId]::bigint[]) THEN
				SELECT INTO lTempRecord 
					*
				FROM spSyncDocumentObject(lChildObjectId, pDocumentId, pUid, pProcessedObjectIds, 0);
				pProcessedObjectIds = lTempRecord.processed_objectids;
			END IF;
			
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
					template_object_id, create_in_popup
				)
				SELECT pDocumentId, t.template_id, t.object_id, overlay(t.pos placing lNextChildPos from 1 for char_length(lNextChildPos)), t.display_in_tree, t.is_fake, t.allow_movement, t.allow_add, t.allow_remove, 
					t.display_title_and_top_actions, t.display_name, t.default_mode_id, t.default_new_mode_id, t.allowed_modes, t.display_default_actions, t.title_display_style,
					t.xml_node_name, t.display_object_in_xml, t.generate_xml_id, t.default_actions_type, t.displayed_actions_type, t.limit_new_object_creation, t.view_xpath_sel, t.view_xsl_templ_mode,
					t.id, t.create_in_popup
				FROM pwt.template_objects t
				JOIN pwt.template_objects c ON c.template_id = t.template_id AND c.pos = substring(t.pos, 1, char_length(c.pos)) AND char_length(c.pos) <= char_length(t.pos) 
				JOIN pwt.template_objects p ON p.template_id = c.template_id AND p.pos = substring(c.pos, 1, char_length(p.pos)) AND char_length(p.pos) + 2 = char_length(c.pos) 
				WHERE t.template_id = lRecord.template_id AND p.id = lTemplateObjectId AND c.id = lChildTemplateObjectId;
				
				UPDATE 	pwt.document_template_objects o SET
					parent_id = p.id
				FROM pwt.document_template_objects p
				WHERE o.document_id = pDocumentId AND p.document_id = o.document_id AND char_length(o.pos) = char_length(p.pos) + 2 AND o.parent_id IS NULL 
					AND substring(o.pos, 1, char_length(p.pos)) = p.pos;
				
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
		
		-- Update the subobjects' pos
		TRUNCATE document_template_objects_ord;
		
		-- Insert according to the pos in template_objects and the current pos
		INSERT INTO document_template_objects_ord(document_template_object_id)
			SELECT id FROM (
				SELECT DISTINCT ON (dto.id) dto.id, i.pos as ipos, dto.pos as dpos
				FROM pwt.document_template_objects dto
				JOIN pwt.template_objects i ON i.object_id = dto.object_id
				JOIN pwt.template_objects p ON p.template_id = i.template_id AND p.pos = substring(i.pos, 1, char_length(p.pos)) AND char_length(p.pos) + 2 = char_length(i.pos)
				WHERE dto.parent_id = lParentId
					AND i.template_id = lRecord.template_id AND p.id = lTemplateObjectId
			 	ORDER BY dto.id, i.pos ASC, dto.pos ASC
			 ) AS temp 
			 ORDER BY ipos ASC, dpos ASC;
		 
		 -- Just in case - there shouldnt be any subobjects that are missing from template_objects
		 INSERT INTO document_template_objects_ord(document_template_object_id)
			SELECT id FROM (
			 	SELECT DISTINCT dto.id, dto.pos
				FROM pwt.document_template_objects dto			 
				JOIN pwt.template_objects p ON p.template_id = lRecord.template_id 
				LEFT JOIN pwt.template_objects i ON i.object_id = dto.object_id AND i.template_id = p.template_id
					AND p.pos = substring(i.pos, 1, char_length(p.pos)) AND char_length(p.pos) + 2 = char_length(i.pos)
				WHERE dto.parent_id = lParentId AND i.id IS NULL
					AND p.id = lTemplateObjectId
			 	ORDER BY dto.pos ASC
			 	)AS temp 
			 	ORDER BY pos;
		 
			
		lNextChildPos = 'AA';
		<<lSubobjectsOrder>>
		FOR lRecord2 IN 
			SELECT * 
			FROM document_template_objects_ord 
			ORDER BY id ASC
		LOOP
			UPDATE document_template_objects_ord SET 
				pos = lParentPos || lNextChildPos 
			WHERE id = lRecord2.id;
			lNextChildPos = ForumGetNextOrd(lNextChildPos);
		END LOOP lSubobjectsOrder;
		
		UPDATE pwt.document_template_objects t SET
			pos = overlay(t.pos placing o.pos from 1 for lChildPosLength)
		FROM pwt.document_template_objects p 	
		JOIN document_template_objects_ord o ON o.document_template_object_id = p.id
		WHERE substring(t.pos, 1, char_length(p.pos)) = p.pos AND p.document_id = pDocumentId AND t.document_id = pDocumentId;			
		
		-- Update the positions of the instances
		FOR lTempRecord IN
			SELECT *
			FROM pwt.document_object_instances 
			WHERE document_template_object_id = lParentId
		LOOP 
			TRUNCATE document_objects_instances_ord;
		
			INSERT INTO document_objects_instances_ord(instance_id, parent_pos)
				SELECT i.id, lTempRecord.pos
				FROM pwt.document_object_instances i				
				JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
				WHERE i.parent_id = lTempRecord.id
					AND i.document_id = pDocumentId
				ORDER BY dto.pos ASC, i.pos ASC;
			 
			 lNextChildPos = 'AA';
			 <<lSubInstancesOrder>>
			FOR lRecord2 IN 
				SELECT * 
				FROM document_objects_instances_ord 
				ORDER BY id ASC
			LOOP
				UPDATE document_objects_instances_ord SET 
					pos = parent_pos || lNextChildPos 
				WHERE id = lRecord2.id;
				lNextChildPos = ForumGetNextOrd(lNextChildPos);
			END LOOP lSubInstancesOrder;
			
			UPDATE pwt.document_object_instances t SET
				pos = overlay(t.pos placing o.pos from 1 for char_length(o.pos))
			FROM pwt.document_object_instances p 	
			JOIN document_objects_instances_ord o ON o.instance_id = p.id
			WHERE substring(t.pos, 1, char_length(p.pos)) = p.pos AND p.document_id = pDocumentId AND t.document_id = pDocumentId;
		END LOOP;
		
	END LOOP ObjectsLoop;
	
	IF pCreateTables = 1 THEN
		DROP TABLE document_template_objects_ord;
		DROP TABLE document_objects_instances_ord;		
	END IF;
	
	lRes.result = 1;
	lRes.processed_objectids = pProcessedObjectIds;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSyncDocumentObject(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int,
	pProcessedObjectIds bigint[],
	pCreateTables int
) TO iusrpmt;
