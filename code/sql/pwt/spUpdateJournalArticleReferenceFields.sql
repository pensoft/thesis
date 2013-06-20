CREATE OR REPLACE FUNCTION pwt."spUpdateJournalArticleReferenceFields"(	
	pReferenceInstanceId bigint,
	pPubYear int,
	pArticleTitle varchar,
	pJournal varchar,
	pBookVolume varchar,
	pIssue int,
	pFirstPage varchar,
	pLastPage varchar,
	pUrl varchar,
	pDoi varchar
)
RETURNS int AS
$BODY$
	DECLARE
	cPubYearFieldId CONSTANT bigint := 254;
	cArticleTitleFieldId CONSTANT bigint := 276;
	cJournalFieldId CONSTANT bigint := 243;
	cBookVolumeFieldId CONSTANT bigint := 258;
	cIssueFieldId CONSTANT bigint := 27;
	cFirstPageFieldId CONSTANT bigint := 28;
	cLastPageFieldId CONSTANT bigint := 29;
	cUrlFieldId CONSTANT bigint := 263;
	cDoiFieldId CONSTANT bigint := 30;
	
	cWrapperObjectId CONSTANT bigint := 97;
	lWrapperInstanceId bigint;
	lWrapperSubobjectInstanceId bigint;
	BEGIN

		-- Първо взимаме id-то на wrapper-a
		SELECT INTO lWrapperInstanceId id 
		FROM pwt.document_object_instances 
		WHERE parent_id = pReferenceInstanceId AND object_id = cWrapperObjectId;
		
		-- След това взимаме id-то на подобекта, който е практически истинската референция
		SELECT INTO lWrapperSubobjectInstanceId id 
		FROM pwt.document_object_instances 
		WHERE parent_id = lWrapperInstanceId;
		
		--След това ъпдейтваме field-овете
		UPDATE pwt.instance_field_values SET
			value_int = pPubYear
		WHERE instance_id = lWrapperSubobjectInstanceId AND field_id = cPubYearFieldId;
		
		UPDATE pwt.instance_field_values SET
			value_str = pArticleTitle
		WHERE instance_id = lWrapperSubobjectInstanceId AND field_id = cArticleTitleFieldId;
		
		UPDATE pwt.instance_field_values SET
			value_str = pJournal
		WHERE instance_id = lWrapperSubobjectInstanceId AND field_id = cJournalFieldId;
		
		UPDATE pwt.instance_field_values SET
			value_str = pBookVolume
		WHERE instance_id = lWrapperSubobjectInstanceId AND field_id = cBookVolumeFieldId;
		
		UPDATE pwt.instance_field_values SET
			value_int = pIssue
		WHERE instance_id = lWrapperSubobjectInstanceId AND field_id = cIssueFieldId;
		
		UPDATE pwt.instance_field_values SET
			value_str = pFirstPage
		WHERE instance_id = lWrapperSubobjectInstanceId AND field_id = cFirstPageFieldId;
		
		UPDATE pwt.instance_field_values SET
			value_str = pLastPage
		WHERE instance_id = lWrapperSubobjectInstanceId AND field_id = cLastPageFieldId;
		
		UPDATE pwt.instance_field_values SET
			value_str = pUrl
		WHERE instance_id = lWrapperSubobjectInstanceId AND field_id = cUrlFieldId;		
		
		UPDATE pwt.instance_field_values SET
			value_str = pDoi
		WHERE instance_id = lWrapperSubobjectInstanceId AND field_id = cDoiFieldId;
		
	RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt."spUpdateJournalArticleReferenceFields"(
	pReferenceInstanceId bigint,
	pPubYear int,
	pArticleTitle varchar,
	pJournal varchar,
	pBookVolume varchar,
	pIssue int,
	pFirstPage varchar,
	pLastPage varchar,
	pUrl varchar,
	pDoi varchar
) TO iusrpmt;
