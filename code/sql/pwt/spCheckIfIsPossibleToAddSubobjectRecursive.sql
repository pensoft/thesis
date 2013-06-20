DROP TYPE ret_spCheckIfIsPossibleToAddSubobjectRecursive CASCADE;
CREATE TYPE ret_spCheckIfIsPossibleToAddSubobjectRecursive AS (
	result boolean
);

CREATE OR REPLACE FUNCTION spCheckIfIsPossibleToAddSubobjectRecursive(
	pParentInstanceId bigint,
	pDocumentTemplateObjectIdToAdd bigint
)
  RETURNS ret_spCheckIfIsPossibleToAddSubobjectRecursive AS
$BODY$
DECLARE
	lRes ret_spCheckIfIsPossibleToAddSubobjectRecursive;
	lRecord record;
	l1stLevelParentDTOid bigint;
	l1stLevelParentObjectId bigint;
	lMaxObjectsCount int;
	lCurrentObjectsCount int;
	lAddIsPossible boolean;
	lParentObjectId bigint;	
BEGIN
	lRes.result = false;
	
	SELECT INTO lParentObjectId object_id 
	FROM pwt.document_object_instances i
	WHERE i.id = pParentInstanceId;
	
--	RAISE NOTICE 'Check possible add parent % child %', lParentObjectId, pDocumentTemplateObjectIdToAdd;
	
	IF NOT EXISTS (
		SELECT 1 
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		JOIN pwt.document_template_objects dto1 ON dto1.id = pDocumentTemplateObjectIdToAdd 
		WHERE i.id = pParentInstanceId AND dto.document_id = dto1.document_id AND substring(dto1.pos, 1, char_length(dto.pos)) = dto.pos
		LIMIT 1
	) THEN -- Има грешка - обекта не е наследник на подадения инстанс
		--RAISE NOTICE 'Wrong heirarchy';
		RETURN lRes;
	END IF;
	
	IF EXISTS (
		SELECT 1 
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		JOIN pwt.document_template_objects dto1 ON dto1.id = pDocumentTemplateObjectIdToAdd 
		WHERE i.id = pParentInstanceId AND dto1.parent_id = dto.id
		LIMIT 1
	) THEN -- Ако искаме да добавим директен наследник - трябва да видим дали бройката, която можем да добавим не е запълнена
		--RAISE NOTICE 'Check for direct ancestor';
		SELECT INTO lCurrentObjectsCount count(*)
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.id = pDocumentTemplateObjectIdToAdd 
		WHERE i.parent_id = pParentInstanceId AND i.object_id = dto.object_id;
		
		SELECT INTO lMaxObjectsCount s.max_occurrence 
		FROM pwt.object_subobjects s
		JOIN pwt.document_object_instances i ON i.id = pParentInstanceId AND i.object_id = s.object_id
		JOIN pwt.document_template_objects dto ON dto.id = pDocumentTemplateObjectIdToAdd 
		WHERE s.subobject_id = dto.object_id;
		
		IF lCurrentObjectsCount < lMaxObjectsCount THEN -- Можем да добавим нов инстанс
			lRes.result = true;
		END IF; -- В противен случай не можем да добавим. Връщаме си false
		
		RETURN lRes;
	END IF;
	
	/** Разглеждаме parent-а на обекта, който искаме да добавим, който е директен наследник на подадения инстанс. Тъй като обекта, който искаме да добавим, не е директен наследник - 
	ще влезем в цикъла поне 1 път и ще инициализираме променливите за parent-а от 1-во ниво.
	Ако имаме instance, който да е потенциален родител гледаме дали може в него да добавим искания обект. Ако не можем - гледаме дали на текущото място
	може да добавим такъв родител. Ако можем - понеже надолу структурата на документа е вярна считаме че е  възможно да се добави такъв елемент.
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
			IF coalesce(lAddIsPossible, false) = true THEN
				lRes.result = true;
				RETURN lRes;
			END IF;
		END IF;		
	END LOOP;
	-- Обиколили сме всички потенциални instance-и които може да са parent-и и в никой от тях не може да добавим обекта.
	-- Сега гледаме дали е възможно да добавим празен instance, който може да е parent
	SELECT INTO lCurrentObjectsCount count(*)
	FROM pwt.document_object_instances i
	WHERE parent_id = pParentInstanceId AND object_id = l1stLevelParentObjectId;
	
	SELECT INTO lMaxObjectsCount s.max_occurrence 
	FROM pwt.object_subobjects s
	JOIN pwt.document_object_instances i ON i.id = pParentInstanceId AND i.object_id = s.object_id
	WHERE s.subobject_id = l1stLevelParentObjectId;
	
	IF lCurrentObjectsCount < lMaxObjectsCount THEN -- Можем да добавим нов инстанс
		lRes.result = true;
	END IF;-- В противен случай не можем да добавим. Връщаме си false
	
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCheckIfIsPossibleToAddSubobjectRecursive(
	pParentInstanceId bigint,
	pDocumentTemplateObjectIdToAdd bigint
) TO iusrpmt;
