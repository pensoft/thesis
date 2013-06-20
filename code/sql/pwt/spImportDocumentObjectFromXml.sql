DROP TYPE ret_spImportDocumentObjectFromXml CASCADE;
CREATE TYPE ret_spImportDocumentObjectFromXml AS (
	result int
);

CREATE OR REPLACE FUNCTION spImportDocumentObjectFromXml(
	pDocumentId int,
	pXml xml,
	pParentInstanceId bigint,
	pUid int
)
  RETURNS ret_spImportDocumentObjectFromXml AS
$BODY$
DECLARE
	lRes ret_spImportDocumentObjectFromXml;
	
	lChildObjects xml[];
	lFields xml[];
	lCurrentInstanceId bigint;
	lCurrentInstanceDTOid bigint;
	lCurrentInstanceObjectId bigint;
	
	lCurrentObjectName varchar;
	lCurrentObjectIdx int;
	
	lCurrentField xml;
	lCurrentFieldName varchar;
	
	lAddIsPosible boolean;
	lTemp xml[];
	lIter int;
	
	lFieldInstanceId bigint;
	lFieldObjectId bigint;
	
	lParentDTOid bigint;
	
	lTempInstanceId bigint;
	lRecord record;
	lRecord2 record;
	lObjectActionImportMode int;
BEGIN
	lObjectActionImportMode = 2;
	-- Първо трябва да определим кой инстанс ще променяме - дали трябва да го създадем или вече е създаден и просто трябва да го ъплоуднем
	lTemp = xpath('@node_name', pXml);
	lCurrentObjectName = lTemp[1]::varchar;
	
	lTemp = xpath('@object_idx', pXml);
	-- RAISE NOTICE 'Object idx %', lTemp[1];
	lCurrentObjectIdx = lTemp[1]::text::int;
	
	IF NOT(pParentInstanceId IS NULL) THEN
	
		SELECT INTO lParentDTOid document_template_object_id FROM pwt.document_object_instances WHERE id = pParentInstanceId;
		
		SELECT INTO lCurrentInstanceId, lCurrentInstanceObjectId i.id, i.object_id
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		JOIN pwt.document_object_instances p ON p.id = pParentInstanceId AND p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
		WHERE dto.xml_node_name = lCurrentObjectName AND i.is_confirmed = true
		LIMIT 1 OFFSET lCurrentObjectIdx - 1;
		
		IF lCurrentInstanceId IS NULL THEN -- Обекта не съществува и трябва да го създадем
			FOR lRecord IN
				SELECT dto.id, dto.object_id
				FROM pwt.document_template_objects dto 
				JOIN pwt.document_template_objects p ON p.id = lParentDTOid AND dto.document_id = p.document_id AND p.pos = substring(dto.pos, 1, char_length(p.pos))
				WHERE dto.xml_node_name = lCurrentObjectName
				ORDER BY dto.pos ASC
			LOOP
				-- RAISE NOTICE 'Parent instance_id %, lChildToAdd %', pParentInstanceId, lRecord.id;
				SELECT INTO lAddIsPosible result FROM spCheckIfIsPossibleToAddSubobjectRecursive(pParentInstanceId, lRecord.id);
				IF coalesce(lAddIsPosible, false) = true THEN
					lCurrentInstanceDTOid = lRecord.id;
					lCurrentInstanceObjectId = lRecord.object_id
					EXIT;
				END IF;
			END LOOP;
			
			IF lCurrentInstanceDTOid IS NULL THEN
				SELECT INTO pParentInstanceId object_id
				FROM pwt.document_object_instances
				WHERE id = pParentInstanceId;
				
				RAISE EXCEPTION 'pwt.cantAddSuchObject1 % % % %', pParentInstanceId, lParentDTOid, lCurrentObjectName, pXml;
				-- RAISE EXCEPTION 'pwt.cantAddSuchObject';
			END IF;
			
			-- RAISE NOTICE 'Parent %, dto_id %', pParentInstanceId, lRecord.id;
			SELECT INTO lCurrentInstanceId id FROM spAddSubobjectRecursive(pParentInstanceId, lCurrentInstanceDTOid, pUid);
		END IF;
	ELSE -- Трябва да ъпдейтнем инстанс от 1во ниво
		SELECT INTO lCurrentInstanceId i.id
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		WHERE i.parent_id IS NULL AND dto.xml_node_name = lCurrentObjectName AND i.document_id = pDocumentId;
	END IF;
	
	IF lCurrentInstanceId IS NULL THEN
		RAISE EXCEPTION 'pwt.cantAddSuchObject';
	END IF;
	
	--Второ - ъпдейтваме field-овете.
	lFields = xpath('/*/fields/*[@is_field="1"]', pXml);
	-- Първо ъпдейтваме полетата които са към този instance
	FOR lIter IN 
		1 .. coalesce(array_upper(lFields, 1), 0) 
	LOOP
		lCurrentField = lFields[lIter];		
		lTemp = xpath('@node_name', lCurrentField);
		lCurrentFieldName = lTemp[1]::varchar;
		
		SELECT INTO lRecord fv.field_id 			
		FROM pwt.instance_field_values fv
		JOIN pwt.document_object_instances i ON i.id = fv.instance_id
		JOIN pwt.object_fields f ON f.object_id = i.object_id AND f.xml_node_name = lCurrentFieldName AND f.field_id = fv.field_id
		WHERE i.id = lCurrentInstanceId AND f.display_in_xml = 1;
		
		-- RAISE NOTICE 'Field Name %, field_id %', lCurrentFieldName, lRecord.field_id;
		IF lRecord.field_id IS NOT NULL THEN
			PERFORM spSaveInstanceFieldFromXml(lCurrentInstanceId, lRecord.field_id, lCurrentField, pUid);
		END IF;
		
	END LOOP;
	
	-- RAISE NOTICE 'Updating fields complete';
	
	--След това извикваме екшъните след save
	 PERFORM spPerformInstancesSqlSaveActions(pUid, ARRAY[lCurrentInstanceId]::int[], lObjectActionImportMode);
	
	-- Ако по някаква причина е изчезнал текущия обект - например след екшън на парента, който трие определни възли - не правим нищо
	IF EXISTS (
		SELECT * 
		FROM pwt.document_object_instances 
		WHERE id = lCurrentInstanceId
	) THEN
	
		-- След това ъпдейтваме field-овете на инстансите надолу в йерархията
		FOR lIter IN 
			1 .. coalesce(array_upper(lFields, 1), 0) 
		LOOP
			lCurrentField = lFields[lIter];		
			lTemp = xpath('@node_name', lCurrentField);
			lCurrentFieldName = lTemp[1]::varchar;
			
			SELECT INTO lRecord fv.field_id 			
			FROM pwt.instance_field_values fv
			JOIN pwt.document_object_instances i ON i.id = fv.instance_id
			JOIN pwt.object_fields f ON f.object_id = i.object_id AND f.xml_node_name = lCurrentFieldName AND f.field_id = fv.field_id
			WHERE i.id = lCurrentInstanceId;
			
			IF lRecord.field_id IS NULL THEN -- Тези които не са към текущия обект
				-- Гледаме дали имаме съществуващ инстанс с такова поле. Тук имаме ограничение, че инстансите трябва да са на 1 ниво надолу
				SELECT INTO lRecord fv.field_id, i.id as instance_id			
				FROM pwt.instance_field_values fv
				JOIN pwt.document_object_instances i ON i.id = fv.instance_id
				JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id 
				JOIN pwt.object_fields f ON f.object_id = i.object_id AND f.xml_node_name = lCurrentFieldName AND f.field_id = fv.field_id
				WHERE i.parent_id = lCurrentInstanceId AND dto.display_object_in_xml = 4 LIMIT 1;
				
				IF lRecord.field_id IS NOT NULL THEN -- Имаме такъв инстанс
					PERFORM spSaveInstanceFieldFromXml(lRecord.instance_id, lRecord.field_id, lCurrentField, pUid);
				ELSE -- Ще трябва да направим нов инстанс
					SELECT INTO lRecord f.field_id, dto1.id as dto_id, dto1.object_id, dto.object_id as parent_object_id
					FROM pwt.object_fields f
					JOIN pwt.document_object_instances i ON i.id = lCurrentInstanceId 
					JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id 
					JOIN pwt.document_template_objects dto1 ON dto1.parent_id = dto.id
					
					WHERE 
					f.object_id = dto1.object_id AND f.xml_node_name = lCurrentFieldName
					AND dto1.display_object_in_xml = 4 LIMIT 1;
					
					/* Тук няма смисъл да гледаме дали може да добавим обекта, понеже щом е в йерархията на документа - той може 
					да бъде добавен (няма създадени instance-и от него, понеже иначе щеше да има инстанс който да ъпдейтнем)
					*/
					--SELECT INTO lTempInstanceId new_instance_id FROM spCreateNewInstance(lCurrentInstanceId, lRecord.object_id, pUid);
					BEGIN
						SELECT INTO lRecord2 * 
						FROM pwt.document_object_instances  
								WHERE id = lCurrentInstanceId;					
								
						SELECT INTO lTempInstanceId new_instance_id FROM spCreateNewInstance(lCurrentInstanceId, lRecord.object_id, pUid, lObjectActionImportMode);
					
						EXCEPTION
							WHEN raise_exception THEN		
								
								RAISE EXCEPTION 'pwt.instance.thisInstanceCantHaveSuchSubobjects FieldXmlName %, CurrentInstanceId %, CurrentObjectId %, XML %', lCurrentFieldName, lCurrentInstanceId, lCurrentInstanceObjectId, pXml;						
					END;
					
					IF lRecord.dto_id IS NOT NULL THEN
						PERFORM spSaveInstanceFieldFromXml(lTempInstanceId, lRecord.field_id, lCurrentField, pUid);
					ELSE
						RAISE EXCEPTION 'pwt.unknownFieldObject';
					END IF;
					
				END IF;
			END IF;
			
		END LOOP;
		
		
		--Накрая вкарваме подобектите
		lChildObjects := xpath('/*[@is_object="1"]/*[@is_object="1"]', pXml);
		FOR lIter IN 
			1 .. coalesce(array_upper(lChildObjects, 1), 0) 
		LOOP
			PERFORM spImportDocumentObjectFromXml(pDocumentId, lChildObjects[lIter], lCurrentInstanceId, pUid);
		END LOOP;
		
		--След това извикваме екшъните, които са за импорт след save след вкарване на подобектите
		PERFORM spPerformInstancesSqlSaveActionsAfterSubobjWithProp(pUid, ARRAY[lCurrentInstanceId]::int[], lObjectActionImportMode);
		PERFORM spPerformInstancesSqlSaveActionsAfterSubobjWithoutProp(pUid, ARRAY[lCurrentInstanceId]::int[], lObjectActionImportMode);
	END IF;
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spImportDocumentObjectFromXml(
	pDocumentId int,
	pXml xml,
	pParentInstanceId bigint,
	pUid int
) TO iusrpmt;
