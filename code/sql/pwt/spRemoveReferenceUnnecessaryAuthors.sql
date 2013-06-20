DROP TYPE ret_spRemoveReferenceUnnecessaryAuthors CASCADE;

CREATE TYPE ret_spRemoveReferenceUnnecessaryAuthors AS (
	result int
);

CREATE OR REPLACE FUNCTION spRemoveReferenceUnnecessaryAuthors(
	pInstanceId bigint, -- id на wrapper-a който държи авторите/редакторите
	pUid int
)
  RETURNS ret_spRemoveReferenceUnnecessaryAuthors AS
$BODY$
	DECLARE
		lRes ret_spRemoveReferenceUnnecessaryAuthors;	
		lCombinedNameFieldId bigint;
		lAuthorObjectId bigint;
		lEditorObjectId bigint;
		lRecord record;
		lMinOccurrence int;
		lNonEmptyAuthorsCount int;
	BEGIN
		lCombinedNameFieldId = 250;
		lAuthorObjectId = 90;	
		lEditorObjectId = 91;
		
		SELECT INTO lMinOccurrence os.min_occurrence
		FROM pwt.document_object_instances i 
		JOIN pwt.object_subobjects os ON os.object_id = i.object_id AND os.subobject_id IN (lAuthorObjectId, lEditorObjectId)
		WHERE i.id = pInstanceId
		LIMIT 1;
		
		
		SELECT INTO lNonEmptyAuthorsCount count(*) 
		FROM pwt.document_object_instances i
		JOIN pwt.instance_field_values fv ON fv.instance_id = i.id
		WHERE fv.field_id = lCombinedNameFieldId AND i.parent_id = pInstanceId AND i.object_id IN (lAuthorObjectId,lEditorObjectId)
			AND coalesce(trim(both from fv.value_str), '') <> '';
		
		-- RAISE NOTICE 'Count %, min %', lNonEmptyAuthorsCount, lMinOccurrence;
		--Ако имаме необходимата бройка попълнени автори - махаме тези с непопълнено име
		IF coalesce(lNonEmptyAuthorsCount, 0) >= lMinOccurrence THEN
			FOR lRecord IN
				SELECT i.id
				FROM pwt.document_object_instances i
				JOIN pwt.instance_field_values fv ON fv.instance_id = i.id
				WHERE fv.field_id = lCombinedNameFieldId AND i.parent_id = pInstanceId AND i.object_id IN (lAuthorObjectId,lEditorObjectId)
					AND coalesce(trim(both from fv.value_str), '') = ''
			LOOP
				PERFORM spRemoveInstance(lRecord.id, pUid);
			END LOOP;
		
		END IF;
		
		
		lRes.result = 1;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spRemoveReferenceUnnecessaryAuthors(	
	pInstanceId bigint,
	pUid int
) TO iusrpmt;
