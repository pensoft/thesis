ALTER TABLE pwt.object_actions ADD COLUMN execute_in_modes int[] DEFAULT ARRAY[1, 2];
COMMENT ON COLUMN pwt.object_actions.execute_in_modes IS 'In which modes to execute the action (1 - normal mode, 2 - import mode)';

UPDATE pwt.object_actions SET execute_in_modes = ARRAY[1] WHERE object_id = 95 AND pos = 4;
UPDATE pwt.object_actions SET execute_in_modes = ARRAY[1] WHERE action_id = 56 AND pos = 4;

UPDATE pwt.object_action_pos SET name = 'After save (SQL) (If executed in import it will be performed immediately after self fields have been updated)' WHERE id = 4;
INSERT INTO pwt.object_action_pos(name) VALUES ('After save (SQL) (Will be executed only in import it will be performed after self fields have been updated and subobjects have been imported and will propagate to parents)');
INSERT INTO pwt.object_action_pos(name) VALUES ('After save (SQL) (Will be executed only in import it will be performed after self fields have been updated and subobjects have been imported and will NOT propagate to parents)');

INSERT INTO pwt.object_actions(object_id, action_id, ord, pos, execute_in_modes) VALUES (21, 57, 1, 12, ARRAY[11]);

DROP TYPE ret_spPerformInstancesSqlSaveActionsAfterSubobjWithoutProp CASCADE;
CREATE TYPE ret_spPerformInstancesSqlSaveActionsAfterSubobjWithoutProp AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstancesSqlSaveActionsAfterSubobjWithoutProp(
	pUid int,
	pInstanceIds bigint[],
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstancesSqlSaveActionsAfterSubobjWithoutProp AS
$BODY$
	DECLARE
		lRes ret_spPerformInstancesSqlSaveActionsAfterSubobjWithoutProp;	
		
		lSqlSaveActionPos int;
		lRecord record;
	BEGIN
		lSqlSaveActionPos = 12;
		
		--Обработваме ги отгоре- надолу, т.е. първо се викат екшъните на подобектите и след това на самите обекти		
		--За всеки от променяните обекти викаме и after save event-ите на parent-ите
		FOR lRecord IN 
			SELECT i.id as instance_id, a.eval_sql_function as function
			FROM pwt.document_object_instances i			
			JOIN pwt.object_actions oa ON oa.object_id = i.object_id
			JOIN pwt.actions a ON a.id = oa.action_id
			WHERE i.id = ANY (pInstanceIds) AND oa.pos = lSqlSaveActionPos AND pMode = ANY(oa.execute_in_modes)
			ORDER BY i.pos DESC, oa.ord ASC
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || lRecord.instance_id || ', ' || pUid || ');';
		END LOOP;
		
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstancesSqlSaveActionsAfterSubobjWithoutProp(	
	pUid int,
	pInstanceIds bigint[],
	pMode int
) TO iusrpmt;


DROP TYPE ret_spPerformInstancesSqlSaveActionsAfterSubobjWithProp CASCADE;
CREATE TYPE ret_spPerformInstancesSqlSaveActionsAfterSubobjWithProp AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstancesSqlSaveActionsAfterSubobjWithProp(
	pUid int,
	pInstanceIds bigint[],
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstancesSqlSaveActionsAfterSubobjWithProp AS
$BODY$
	DECLARE
		lRes ret_spPerformInstancesSqlSaveActionsAfterSubobjWithProp;	
		
		lSqlSaveActionPos int;
		lRecord record;
	BEGIN
		lSqlSaveActionPos = 11;
		
		--Обработваме ги отгоре- надолу, т.е. първо се викат екшъните на подобектите и след това на самите обекти		
		--За всеки от променяните обекти викаме и after save event-ите на parent-ите
		FOR lRecord IN 
			SELECT i.id as instance_id, a.eval_sql_function as function
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances c ON c.document_id = i.document_id AND i.pos = substring(c.pos, 1, char_length(i.pos))
			JOIN pwt.object_actions oa ON oa.object_id = i.object_id
			JOIN pwt.actions a ON a.id = oa.action_id
			WHERE c.id = ANY (pInstanceIds) AND oa.pos = lSqlSaveActionPos AND pMode = ANY(oa.execute_in_modes)
			ORDER BY i.pos DESC, oa.ord ASC
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || lRecord.instance_id || ', ' || pUid || ');';
		END LOOP;
		
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstancesSqlSaveActionsAfterSubobjWithProp(	
	pUid int,
	pInstanceIds bigint[],
	pMode int
) TO iusrpmt;


CREATE OR REPLACE FUNCTION spAddJournalArticleReferenceSingleAuthor(	
	pReferenceInstanceId bigint,
	pCombinedName varchar,
	pAuthorIdx int,
	pUID int
)
RETURNS int AS
$BODY$
	DECLARE
	lWrapperObjectId bigint;
	lWrapperInstanceId bigint;
	lWrapperSubobjectInstanceId bigint;
	lAuthorsHolderObjectId bigint;
	lAuthorsHolderInstanceId bigint;
	lSingleAuthorObjectId bigint;
	lAuthorObjectId bigint;
	lRecord record;
	
	lIter int;
	lMinCount int;
	
	lSingleAuthorInstanceId bigint;
	lCombinedNameFieldId bigint;
	BEGIN
		lWrapperObjectId = 97;
		lAuthorsHolderObjectId = 100;
		lSingleAuthorObjectId = 90;
		lCombinedNameFieldId = 250;
		
		-- Първо взимаме id-то на wrapper-a
		SELECT INTO lWrapperInstanceId id 
		FROM pwt.document_object_instances 
		WHERE parent_id = pReferenceInstanceId AND object_id = lWrapperObjectId;
		-- RAISE NOTICE 'Ref %, Wrapper1 %, object_id %', pReferenceInstanceId, lWrapperInstanceId, lWrapperObjectId;
		
		-- След това взимаме id-то на подобекта, който е практически истинската референция
		SELECT INTO lWrapperSubobjectInstanceId id 
		FROM pwt.document_object_instances 
		WHERE parent_id = lWrapperInstanceId;
		
		-- След това взимаме id-то на подобекта, в който стоят авторите
		SELECT INTO lAuthorsHolderInstanceId id 
		FROM pwt.document_object_instances 
		WHERE parent_id = lWrapperSubobjectInstanceId AND object_id = lAuthorsHolderObjectId;
		
		-- RAISE NOTICE 'Wrapper2 %, object_id %', lWrapperSubobjectInstanceId, lAuthorsHolderObjectId;
		
		SELECT INTO lSingleAuthorInstanceId id FROM 
		pwt.document_object_instances 
		WHERE parent_id = lAuthorsHolderInstanceId AND object_id = lSingleAuthorObjectId ORDER BY pos ASC
		LIMIT 1 OFFSET pAuthorIdx - 1;
		
		IF lSingleAuthorInstanceId IS NULL THEN -- Трябва да добавим нов автор
			-- RAISE NOTICE 'holder %, object_id %', lAuthorsHolderInstanceId, lSingleAuthorObjectId;
			SELECT INTO lSingleAuthorInstanceId new_instance_id FROM spCreateNewInstance(lAuthorsHolderInstanceId, lSingleAuthorObjectId, pUID);
		END IF;
		
		-- Ъпдейтваме му името и викаме тригерите след сейв
		UPDATE instance_field_values SET
			value_str = pCombinedName
		WHERE instance_id = lSingleAuthorInstanceId AND field_id = lCombinedNameFieldId;
		
		PERFORM spPerformInstancesSqlSaveActions(pUid, ARRAY[lSingleAuthorInstanceId]::int[]);
		
	RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spAddJournalArticleReferenceSingleAuthor(
	pReferenceInstanceId bigint,
	pCombinedName varchar,
	pAuthorIdx int,
	pUID int
) TO iusrpmt;

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
BEGIN
	lRes.result = false;
	
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

-- Function: pwt.spcreatedocumentbytemplate(integer, character varying, integer, integer, integer)

-- DROP FUNCTION pwt.spcreatedocumentbytemplate(integer, character varying, integer, integer, integer);

CREATE OR REPLACE FUNCTION pwt.spcreatedocumentbytemplate(ptemplateid integer, pdocumentname character varying, ppapertype integer, pjournal_id integer, puid integer)
  RETURNS ret_spcreatedocument AS
$BODY$
		DECLARE
			lRes ret_spCreateDocument;		
			lId int;
			lRecord record;
			lRecord2 record;
			lInstanceId bigint;
			lCurrentPos varchar;
			lActionAfterCreationPos bigint;
			lAuthorInstanceId bigint;
			lAuthorObjectId int;
			lAuthorRightsFieldId int;
			lDefaultAuthorRights int[];
			lAuthorMarkAsCorrespondingFieldIdValue int[];
			lAuthorMarkAsCorrespondingFieldId int;
			lAuthorNameSearchInstanceId bigint;
		BEGIN	
			lActionAfterCreationPos = 5;
			lAuthorObjectId = 8;
			lAuthorRightsFieldId = 14; 
			lDefaultAuthorRights = ARRAY[1];
			lAuthorMarkAsCorrespondingFieldId = 15;
			lAuthorMarkAsCorrespondingFieldIdValue = ARRAY[1];
			
			SELECT INTO lId nextval('pwt.documents_id_seq'::regclass);
			INSERT INTO pwt.documents(id, name, template_id, createuid, lastmoduid, state, papertype_id, journal_id) VALUES (lId, pDocumentName, pTemplateId, pUid, pUid, 1, pPaperType, pJournal_id);
			
			-- Първо запазваме цялата текуща структурата на шаблона.
			INSERT INTO pwt.document_template_objects(document_id, template_id, object_id, pos, display_in_tree, is_fake, allow_movement, allow_add, allow_remove, 
					display_title_and_top_actions, display_name, default_mode_id, default_new_mode_id, allowed_modes, display_default_actions, title_display_style,
					xml_node_name, display_object_in_xml, generate_xml_id, default_actions_type, displayed_actions_type, limit_new_object_creation, view_xpath_sel, view_xsl_templ_mode,
					template_object_id
				)
				SELECT lId, pTemplateId, t.object_id, t.pos, t.display_in_tree, t.is_fake, t.allow_movement, t.allow_add, t.allow_remove, 
					t.display_title_and_top_actions, t.display_name, t.default_mode_id, t.default_new_mode_id, t.allowed_modes, t.display_default_actions, t.title_display_style,
					t.xml_node_name, t.display_object_in_xml, t.generate_xml_id, t.default_actions_type, t.displayed_actions_type, t.limit_new_object_creation, t.view_xpath_sel, t.view_xsl_templ_mode,
					t.id
				FROM pwt.template_objects t
				WHERE t.template_id = pTemplateId;
				
			UPDATE 	pwt.document_template_objects o SET
				parent_id = p.id
			FROM pwt.document_template_objects p
			WHERE o.document_id = lId AND p.document_id = o.document_id AND char_length(o.pos) = char_length(p.pos) + 2 
				AND substring(o.pos, 1, char_length(p.pos)) = p.pos;
			
			-- След това копираме цялостната структура на шаблона към документа
			-- Вкарваме ръчно само обектите на 1-во ниво и по-надолу ползваме функцията за добавяне на подобекти
			lCurrentPos = 'AA';
			<<MainObjectLoop>>
			FOR lRecord IN 
				SELECT * FROM pwt.document_template_objects WHERE document_id = lId AND char_length(pos) = 2 AND is_fake = false ORDER BY pos ASC
			LOOP
				SELECT INTO lInstanceId nextval('pwt.document_object_instances_id_seq'::regclass);
				INSERT INTO pwt.document_object_instances(id, document_id, object_id, pos, display_in_tree, document_template_object_id, display_name) 
					VALUES (lInstanceId, lId, lRecord.object_id, lCurrentPos, lRecord.display_in_tree, lRecord.id, lRecord.display_name);
				lCurrentPos = ForumGetNextOrd(lCurrentPos);
				-- Вкарваме празните field-ове
				INSERT INTO pwt.instance_field_values(instance_id, field_id, document_id, 
						value_str, value_int, value_arr_int, value_arr_str, value_date, value_arr_date, is_read_only, data_src_id) 
					SELECT lInstanceId, of.field_id, lId,
						dv.value_str, dv.value_int, dv.value_arr_int, dv.value_arr_str, dv.value_date, dv.value_arr_date, of.is_read_only, of.data_src_id
				FROM pwt.object_fields of
				LEFT JOIN pwt.field_default_values dv ON dv.id = of.default_value_id
				WHERE of.object_id = lRecord.object_id;


				<<SubObjectLoop>>
				FOR lRecord2 IN
					SELECT object_id FROM pwt.document_template_objects 
					WHERE document_id = lId AND parent_id = lRecord.id AND is_fake = false ORDER BY pos ASC
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
					
							
			-- След това вкарваме потребителя, който създава документа в потребителите на документа. 2 - типа на потребителя е "Автор"
			INSERT INTO pwt.document_users(document_id, usr_id, first_name, middle_name, last_name, usr_type)
				SELECT lId, pUid, first_name, middle_name, last_name, 2 FROM usr WHERE id = pUid;
			
			-- След това вкарваме текущия потребител като автор на документа
			-- Тъй като вече имаме такъв (празен) само го ъпдейтваме с данните на текущия потребител
			
			-- Първо проверяваме дали въобще имаме такъв обект в темплейта
			SELECT INTO lAuthorInstanceId i.id 
			FROM pwt.document_object_instances i 
			JOIN pwt.document_template_objects dto ON dto.parent_id = i.document_template_object_id AND dto.object_id = lAuthorObjectId AND dto.document_id = lId;
			
			-- Ако имаме започваме ъпдейта
			IF lAuthorInstanceId IS NOT NULL THEN
				-- Зимаме ид-то на инстанса
				SELECT INTO lAuthorInstanceId instance_id 
					FROM pwt.instance_field_values 
					WHERE document_id = lId AND field_id = 13;
				
				-- Update-ваме rights-а на автора да може да пише и коментира
				UPDATE pwt.instance_field_values SET
					value_arr_int = lDefaultAuthorRights
				WHERE instance_id = lAuthorInstanceId AND field_id = lAuthorRightsFieldId;
				
				-- Mаркираме автора като Corresponding author
				UPDATE pwt.instance_field_values SET
				value_arr_int = lAuthorMarkAsCorrespondingFieldIdValue
				WHERE instance_id = lAuthorInstanceId AND field_id = lAuthorMarkAsCorrespondingFieldId;
					
				-- Попълваме останалите полета от обекта Автор
				SELECT INTO lAuthorNameSearchInstanceId id
					FROM pwt.document_object_instances i 
					WHERE parent_id = lAuthorInstanceId;
				IF lAuthorNameSearchInstanceId IS NOT NULL THEN
					PERFORM spSelectAuthor(lAuthorNameSearchInstanceId, pUid, pUid);
					-- Изпълняваме всички save action-и на всички подобекти на автора отдолу нагоре
					FOR lRecord IN 
						SELECT i.id 
						FROM pwt.document_object_instances i
						JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
						WHERE p.id = lAuthorInstanceId
						ORDER BY i.pos DESC
					LOOP
						PERFORM spPerformInstancesSqlSaveActions(pUid, ARRAY[lRecord.id]::int[]);
					END LOOP;
				END IF;				
			END IF;
				
			lRes.id = lId;
			RETURN lRes;
		END
	$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spcreatedocumentbytemplate(integer, character varying, integer, integer, integer) OWNER TO postgres;


DROP TYPE ret_spCreateDocumentDefaultAuthor CASCADE;
CREATE TYPE ret_spCreateDocumentDefaultAuthor AS (
	id int
);


CREATE OR REPLACE FUNCTION spCreateDocumentDefaultAuthor(
	pDocumentId int,
	pUid int
)
  RETURNS ret_spCreateDocument AS
$BODY$
	DECLARE
		lRes ret_spCreateDocument;		
		lId int;
		lAuthorsHolderInstanceId bigint;
		lAuthorObjectId bigint;
		lAuthorNameSearchObjectId bigint;
		lAuthorNameSearchInstanceId bigint;
		lAuthorRightsFieldId bigint;
		lDefaultAuthorRights int[];
		
		lRecord record;
	BEGIN	
		lAuthorObjectId = 8;
		lAuthorRightsFieldId = 14; 
		lDefaultAuthorRights = ARRAY[1];
		
		SELECT INTO lAuthorsHolderInstanceId i.id 
		FROM pwt.document_object_instances i 
		JOIN pwt.document_template_objects dto ON dto.parent_id = i.document_template_object_id AND dto.object_id = lAuthorObjectId AND dto.document_id = pDocumentId;
		
		IF lAuthorsHolderInstanceId IS NOT NULL THEN
			SELECT INTO lRes.id new_instance_id FROM spCreateNewInstance(lAuthorsHolderInstanceId, lAuthorObjectId, pUid);
			
			--Update-ваме rights-а на автора да може да пише и коментира
			UPDATE pwt.instance_field_values SET
				value_arr_int = lDefaultAuthorRights
			WHERE instance_id = lRes.id AND field_id = lAuthorRightsFieldId;
			
			SELECT INTO lAuthorNameSearchInstanceId id
				FROM pwt.document_object_instances i 
				WHERE parent_id = lRes.id;
			IF lAuthorNameSearchInstanceId IS NOT NULL THEN
				PERFORM spSelectAuthor(lAuthorNameSearchInstanceId, pUid, pUid);
				-- Изпълняваме всички save action-и на всички подобекти на автора отдолу нагоре
				FOR lRecord IN 
					SELECT i.id 
					FROM pwt.document_object_instances i
					JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
					WHERE p.id = lRes.id
					ORDER BY i.pos DESC
				LOOP
					PERFORM spPerformInstancesSqlSaveActions(pUid, ARRAY[lRecord.id]::int[]);
				END LOOP;
				
			END IF;				
		END IF;
		
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateDocumentDefaultAuthor(
	pDocumentId int,
	pUid int
) TO iusrpmt;

DROP TYPE ret_spCreateNewInstance CASCADE;
CREATE TYPE ret_spCreateNewInstance AS (
	new_instance_id bigint,
	parent_instance_id bigint,
	display_in_tree int,
	container_id bigint
);

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
		lDocumentTemplateObjectId bigint;
		lDisplayInTree boolean;
		lRecord record;
		lContainerObjectType int;
		lDisplayName varchar;		
	BEGIN
		lContainerObjectType = 2;		
		
		SELECT INTO lParentObjectId, lParentPos, lParentDocumentTemplateObjectId, lDocumentId i.object_id, i.pos, i.document_template_object_id, i.document_id
		FROM pwt.document_object_instances i		
		WHERE i.id = pParentInstanceId;	

		SELECT INTO lDocumentTemplateObjectId, lDisplayInTree, lDisplayName id, display_in_tree, display_name
		FROM pwt.document_template_objects
		WHERE parent_id = lParentDocumentTemplateObjectId AND object_id = pObjectId ORDER BY pos ASC LIMIT 1;
		
		IF lDocumentTemplateObjectId IS NULL THEN -- Ако няма такъв подобект в дървото - грешка
			RAISE EXCEPTION 'pwt.instance.thisInstanceCantHaveSuchSubobjects';
		END IF;
		
		-- Гледаме дали е възможно добавянето на този обект - дали няма прекалено много инстанси от този тип, към този parent
		SELECT INTO lCurrentInstanceCntOfThisType count(*) 
		FROM pwt.document_object_instances 
		WHERE object_id = pObjectId AND parent_id = pParentInstanceId;
		
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
		INSERT INTO pwt.document_object_instances(id, document_id, object_id, pos, display_in_tree, document_template_object_id, parent_id, display_name) 
			VALUES (lInstanceId, lDocumentId, pObjectId, lCurrentPos, lDisplayInTree, lDocumentTemplateObjectId, pParentInstanceId, lDisplayName);
		
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
		PERFORM spPerformInstanceAfterCreationActions(lInstanceId, pUid, pMode);
		
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
		LEFT JOIN pwt.document_object_instances p1 ON p1.document_id = p.document_id 
			AND p1.pos = substring(i.pos, 1, char_length(p1.pos)) AND char_length(p1.pos) > char_length(p.pos) AND char_length(p1.pos) < char_length(i.pos)
		LEFT JOIN pwt.document_template_objects dto1 ON dto1.id = p1.document_template_object_id AND dto1.display_object_in_xml <> 3
		WHERE dto.xml_node_name = lCurrentObjectName AND dto1.id IS NULL AND dto.display_object_in_xml = 1
		LIMIT 1 OFFSET lCurrentObjectIdx - 1;
		
		IF lCurrentInstanceId IS NULL THEN -- Обекта не съществува и трябва да го създадем
			FOR lRecord IN
				SELECT dto.id, dto.object_id
				FROM pwt.document_template_objects dto 
				JOIN pwt.document_template_objects p ON p.id = lParentDTOid AND dto.document_id = p.document_id AND p.pos = substring(dto.pos, 1, char_length(p.pos))
				LEFT JOIN pwt.document_template_objects p1 ON p1.display_object_in_xml <> 3 AND p1.document_id = p.document_id
					AND p1.pos = substring(dto.pos, 1, char_length(p1.pos)) AND char_length(p1.pos) > char_length(p.pos) AND char_length(p1.pos) < char_length(dto.pos) 
				WHERE dto.xml_node_name = lCurrentObjectName AND dto.display_object_in_xml = 1
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
				-- RAISE EXCEPTION 'pwt.cantAddSuchObject1 % % %', pParentInstanceId, lParentDTOid, pXml;
				RAISE EXCEPTION 'pwt.cantAddSuchObject';
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


DROP TYPE ret_spPerformInstanceAfterCreationActions CASCADE;
CREATE TYPE ret_spPerformInstanceAfterCreationActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstanceAfterCreationActions(
	pInstanceId bigint,
	pUid int,
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstanceAfterCreationActions AS
$BODY$
	DECLARE
		lRes ret_spPerformInstanceAfterCreationActions;							
		lRecord record;
		lActionAfterCreationPos bigint;
		lObjectId bigint;		
	BEGIN
		lActionAfterCreationPos = 5;
		
		SELECT INTO lObjectId object_id FROM pwt.document_object_instances WHERE id = pInstanceId;
		
		--Изпълняваме екшъните след създаване към този обект
		<<AfterCreationActions>>
		FOR lRecord IN
			SELECT a.eval_sql_function as function 
			FROM pwt.actions a
			JOIN pwt.object_actions oa ON oa.action_id = a.id
			WHERE oa.object_id = lObjectId AND oa.pos = lActionAfterCreationPos AND a.eval_sql_function <> '' AND pMode = ANY (oa.execute_in_modes)
			ORDER BY oa.ord ASC 
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || pInstanceId || ', ' || pUid || ');';
		END LOOP AfterCreationActions;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstanceAfterCreationActions(
	pInstanceId bigint,
	pUid int,
	pMode int
) TO iusrpmt;


DROP TYPE ret_spPerformInstanceBeforeDeleteActions CASCADE;
CREATE TYPE ret_spPerformInstanceBeforeDeleteActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstanceBeforeDeleteActions(
	pInstanceId bigint,
	pUid int,
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstanceBeforeDeleteActions AS
$BODY$
	DECLARE
		lRes ret_spPerformInstanceBeforeDeleteActions;					
		lReferenceTypeFieldId bigint;
		lRecord record;
		lActionBeforeDeletePos bigint;
		lObjectId bigint;		
	BEGIN
		lActionBeforeDeletePos = 6;
		
		SELECT INTO lObjectId object_id FROM pwt.document_object_instances WHERE id = pInstanceId;
		
		--Изпълняваме екшъните след създаване към този обект
		<<AfterCreationActions>>
		FOR lRecord IN
			SELECT a.eval_sql_function as function 
			FROM pwt.actions a
			JOIN pwt.object_actions oa ON oa.action_id = a.id
			WHERE oa.object_id = lObjectId AND oa.pos = lActionBeforeDeletePos AND a.eval_sql_function <> '' 
			ORDER BY oa.ord ASC 
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || pInstanceId || ', ' || pUid || ');';
		END LOOP AfterCreationActions;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstanceBeforeDeleteActions(
	pInstanceId bigint,
	pUid int,
	pMode int
) TO iusrpmt;


DROP TYPE ret_spPerformInstancesBeforeSqlAutoSaveActions CASCADE;
CREATE TYPE ret_spPerformInstancesBeforeSqlAutoSaveActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstancesBeforeSqlAutoSaveActions(
	pUid int,
	pInstanceIds bigint[],
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstancesBeforeSqlAutoSaveActions AS
$BODY$
	DECLARE
		lRes ret_spPerformInstancesBeforeSqlAutoSaveActions;	
		
		lSqlBeforeSaveActionPos int;
		lRecord record;
	BEGIN
		lSqlBeforeSaveActionPos = 10;
		
		--Обработваме ги отгоре- надолу, т.е. първо се викат екшъните на подобектите и след това на самите обекти		
		FOR lRecord IN 
			SELECT i.id as instance_id, a.eval_sql_function as function
			FROM pwt.document_object_instances i
			JOIN pwt.object_actions oa ON oa.object_id = i.object_id
			JOIN pwt.actions a ON a.id = oa.action_id
			WHERE i.id = ANY (pInstanceIds) AND oa.pos = lSqlBeforeSaveActionPos AND pMode = ANY(oa.execute_in_modes)
			ORDER BY i.pos DESC, oa.ord ASC
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || lRecord.instance_id || ', ' || pUid || ');';
		END LOOP;
		
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstancesBeforeSqlAutoSaveActions(	
	pUid int,
	pInstanceIds bigint[],
	pMode int
) TO iusrpmt;


DROP TYPE ret_spPerformInstancesBeforeSqlSaveActions CASCADE;
CREATE TYPE ret_spPerformInstancesBeforeSqlSaveActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstancesBeforeSqlSaveActions(
	pUid int,
	pInstanceIds bigint[],
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstancesBeforeSqlSaveActions AS
$BODY$
	DECLARE
		lRes ret_spPerformInstancesBeforeSqlSaveActions;	
		
		lSqlBeforeSaveActionPos int;
		lRecord record;
	BEGIN
		lSqlBeforeSaveActionPos = 9;
		
		--Обработваме ги отгоре- надолу, т.е. първо се викат екшъните на подобектите и след това на самите обекти		
		FOR lRecord IN 
			SELECT i.id as instance_id, a.eval_sql_function as function
			FROM pwt.document_object_instances i
			JOIN pwt.object_actions oa ON oa.object_id = i.object_id
			JOIN pwt.actions a ON a.id = oa.action_id
			WHERE i.id = ANY (pInstanceIds) AND oa.pos = lSqlBeforeSaveActionPos AND pMode = ANY(oa.execute_in_modes)
			ORDER BY i.pos DESC, oa.ord ASC
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || lRecord.instance_id || ', ' || pUid || ');';
		END LOOP;
		
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstancesBeforeSqlSaveActions(	
	pUid int,
	pInstanceIds bigint[],
	pMode int
) TO iusrpmt;


DROP TYPE ret_spPerformInstancesSqlAutoSaveActions CASCADE;
CREATE TYPE ret_spPerformInstancesSqlAutoSaveActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstancesSqlAutoSaveActions(
	pUid int,
	pInstanceIds bigint[],
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstancesSqlAutoSaveActions AS
$BODY$
	DECLARE
		lRes ret_spPerformInstancesSqlAutoSaveActions;	
		
		lSqlAutoSaveActionPos int;
		lRecord record;
	BEGIN
		lSqlAutoSaveActionPos = 8;		
		
		--Обработваме ги отгоре- надолу, т.е. първо се викат екшъните на подобектите и след това на самите обекти		
		FOR lRecord IN 
			SELECT i.id as instance_id, a.eval_sql_function as function
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances c ON c.document_id = i.document_id AND i.pos = substring(c.pos, 1, char_length(i.pos))
			JOIN pwt.object_actions oa ON oa.object_id = i.object_id
			JOIN pwt.actions a ON a.id = oa.action_id
			WHERE c.id = ANY (pInstanceIds) AND oa.pos = lSqlAutoSaveActionPos AND pMode = ANY(oa.execute_in_modes)
			ORDER BY i.pos DESC, oa.ord ASC
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || lRecord.instance_id || ', ' || pUid || ');';
		END LOOP;
		
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstancesSqlAutoSaveActions(	
	pUid int,
	pInstanceIds bigint[],
	pMode int
) TO iusrpmt;


DROP TYPE ret_spPerformInstancesSqlSaveActions CASCADE;
CREATE TYPE ret_spPerformInstancesSqlSaveActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spPerformInstancesSqlSaveActions(
	pUid int,
	pInstanceIds bigint[],
	pMode int DEFAULT 1
)
  RETURNS ret_spPerformInstancesSqlSaveActions AS
$BODY$
	DECLARE
		lRes ret_spPerformInstancesSqlSaveActions;	
		
		lSqlSaveActionPos int;
		lRecord record;
	BEGIN
		lSqlSaveActionPos = 4;
		-- Първо маркираме обектите (и родителите им нагоре), че вече не са нови
		FOR lRecord IN 
			SELECT id, i.pos, i.document_id 
			FROM pwt.document_object_instances i
			WHERE  i.id = ANY (pInstanceIds)  AND i.is_new = true
		LOOP	
			UPDATE pwt.document_object_instances SET
				is_new = false
			WHERE is_new = true AND document_id = lRecord.document_id AND pos = substring(lRecord.pos, 1, char_length(pos));
		END LOOP;
		
		--Обработваме ги отгоре- надолу, т.е. първо се викат екшъните на подобектите и след това на самите обекти		
		--За всеки от променяните обекти викаме и after save event-ите на parent-ите
		FOR lRecord IN 
			SELECT i.id as instance_id, a.eval_sql_function as function
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances c ON c.document_id = i.document_id AND i.pos = substring(c.pos, 1, char_length(i.pos))
			JOIN pwt.object_actions oa ON oa.object_id = i.object_id
			JOIN pwt.actions a ON a.id = oa.action_id
			WHERE c.id = ANY (pInstanceIds) AND oa.pos = lSqlSaveActionPos AND pMode = ANY(oa.execute_in_modes)
			ORDER BY i.pos DESC, oa.ord ASC
		LOOP
			EXECUTE 'SELECT * FROM ' || lRecord.function || '(' || lRecord.instance_id || ', ' || pUid || ');';
		END LOOP;
		
		
		lRes.result = 1;		

		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spPerformInstancesSqlSaveActions(	
	pUid int,
	pInstanceIds bigint[],
	pMode int
) TO iusrpmt;


-- Function: pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer)

-- DROP FUNCTION pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer);

CREATE OR REPLACE FUNCTION pwt.spuploadfigurephoto(poper integer, pid integer, pdocid integer, pplateid integer, ptitle character varying, pdesc character varying, pcreateuid integer, pfnupl character varying, pposition integer, pplateval integer)
  RETURNS pwt.ret_spuploadfigurephoto AS
$BODY$
DECLARE
	DECLARE lRes pwt.ret_spuploadfigurephoto;
	lPlate int;
	lMaxPosition int;
BEGIN
	
	IF (pOper = 1) THEN -- INSERT
		IF pPlateVal > 0 THEN
			SELECT INTO lPlate id
			FROM pwt.plates
			WHERE document_id = pDocId AND id = pPlateId;
			IF lPlate > 0 THEN
				SELECT INTO lMaxPosition max(move_position) FROM pwt.media WHERE document_id = pDocId AND plate_id = lPlate;
				IF lMaxPosition IS NULL THEN
					lMaxPosition := 1;
				END IF;
				INSERT INTO pwt.media (document_id, plate_id, title, description, usr_id, original_name, mimetype, position, move_position) 
				VALUES (pDocId, lPlate, pTitle, pDesc, pCreateUid, pFnupl, 'image/jpeg', pPosition, lMaxPosition);
				lRes.photo_id := currval('pwt.media_id_seq');
				lRes.plate_id := lPlate;
			ELSE
				INSERT INTO pwt.plates (document_id, title, description, format_type, createdate, lastmod, usr_id)
					VALUES (pDocId, pDesc, pDesc, pPlateVal, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, pCreateUid);
				lRes.plate_id := currval('pwt.plates_id_seq');
				SELECT INTO lMaxPosition max(move_position) FROM pwt.media WHERE document_id = pDocId;
				IF lMaxPosition IS NULL THEN
					lMaxPosition := 0;
				END IF;	
				INSERT INTO pwt.media (document_id, plate_id, title, description, usr_id, original_name, mimetype, position, move_position) 
					VALUES (pDocId, lRes.plate_id, pTitle, pDesc, pCreateUid, pFnupl, 'image/jpeg', pPosition, lMaxPosition + 1);
				lRes.photo_id := currval('pwt.media_id_seq');
			END IF;
		ELSE
			SELECT INTO lMaxPosition max(move_position) FROM pwt.media WHERE document_id = pDocId;
			IF lMaxPosition IS NULL THEN
				lMaxPosition := 0;
			END IF;
			IF coalesce(pPlateId, 0) > 0 THEN
				lPlate = pPlateId;
			END IF;
			INSERT INTO pwt.media (document_id, plate_id, title, description, usr_id, original_name, mimetype, move_position) 
					VALUES (pDocId, lPlate, pTitle, pDesc, pCreateUid, pFnupl, 'image/jpeg', lMaxPosition + 1);
				lRes.photo_id := currval('pwt.media_id_seq');
		END IF;
	ELSIF pOper = 2 THEN -- UPDATE
		UPDATE pwt.media SET 
					title = pTitle, 
					description = pDesc,
					original_name = pFnupl,
					lastmod = CURRENT_TIMESTAMP
				WHERE id = pId;			
		lRes.photo_id := pId;
	ELSIF pOper = 3 THEN -- DELETE
		DELETE FROM pwt.media WHERE id = pId;
	END IF;

	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer) TO public;
GRANT EXECUTE ON FUNCTION pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt.spuploadfigurephoto(integer, integer, integer, integer, character varying, character varying, integer, character varying, integer, integer) TO iusrpmt;
