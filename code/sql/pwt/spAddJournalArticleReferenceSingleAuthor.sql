
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
