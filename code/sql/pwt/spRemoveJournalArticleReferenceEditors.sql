CREATE OR REPLACE FUNCTION spRemoveJournalArticleReferenceEditors(	
	pReferenceInstanceId bigint,	
	pUID int
)
RETURNS int AS
$BODY$
	DECLARE
	lWrapperObjectId bigint;
	lWrapperInstanceId bigint;
	lWrapperSubobjectInstanceId bigint;
	lSeriesEditorsObjectId bigint;
	lSeriesEditorsInstanceId bigint;
	lRecord record;
	BEGIN
		lWrapperObjectId = 97;
		lSeriesEditorsObjectId = 93;
		
		
		-- Първо взимаме id-то на wrapper-a
		SELECT INTO lWrapperInstanceId id 
		FROM pwt.document_object_instances 
		WHERE parent_id = pReferenceInstanceId AND object_id = lWrapperObjectId;
		
		-- След това взимаме id-то на подобекта, който е практически истинската референция
		SELECT INTO lWrapperSubobjectInstanceId id 
		FROM pwt.document_object_instances 
		WHERE parent_id = lWrapperInstanceId;
		
		-- След това взимаме id-то на подобекта, в който стоят едиторите
		SELECT INTO lSeriesEditorsInstanceId id 
		FROM pwt.document_object_instances 
		WHERE parent_id = lWrapperSubobjectInstanceId AND object_id = lSeriesEditorsObjectId;
		
		-- След това махаме всички деца на този обект
		FOR lRecord IN
			SELECT * FROM
			pwt.document_object_instances 
			WHERE parent_id = lSeriesEditorsInstanceId
		LOOP
			PERFORM spRemoveInstance(lRecord.id, pUID);
		END LOOP;
		
	RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spRemoveJournalArticleReferenceEditors(
	pReferenceInstanceId bigint,	
	pUID int
) TO iusrpmt;
