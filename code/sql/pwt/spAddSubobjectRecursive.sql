DROP TYPE ret_spAddSubobjectRecursive CASCADE;
CREATE TYPE ret_spAddSubobjectRecursive AS (
	id bigint
);

CREATE OR REPLACE FUNCTION spAddSubobjectRecursive(
	pParentInstanceId bigint,
	pDocumentTemplateObjectIdToAdd bigint,
	pUid int
)
  RETURNS ret_spAddSubobjectRecursive AS
$BODY$
DECLARE
	lRes ret_spAddSubobjectRecursive;
	lRecord record;
	l1stLevelParentDTOid bigint;
	l1stLevelParentObjectId bigint;
	l1stLevelParentInstanceId bigint;
	lMaxObjectsCount int;
	lCurrentObjectsCount int;
	lAddIsPossible boolean;
	lObjectActionImportMode int;
BEGIN
	lObjectActionImportMode = 2;
	IF NOT EXISTS (
		SELECT 1 
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		JOIN pwt.document_template_objects dto1 ON dto1.id = pDocumentTemplateObjectIdToAdd 
		WHERE i.id = pParentInstanceId AND dto.document_id = dto1.document_id AND substring(dto1.pos, 1, char_length(dto.pos)) = dto.pos
		LIMIT 1
	) THEN -- Има грешка - обекта не е наследник на подадения инстанс
		RAISE EXCEPTION 'pwt.instance.thisInstanceCantHaveSuchSubobjects';
	END IF;
	
	IF EXISTS (
		SELECT 1 
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		JOIN pwt.document_template_objects dto1 ON dto1.id = pDocumentTemplateObjectIdToAdd 
		WHERE i.id = pParentInstanceId AND dto1.parent_id = dto.id
		LIMIT 1
	) THEN -- Ако искаме да добавим директен наследник
		SELECT INTO l1stLevelParentObjectId object_id FROM pwt.document_template_objects WHERE id = pDocumentTemplateObjectIdToAdd;
		SELECT INTO lRes.id new_instance_id FROM spCreateNewInstance(
			pParentInstanceId,
			l1stLevelParentObjectId,
			pUid,
			lObjectActionImportMode
		);
		RETURN lRes;
		
	END IF;
	
	/** Разглеждаме parent-а на обекта, който искаме да добавим, който е директен наследник на подадения инстанс. Тъй като обекта, който искаме да добавим, не е директен наследник - 
	ще влезем в цикъла поне 1 път и ще инициализираме променливите за parent-а от 1-во ниво.
	Ако имаме instance, който да е потенциален родител гледаме дали може в него да добавим искания обект. Ако не можем - гледаме дали на текущото място
	може да добавим такъв родител. Ако можем - понеже надолу структурата на документа е вярна, считаме, че е  възможно да се добави такъв елемент.
	*/
	FOR lRecord IN
		SELECT p.id as parent_id, i1.id as instance_id, p.object_id
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		JOIN pwt.document_template_objects dto1 ON dto1.id = pDocumentTemplateObjectIdToAdd
		JOIN pwt.document_template_objects p ON p.parent_id = dto.id
			AND substring(dto1.pos, 1, char_length(p.pos)) = p.pos  AND char_length(p.pos) < char_length(dto1.pos) 
		LEFT JOIN pwt.document_object_instances i1 ON i1.parent_id = i.id AND i1.document_template_object_id = p.id		
		WHERE i.id = pParentInstanceId
		ORDER BY p.pos ASC
	LOOP
		l1stLevelParentDTOid = lRecord.parent_id;
		l1stLevelParentObjectId = lRecord.object_id;
		IF lRecord.instance_id IS NOT NULL THEN
			SELECT INTO lAddIsPossible result FROM spCheckIfIsPossibleToAddSubobjectRecursive(lRecord.instance_id, pDocumentTemplateObjectIdToAdd);
			IF coalesce(lAddIsPossible, false) = true THEN -- Тук може да добавим обекта който ни трябва - продължаваме рекурсивно надолу
				SELECT INTO lRes.id id FROM spAddSubobjectRecursive(
					lRecord.instance_id,
					pDocumentTemplateObjectIdToAdd,
					pUid
				);
				RETURN lRes;
			END IF;
		END IF;		
	END LOOP;
	-- Обиколили сме всички потенциални instance-и които може да са parent-и и в никой от тях не може да добавим обекта. Правим нов обект на текущото ниво.
	SELECT INTO l1stLevelParentInstanceId new_instance_id FROM spCreateNewInstance(
		pParentInstanceId,
		l1stLevelParentObjectId,
		pUid,
		lObjectActionImportMode
	);
	
	-- Ако при създаването на parent-а автоматично се е създал обект, какъвто ни трябва - връщаме го него
	FOR lRecord IN 
		SELECT i.id 
		FROM pwt.document_object_instances i		
		JOIN pwt.document_object_instances p ON p.id = l1stLevelParentInstanceId AND p.document_id = i.document_id			
		WHERE i.document_template_object_id = pDocumentTemplateObjectIdToAdd AND substring(i.pos, 1, char_length(p.pos)) = p.pos
	LOOP	
		lRes.id = lRecord.id;
		RETURN lRes;
	END LOOP;
	-- Ако не се е създал обекта, който ни трябва, - продължаваме рекурсивно надолу
	SELECT INTO lRes.id id FROM spAddSubobjectRecursive(
		l1stLevelParentInstanceId,
		pDocumentTemplateObjectIdToAdd,
		pUid
	);
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spAddSubobjectRecursive(
	pParentInstanceId bigint,
	pDocumentTemplateObjectIdToAdd bigint,
	pUid int
) TO iusrpmt;
