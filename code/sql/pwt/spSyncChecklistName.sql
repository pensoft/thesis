-- Function: pwt."spSyncChecklistName"(bigint, integer)

-- DROP FUNCTION pwt."spSyncChecklistName"(bigint, integer);

CREATE OR REPLACE FUNCTION pwt."spSyncChecklistName"(pinstanceid bigint, puid integer)
  RETURNS integer AS
$BODY$
	DECLARE
		cTitleFieldId bigint := 413;
		cObjectId bigint := 204;
		cMaxLen int := 40;

		lInstanceId bigint;
		lName varchar;
		lTempStr varchar;
	BEGIN
		-- Взимаме името
		SELECT INTO lTempStr value_str FROM pwt.instance_field_values WHERE instance_id = pInstanceId AND field_id = cTitleFieldId;
		
		lName = coalesce(lTempStr, '');
		
		-- Стрипваме таговете
		SELECT INTO lName * FROM public.spStripTags(lName);

		IF length(lName) = 0 THEN
			lName = 'Checklist';
		END IF;
		
		/*
		IF length(lName) > cMaxLen THEN
			lName = left(lName, cMaxLen) || '...';
		END IF;*/
			
		-- Взимаме ид-то на обекта
		SELECT INTO lInstanceId i.id 
		FROM pwt.document_object_instances i
		JOIN pwt.document_object_instances n ON n.document_id = i.document_id AND i.pos = substring(n.pos, 1, char_length(i.pos))
		WHERE i.object_id = cObjectId AND n.id = pInstanceId;
		
		-- Ъпдейтваме името
		UPDATE pwt.document_object_instances SET
			display_name = lName 
		WHERE id = lInstanceId;

		RETURN 1;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pwt."spSyncChecklistName"(bigint, integer)
  OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pwt."spSyncChecklistName"(bigint, integer) TO public;
GRANT EXECUTE ON FUNCTION pwt."spSyncChecklistName"(bigint, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pwt."spSyncChecklistName"(bigint, integer) TO iusrpmt;
