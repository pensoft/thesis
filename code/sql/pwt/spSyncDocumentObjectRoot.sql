DROP TYPE ret_spSyncDocumentObjectRoot CASCADE;
CREATE TYPE ret_spSyncDocumentObjectRoot AS (
	result int,
	processed_objectids bigint[]
);

CREATE OR REPLACE FUNCTION spSyncDocumentObjectRoot(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int
)
  RETURNS ret_spSyncDocumentObjectRoot AS
$BODY$
DECLARE
	lRes ret_spSyncDocumentObjectRoot;
	--lSid int;
	lRecord record;
	lRecord2 record;
	lTemplateId int;
	lPos varchar;
	lInstanceId bigint;
	lActionAfterCreationPos bigint = 5;
BEGIN	
	SELECT INTO lTemplateId
		template_id 
	FROM pwt.documents WHERE id = pDocumentId;
	
	IF NOT EXISTS (
		SELECT *
		FROM pwt.template_objects
		WHERE template_id = lTemplateId AND object_id = pObjectId AND char_length(pos) = 2 
	) THEN
		RAISE EXCEPTION 'pwt.noSuchRootObjectInTemplateObjectsForThisTemplate';
	END IF;
	
	IF EXISTS (
		SELECT * 
		FROM pwt.document_template_objects 
		WHERE document_id = pDocumentId AND object_id = pObjectId
	)THEN
		SELECT INTO lRes
			result, processed_objectids 
		FROM spSyncDocumentObject(pObjectId, pDocumentId, pUid);
		RETURN lRes;
	END IF;
	
	SELECT INTO lPos
		max(pos)
	FROM pwt.document_template_objects
	WHERE document_id = pDocumentId AND char_length(pos) = 2;
	
	lPos = ForumGetNextOrd(lPos);
	
	
	INSERT INTO pwt.document_template_objects(document_id, template_id, object_id, pos, display_in_tree, is_fake, allow_movement, allow_add, allow_remove, 
		display_title_and_top_actions, display_name, default_mode_id, default_new_mode_id, allowed_modes, display_default_actions, title_display_style,
		xml_node_name, display_object_in_xml, generate_xml_id, default_actions_type, displayed_actions_type, limit_new_object_creation, view_xpath_sel, view_xsl_templ_mode,
		template_object_id, display_err
	)
	SELECT pDocumentId, lTemplateId, t.object_id, overlay(t.pos placing lPos from 1 for char_length(lPos)), t.display_in_tree, t.is_fake, t.allow_movement, t.allow_add, t.allow_remove, 
		t.display_title_and_top_actions, t.display_name, t.default_mode_id, t.default_new_mode_id, t.allowed_modes, t.display_default_actions, t.title_display_style,
		t.xml_node_name, t.display_object_in_xml, t.generate_xml_id, t.default_actions_type, t.displayed_actions_type, t.limit_new_object_creation, t.view_xpath_sel, t.view_xsl_templ_mode,
		t.id, t.display_err
	FROM pwt.template_objects t
	JOIN pwt.template_objects p ON p.template_id = t.template_id AND p.pos = substring(t.pos, 1, char_length(p.pos))
	WHERE t.template_id = lTemplateId AND p.object_id = pObjectId AND char_length(p.pos) = 2;
	
	UPDATE 	pwt.document_template_objects o SET
		parent_id = p.id
	FROM pwt.document_template_objects p
	WHERE o.parent_id IS NULL AND o.document_id = pDocumentId AND p.document_id = o.document_id AND char_length(o.pos) = char_length(p.pos) + 2 
		AND substring(o.pos, 1, char_length(p.pos)) = p.pos;
	
	SELECT INTO lPos
		max(pos)
	FROM pwt.document_object_instances 
	WHERE document_id = pDocumentId AND char_length(pos) = 2;
	
	lPos = ForumGetNextOrd(lPos);
	
	<<MainObjectLoop>>
	FOR lRecord IN 
		SELECT * 
		FROM pwt.document_template_objects 
		WHERE document_id = pDocumentId AND char_length(pos) = 2 AND is_fake = false AND object_id = pObjectId
		ORDER BY pos ASC
	LOOP
		SELECT INTO lInstanceId nextval('pwt.document_object_instances_id_seq'::regclass);
		INSERT INTO pwt.document_object_instances(id, document_id, object_id, pos, display_in_tree, document_template_object_id, display_name, display_err) 
			VALUES (lInstanceId, pDocumentId, lRecord.object_id, lPos, lRecord.display_in_tree, lRecord.id, lRecord.display_name, lRecord.display_err);
		lPos = ForumGetNextOrd(lPos);
		-- Вкарваме празните field-ове
		INSERT INTO pwt.instance_field_values(instance_id, field_id, document_id, 
				value_str, value_int, value_arr_int, value_arr_str, value_date, value_arr_date, is_read_only, data_src_id) 
			SELECT lInstanceId, of.field_id, pDocumentId,
				dv.value_str, dv.value_int, dv.value_arr_int, dv.value_arr_str, dv.value_date, dv.value_arr_date, of.is_read_only, of.data_src_id
		FROM pwt.object_fields of
		LEFT JOIN pwt.field_default_values dv ON dv.id = of.default_value_id
		WHERE of.object_id = lRecord.object_id;


		<<SubObjectLoop>>
		FOR lRecord2 IN
			SELECT object_id FROM pwt.document_template_objects 
			WHERE document_id = pDocumentId AND parent_id = lRecord.id AND is_fake = false ORDER BY pos ASC
		LOOP
			PERFORM spCreateNewInstance(lInstanceId, lRecord2.object_id, pUid); 
		END LOOP SubObjectLoop;
		
		--Изпълняваме екшъните след създаване към този обект
		<<AfterCreationActions>>
		FOR lRecord2 IN
			SELECT a.eval_sql_function as function 
			FROM pwt.actions a
			JOIN pwt.object_actions oa ON oa.action_id = a.id
			WHERE oa.object_id = lRecord.object_id AND oa.pos = lActionAfterCreationPos AND a.eval_sql_function <> '' 
			ORDER BY oa.ord ASC 
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord2.function || '(' || lInstanceId || ', ' || pUid || ');';
		END LOOP AfterCreationActions;
	END LOOP MainObjectLoop;	
		
	
	lRes.result = 1;
	lRes.processed_objectids = ARRAY[pObjectId]::bigint[];
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSyncDocumentObjectRoot(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int
) TO iusrpmt;
