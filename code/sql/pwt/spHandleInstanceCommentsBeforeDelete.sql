DROP TYPE ret_spHandleInstanceCommentsBeforeDelete CASCADE;
CREATE TYPE ret_spHandleInstanceCommentsBeforeDelete AS (
	result int
);

/**
	Тук ще оправяме коментарите, които започват/свършват в даден обект.
	Ще трием тези които са изцяло в дадения обект. 
	А тези които започват/свършват вътре ще ги местим.
*/
CREATE OR REPLACE FUNCTION spHandleInstanceCommentsBeforeDelete(
	pInstanceId bigint,	
	pUid int
)
  RETURNS ret_spHandleInstanceCommentsBeforeDelete AS
$BODY$
	DECLARE
		lRes ret_spHandleInstanceCommentsBeforeDelete;	
		lInstanceIds bigint[];
		lParentInstanceId bigint;
		
		lPreviousItemId bigint;
		lPreviousItemType int;
		
		lFollowingItemId bigint;
		lFollowingItemType int;
		
		lContainerItemFieldType int;
		lContainerItemObjectType int;	
		
		lRecord record;
		lPreviousFound boolean;
		lNextFound boolean;
		lCurrentFound boolean;
	BEGIN
		lContainerItemFieldType = 1;
		lContainerItemObjectType = 2;
		
		lPreviousFound = false;
		lNextFound = false;
		lCurrentFound = false;
		
		--Взимаме ид-тата на всички подобекти
		SELECT INTO lInstanceIds array_agg(i.id)
		FROM pwt.document_object_instances i
		JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
		WHERE p.id = pInstanceId;
		
		SELECT INTO lParentInstanceId i.parent_id 
		FROM pwt.document_object_instances i
		WHERE i.id = pInstanceId;
		
		--Първо трием тези които са изцяло в обекта
		DELETE 
		FROM pwt.msg m
		WHERE start_object_instances_id = ANY (lInstanceIds) 
			AND end_object_instances_id = ANY (lInstanceIds);
			
		-- Намираме предходния и следващия обект / field на parent-а след който ще сложим началото на коментара, за тези
		-- коментари които започват в обекта
		-- Тук ще разчитаме на подредбата от контейнерите на parent-а
		<<lItemsLoop>>
		FOR lRecord IN 
			SELECT cd.item_type, if.field_id, iso.id as instance_id
			FROM pwt.object_container_details cd
			JOIN pwt.object_containers oc ON oc.id = cd.container_id		
			JOIN pwt.document_object_instances di ON di.id = lParentInstanceId AND di.object_id = oc.object_id
			LEFT JOIN pwt.v_instance_fields if ON if.instance_id = di.id AND if.field_id = cd.item_id AND cd.item_type = lContainerItemFieldType
			LEFT JOIN pwt.document_object_instances iso ON iso.parent_id = di.id
				AND cd.item_type = lContainerItemObjectType AND iso.object_id = cd.item_id		
			WHERE (iso.id IS NOT NULL OR if.field_id IS NOT NULL) 		
			ORDER BY oc.ord ASC, cd.ord ASC, iso.pos ASC
		LOOP
			IF lRecord.item_type = lContainerItemObjectType AND lRecord.instance_id = pInstanceId THEN
				lCurrentFound = true;
				CONTINUE lItemsLoop;
			END IF;
			
			--Маркираме предходния
			IF lCurrentFound = false THEN
				lPreviousFound = true;
				
				lPreviousItemType = lRecord.item_type;
				IF lRecord.item_type = lContainerItemFieldType THEN
					lPreviousItemId = lRecord.field_id;
				ELSE
					lPreviousItemId = lRecord.instance_id;
				END IF;
				
				CONTINUE lItemsLoop;
			END IF;
			
			-- Спираме след първия елемент след подадения инстанс
			IF lCurrentFound = true THEN
				lNextFound = true;
				
				lFollowingItemType = lRecord.item_type;
				IF lRecord.item_type = lContainerItemFieldType THEN
					lFollowingItemId = lRecord.field_id;
				ELSE
					lFollowingItemId = lRecord.instance_id;
				END IF;
				EXIT lItemsLoop;
			END IF;
		
		END LOOP lItemsLoop;
		
		
		--Първо ъпдейтваме тези коментари които започват в обекта
		IF lPreviousFound = true THEN -- Ако има предходен елемент - слагаме ги след него
			-- Ако предходния елемент е field			
			IF lPreviousItemType = lContainerItemFieldType THEN
				UPDATE pwt.msg m SET
					start_object_instances_id = lParentInstanceId,
					start_object_field_id = lPreviousItemId,
					start_offset = -1
				WHERE start_object_instances_id = ANY (lInstanceIds);
			ELSE -- Слагаме ги след края на instance-а
				UPDATE pwt.msg m SET
					start_object_instances_id = lPreviousItemId,
					start_object_field_id = null,
					start_offset = -1
				WHERE start_object_instances_id = ANY (lInstanceIds);
			END IF;
			
		ELSE -- Ако няма - слагаме ги в началото на parent-а
			UPDATE pwt.msg m SET
				start_object_instances_id = lParentInstanceId,
				start_object_field_id = null,
				start_offset = 0
			WHERE start_object_instances_id = ANY (lInstanceIds);
		END IF;
		
		--След това ъпдейтваме тези които свършват в обекта
		IF lNextFound = true THEN -- Ако има следващ елемент - слагаме ги преди него
			-- Ако следващия елемент е field			
			IF lFollowingItemType = lContainerItemFieldType THEN
				UPDATE pwt.msg m SET
					end_object_instances_id = lParentInstanceId,
					end_object_field_id = lFollowingItemId,
					end_offset = -2
				WHERE end_object_instances_id = ANY (lInstanceIds);
			ELSE -- Слагаме ги преди instance-а
				UPDATE pwt.msg m SET
					end_object_instances_id = lFollowingItemId,
					end_object_field_id = null,
					end_offset = -2
				WHERE end_object_instances_id = ANY (lInstanceIds);
			END IF;
			
		ELSE -- Ако няма - слагаме ги в края на parent-а
			UPDATE pwt.msg m SET
				end_object_instances_id = lParentInstanceId,
				end_object_field_id = null,
				end_offset = -1
			WHERE end_object_instances_id = ANY (lInstanceIds);
		END IF;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spHandleInstanceCommentsBeforeDelete(
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
