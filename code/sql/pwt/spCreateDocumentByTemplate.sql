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
			lSubmittingAuthorFieldId int;
			cAuthorUsrType CONSTANT int := 2;
		BEGIN	
			lActionAfterCreationPos = 5;
			lAuthorObjectId = 8;
			lAuthorRightsFieldId = 14;
			lSubmittingAuthorFieldId = 248;
			lDefaultAuthorRights = ARRAY[1];
			lAuthorMarkAsCorrespondingFieldId = 15;
			lAuthorMarkAsCorrespondingFieldIdValue = ARRAY[1];
			
			SELECT INTO lId nextval('pwt.documents_id_seq'::regclass);
			INSERT INTO pwt.documents(id, name, template_id, createuid, lastmoduid, state, papertype_id, journal_id) VALUES (lId, pDocumentName, pTemplateId, pUid, pUid, 1, pPaperType, pJournal_id);
			
			-- Първо запазваме цялата текуща структурата на шаблона.
			INSERT INTO pwt.document_template_objects(document_id, template_id, object_id, pos, display_in_tree, is_fake, allow_movement, allow_add, allow_remove, 
					display_title_and_top_actions, display_name, default_mode_id, default_new_mode_id, allowed_modes, display_default_actions, title_display_style,
					xml_node_name, display_object_in_xml, generate_xml_id, default_actions_type, displayed_actions_type, limit_new_object_creation, view_xpath_sel, view_xsl_templ_mode,
					template_object_id, create_in_popup, display_err
				)
				SELECT lId, pTemplateId, t.object_id, t.pos, t.display_in_tree, t.is_fake, t.allow_movement, t.allow_add, t.allow_remove, 
					t.display_title_and_top_actions, t.display_name, t.default_mode_id, t.default_new_mode_id, t.allowed_modes, t.display_default_actions, t.title_display_style,
					t.xml_node_name, t.display_object_in_xml, t.generate_xml_id, t.default_actions_type, t.displayed_actions_type, t.limit_new_object_creation, t.view_xpath_sel, t.view_xsl_templ_mode,
					t.id, t.create_in_popup, t.display_err
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
				INSERT INTO pwt.document_object_instances(id, document_id, object_id, pos, display_in_tree, document_template_object_id, display_name, display_err) 
					VALUES (lInstanceId, lId, lRecord.object_id, lCurrentPos, lRecord.display_in_tree, lRecord.id, lRecord.display_name, lRecord.display_err);
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
			INSERT INTO pwt.document_users(document_id, usr_id, first_name, middle_name, last_name, usr_type, ord)
				SELECT lId, pUid, first_name, middle_name, last_name, 2, 1 FROM usr WHERE id = pUid;
			
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
				
				-- submitting author
				UPDATE pwt.instance_field_values SET
				value_int = 1
				WHERE instance_id = lAuthorInstanceId AND field_id = lSubmittingAuthorFieldId;
				
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
