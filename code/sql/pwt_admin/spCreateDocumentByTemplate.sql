	DROP TYPE ret_spCreateDocumentByTemplate CASCADE;
	CREATE TYPE ret_spCreateDocumentByTemplate AS (
		id int
	);


	CREATE OR REPLACE FUNCTION pwt.spCreateDocumentByTemplate(
		pTemplateId int,
		pDocumentName varchar,
		pPaperType int,
		pJournal_id int,
		pUid int
	)
	  RETURNS ret_spCreateDocument AS
	$BODY$
		DECLARE
			lRes ret_spCreateDocument;		
			lId int;
			lRecord record;
			lRecord2 record;
			lInstanceId bigint;
			lCurrentPos varchar;
			lActionAfterCreationPos bigint;
		BEGIN	
			lActionAfterCreationPos = 5;
			
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
			
			-- След това създаваме default-ен автор
			PERFORM spCreateDocumentDefaultAuthor(lId, pUid);
				
			lRes.id = lId;
			RETURN lRes;
		END
	$BODY$
	  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

	GRANT EXECUTE ON FUNCTION spCreateDocumentByTemplate(
		pTemplateId int,
		pDocumentName varchar,
		pUid int
	) TO iusrpmt;
