DROP TYPE ret_spCopyObject CASCADE;
CREATE TYPE ret_spCopyObject AS (
	new_object_id bigint
);

CREATE OR REPLACE FUNCTION spCopyObject(
	pObjectId bigint,
	pDeepCopy int,
	pCreateUid int,
	pNameAffix varchar
)
  RETURNS ret_spCopyObject AS
$BODY$
DECLARE
	lRes ret_spCopyObject;	
	lRecord record;
	lContainerId bigint;
	lTempObjectId bigint;
	lContainerItemObjectType int;
BEGIN
	lContainerItemObjectType = 2;
	
	INSERT INTO pwt.objects(name, createuid, display_label, display_nesting_indicator, css_class, default_display_name, default_mode_id, default_allowed_modes, default_display_in_tree,
		default_allow_movement, default_allow_add, default_allow_remove, default_display_title_and_top_actions, default_display_default_actions, default_title_display_style,
		default_new_mode_id, default_actions_type, default_displayed_actions_type, default_limit_new_object_creation)
	SELECT name || coalesce(pNameAffix, '_Copy'), pCreateUid, display_label, display_nesting_indicator, css_class, default_display_name, default_mode_id, default_allowed_modes, default_display_in_tree,
		default_allow_movement, default_allow_add, default_allow_remove, default_display_title_and_top_actions, default_display_default_actions, default_title_display_style,
		default_new_mode_id, default_actions_type, default_displayed_actions_type, default_limit_new_object_creation
	FROM pwt.objects 
	WHERE id = pObjectId;
	
	lRes.new_object_id = currval('pwt.objects_id_seq');
	
	-- Копираме филдовете
	INSERT INTO pwt.object_fields(object_id, field_id, control_type, label, allow_nulls, data_src_id, has_help_label, help_label, display_label,
		css_class, autocomplete_row_templ, default_value_id, is_read_only, xml_node_name, display_in_xml, help_label_display_style, has_example_label, example_label, autocomplete_onselect)
	SELECT lRes.new_object_id, field_id, control_type, label, allow_nulls, data_src_id, has_help_label, help_label, display_label,
		css_class, autocomplete_row_templ, default_value_id, is_read_only, xml_node_name, display_in_xml, help_label_display_style, has_example_label, example_label, autocomplete_onselect
	FROM pwt.object_fields 
	WHERE object_id = pObjectId;
	
	-- Копираме контейнерите и детайлите към тях
	FOR lRecord IN 
		SELECT * FROM pwt.object_containers
		WHERE object_id = pObjectId
	LOOP
		lContainerId = nextval('pwt.object_containers_id_seq'::regclass);
		INSERT INTO pwt.object_containers(id, object_id, mode_id, ord, "type", "name", css_class)
			VALUES (lContainerId, lRes.new_object_id, lRecord.mode_id, lRecord.ord, lRecord.type, lRecord.name, lRecord.css_class);
		
		INSERT INTO pwt.object_container_details(container_id, item_id, ord, item_type, css_class)
		SELECT lContainerId, item_id, ord, item_type, css_class
		FROM pwt.object_container_details
		WHERE container_id = lRecord.id;
	END LOOP;
	
	--Копираме подобектите
	IF coalesce(pDeepCopy, 0) = 0 THEN
		INSERT INTO pwt.object_subobjects(object_id, subobject_id, min_occurrence, max_occurrence, initial_occurrence, ord)
		SELECT lRes.new_object_id, subobject_id, min_occurrence, max_occurrence, initial_occurrence, ord
		FROM pwt.object_subobjects
		WHERE object_id = pObjectId;
	ELSE -- Правим deep copy - копираме и подобектите
		FOR lRecord IN 
			SELECT subobject_id, min_occurrence, max_occurrence, initial_occurrence, ord
			FROM pwt.object_subobjects
			WHERE object_id = pObjectId
		LOOP 
			SELECT INTO lTempObjectId new_object_id FROM spCopyObject(lRecord.subobject_id,	pDeepCopy, pCreateUid, pNameAffix);
			
			INSERT INTO pwt.object_subobjects(object_id, subobject_id, min_occurrence, max_occurrence, initial_occurrence, ord)
			VALUES (lRes.new_object_id, lTempObjectId, lRecord.min_occurrence, lRecord.max_occurrence, lRecord.initial_occurrence, lRecord.ord);
			
			-- Ъпдейтваме детайлите на контейнерите да са към новия обект
			UPDATE pwt.object_container_details cd	SET 
				item_id = lTempObjectId
			FROM pwt.object_containers c
			WHERE c.id = cd.container_id 
				AND cd.item_id = lRecord.subobject_id 
				AND c.type = lContainerItemObjectType 
				AND c.object_id = lRes.new_object_id;
		END LOOP;
	END IF;
	
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCopyObject(
	pObjectId bigint,
	pDeepCopy int,
	pCreateUid int,
	pNameAffix varchar
) TO iusrpmt;
