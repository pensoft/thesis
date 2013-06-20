CREATE OR REPLACE FUNCTION spRemoveJournalArticleReferenceAuthors(	
	pReferenceInstanceId bigint,	
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
	BEGIN
		lWrapperObjectId = 97;
		lAuthorsHolderObjectId = 100;
		lSingleAuthorObjectId = 90;
		
		
		-- Първо взимаме id-то на wrapper-a
		SELECT INTO lWrapperInstanceId id 
		FROM pwt.document_object_instances 
		WHERE parent_id = pReferenceInstanceId AND object_id = lWrapperObjectId;
		
		-- След това взимаме id-то на подобекта, който е практически истинската референция
		SELECT INTO lWrapperSubobjectInstanceId id 
		FROM pwt.document_object_instances 
		WHERE parent_id = lWrapperInstanceId;
		
		-- След това взимаме id-то на подобекта, в който стоят авторите
		SELECT INTO lAuthorsHolderInstanceId id 
		FROM pwt.document_object_instances 
		WHERE parent_id = lWrapperSubobjectInstanceId AND object_id = lAuthorsHolderObjectId;
		
		
		SELECT INTO lMinCount min_occurrence 
		FROM pwt.object_subobjects
		WHERE object_id = lAuthorsHolderObjectId AND subobject_id = lSingleAuthorObjectId;
		
		
		-- След това махаме всички деца на този обект
		lIter = 1;
		FOR lRecord IN
			SELECT * FROM
			pwt.document_object_instances 
			WHERE parent_id = lAuthorsHolderInstanceId
		LOOP
			IF lIter <= lMinCount THEN --Ако имаме задължително няколко броя - на тях им сетваме стойностите да са празни
				UPDATE instance_field_values SET
					value_str = NULL,					
					value_int = NULL,
					value_arr_int = NULL,
					value_arr_str = NULL,
					value_date = NULL,
					value_arr_date = NULL
				WHERE instance_id = lRecord.id;
			ELSE -- Останалите ги махаме
				PERFORM spRemoveInstance(lRecord.id, pUID);
			END IF;
			lIter = lIter + 1;
		END LOOP;
		
	RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spRemoveJournalArticleReferenceAuthors(
	pReferenceInstanceId bigint,	
	pUID int
) TO iusrpmt;
