
CREATE OR REPLACE FUNCTION spCreateNewInstance(
	pParentInstanceId bigint,
	pObjectId bigint,
	pUid int,
	pMode int DEFAULT 1
)
  RETURNS ret_spCreateNewInstance AS
$BODY$
	DECLARE
		lRes ret_spCreateNewInstance;			
		lParentObjectId bigint;
		lParentPos varchar;
		lAllowedMaxCount int;
		lCurrentInstanceCntOfThisType int;
		lDocumentId int;
		lInstanceId bigint;
		lCurrentPos varchar;		
		lParentDocumentTemplateObjectId bigint;
		lParentIsConfirmed boolean;
		lDocumentTemplateObjectId bigint;
		lDisplayInTree boolean;
		lRecord record;
		lContainerObjectType int;
		lDisplayName varchar;
		lDisplayErr	boolean;
	BEGIN
		lContainerObjectType = 2;		
		
		SELECT INTO lParentObjectId, lParentPos, lParentDocumentTemplateObjectId, lDocumentId, lParentIsConfirmed
			i.object_id, i.pos, i.document_template_object_id, i.document_id, i.is_confirmed
		FROM pwt.document_object_instances i		
		WHERE i.id = pParentInstanceId;	
		
		lParentIsConfirmed = coalesce(lParentIsConfirmed, true);

		SELECT INTO lDocumentTemplateObjectId, lDisplayInTree, lDisplayName, lDisplayErr id, display_in_tree, display_name, display_err
		FROM pwt.document_template_objects
		WHERE parent_id = lParentDocumentTemplateObjectId AND object_id = pObjectId ORDER BY pos ASC LIMIT 1;
		
		IF lDocumentTemplateObjectId IS NULL THEN -- Ако няма такъв подобект в дървото - грешка
			RAISE EXCEPTION 'pwt.instance.thisInstanceCantHaveSuchSubobjects';
		END IF;
		
		-- Гледаме дали е възможно добавянето на този обект - дали няма прекалено много инстанси от този тип, към този parent
		SELECT INTO lCurrentInstanceCntOfThisType count(*) 
		FROM pwt.document_object_instances 
		WHERE object_id = pObjectId AND parent_id = pParentInstanceId AND (lParentIsConfirmed = false OR is_confirmed = true);
		
		SELECT INTO lAllowedMaxCount max_occurrence
		FROM pwt.object_subobjects
		WHERE object_id = lParentObjectId AND subobject_id = pObjectId;
		
		-- RAISE NOTICE 'Max %, Current %', lAllowedMaxCount, lCurrentInstanceCntOfThisType;

		IF lAllowedMaxCount <= lCurrentInstanceCntOfThisType THEN
			RAISE EXCEPTION 'pwt.instance.thereAreTooManyInstancesOfThisType';
		END IF;
		
		
		-- Взимаме следващата възможна позиция за новия инстанс
		SELECT INTO lCurrentPos pos 
		FROM pwt.document_object_instances
		WHERE parent_id = pParentInstanceId ORDER BY pos DESC LIMIT 1;
		
		IF lCurrentPos IS NULL THEN
			lCurrentPos = lParentPos || 'AA';
		ELSE
			lCurrentPos = lParentPos || ForumGetNextOrd(lCurrentPos);
		END IF;
		
		-- Вкарваме новия instance.
		SELECT INTO lInstanceId nextval('pwt.document_object_instances_id_seq'::regclass);
		INSERT INTO pwt.document_object_instances(id, document_id, object_id, pos, display_in_tree, document_template_object_id, parent_id, display_name, is_confirmed, display_err) 
			VALUES (lInstanceId, lDocumentId, pObjectId, lCurrentPos, lDisplayInTree, lDocumentTemplateObjectId, pParentInstanceId, lDisplayName, lParentIsConfirmed, lDisplayErr);
		
		-- Вкарваме празните field-ове
		INSERT INTO pwt.instance_field_values(instance_id, field_id, document_id, 
			value_str, value_int, value_arr_int, value_arr_str, value_date, value_arr_date, is_read_only, data_src_id) 
			SELECT lInstanceId, of.field_id, lDocumentId,
				dv.value_str, dv.value_int, dv.value_arr_int, dv.value_arr_str, dv.value_date, dv.value_arr_date, of.is_read_only, of.data_src_id
		FROM pwt.object_fields of
		LEFT JOIN pwt.field_default_values dv ON dv.id = of.default_value_id
		WHERE of.object_id = pObjectId;
		
		
		-- Вкарваме подобектите.
		<<SubObjectLoop>>
		FOR lRecord IN
			SELECT object_id FROM pwt.document_template_objects
			WHERE document_id = lDocumentId AND parent_id = lDocumentTemplateObjectId AND is_fake = false ORDER BY pos ASC
		LOOP
			PERFORM spCreateNewInstance(lInstanceId, lRecord.object_id, pUid); 
		END LOOP SubObjectLoop;
		
		SELECT INTO lRes.container_id  c.id
		FROM pwt.object_container_details cd
		JOIN pwt.object_containers c ON c.id = cd.container_id
		WHERE cd.item_id = pObjectId AND cd.item_type = lContainerObjectType AND c.object_id = lParentObjectId;
		
		--Изпълняваме екшъните след създаване към този обект
		BEGIN
			PERFORM spPerformInstanceAfterCreationActions(lInstanceId, pUid, pMode);
			EXCEPTION WHEN raise_exception THEN			
				RAISE EXCEPTION USING MESSAGE = SQLERRM;
		END;
		
		
		lRes.new_instance_id = lInstanceId;
		lRes.parent_instance_id = pParentInstanceId;
		lRes.display_in_tree = lDisplayInTree::int;

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateNewInstance(
	pParentInstanceId bigint,
	pObjectId bigint,
	pUid int,
	pMode int
) TO iusrpmt;
