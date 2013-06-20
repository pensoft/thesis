DROP TYPE ret_spRemoveDuplicateUsers CASCADE;

CREATE TYPE ret_spRemoveDuplicateUsers AS (
	result int
);

/**
	Премахваме дубликираните автори
*/
CREATE OR REPLACE FUNCTION spRemoveDuplicateUsers()
  RETURNS ret_spRemoveDuplicateUsers AS
$BODY$
	DECLARE
		lRes ret_spRemoveDuplicateUsers;	
		
		lRecord record;
		lActiveUid bigint;
		lAuthorIdFieldId bigint;
		lAuthorObjectId bigint;
		lContributorObjectId bigint;
	BEGIN
		lAuthorIdFieldId = 13;
		lAuthorObjectId = 8;
		lContributorObjectId = 12;
		
		FOR lRecord IN
			SELECT trim(lower(uname)) as uname, count(*) FROM usr 
			WHERE state >= 0
			GROUP BY trim(lower(uname)) HAVING count(*) > 1
		LOOP
			-- Взимаме ид-то на юзъра, който няма да трием
			SELECT INTO lActiveUid id
			FROM usr
			WHERE trim(lower(uname)) = lRecord.uname AND state >= 0
			ORDER BY state DESC, char_length(uname) ASC
			LIMIT 1;
			
			RAISE NOTICE 'Email %, ActiveId %', lRecord.uname, lActiveUid;
			
			
			-- Сменяме ид-тата на дубликатите при авторите на статии
			UPDATE pwt.document_users du SET
				usr_id = lActiveUid
			FROM public.usr u
			WHERE trim(lower(u.uname)) = lRecord.uname
			AND u.id = du.usr_id AND u.id <> lActiveUid;
			
			-- Сменяме и id-тата в самите документи
			UPDATE pwt.instance_field_values fv
			SET value_int = lActiveUid
			FROM pwt.document_object_instances i
			JOIN public.usr u ON u.id <> lActiveUid
			WHERE u.id = fv.value_int AND
				i.id = fv.instance_id 
				AND fv.field_id = lAuthorIdFieldId 
				AND i.object_id IN (lAuthorObjectId, lContributorObjectId)
				AND trim(lower(u.uname)) = lRecord.uname;
			
			-- Накрая трием дубликатите
			DELETE FROM public.usr 
			WHERE trim(lower(uname)) = lRecord.uname AND state >= 0
			AND id <> lActiveUid;
			
			
		END LOOP;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spRemoveDuplicateUsers() TO iusrpmt;
