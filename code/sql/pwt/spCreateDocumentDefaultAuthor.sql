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
