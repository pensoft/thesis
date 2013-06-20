DROP TYPE ret_spDataPaperResourcesAfterCreationActions CASCADE;
CREATE TYPE ret_spDataPaperResourcesAfterCreationActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spDataPaperResourcesAfterCreationActions(
	pResourcesInstanceId bigint,	
	pUid int
)
  RETURNS ret_spDataPaperResourcesAfterCreationActions AS
$BODY$
DECLARE
	lRes ret_spDataPaperResourcesAfterCreationActions;
	lDataSetsObjectId bigint;
	lCurrentDataSetsNumFieldId bigint;
	lCurrentColumnsCount int;
BEGIN	
	lDataSetsObjectId = 141;
	lCurrentDataSetsNumFieldId = 404;
		
		
	SELECT INTO lCurrentColumnsCount count(*) FROM pwt.document_object_instances WHERE parent_id = pResourcesInstanceId AND object_id = lDataSetsObjectId;
	
	--Ъпдейтваме полето за брой на колоните
	UPDATE pwt.instance_field_values SET value_int = lCurrentColumnsCount WHERE instance_id = pResourcesInstanceId AND field_id = lCurrentDataSetsNumFieldId;
	
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spDataPaperResourcesAfterCreationActions(
	pResourcesInstanceId bigint,
	pUid int
) TO iusrpmt;
