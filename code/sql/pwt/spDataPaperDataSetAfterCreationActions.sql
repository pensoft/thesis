DROP TYPE ret_spDataPaperDataSetAfterCreationActions CASCADE;
CREATE TYPE ret_spDataPaperDataSetAfterCreationActions AS (
	result int
);

CREATE OR REPLACE FUNCTION spDataPaperDataSetAfterCreationActions(
	pDataSetInstanceId bigint,	
	pUid int
)
  RETURNS ret_spDataPaperDataSetAfterCreationActions AS
$BODY$
DECLARE
	lRes ret_spDataPaperDataSetAfterCreationActions;
	lColumnObjectId bigint;
	lCurrentColumnsNumFieldId bigint;
	lCurrentColumnsCount int;
BEGIN	
	lColumnObjectId = 142;
	lCurrentColumnsNumFieldId = 403;
		
		
	SELECT INTO lCurrentColumnsCount count(*) FROM pwt.document_object_instances WHERE parent_id = pDataSetInstanceId AND object_id = lColumnObjectId;
	
	--Ъпдейтваме полето за брой на колоните
	UPDATE pwt.instance_field_values SET value_int = lCurrentColumnsCount WHERE instance_id = pDataSetInstanceId AND field_id = lCurrentColumnsNumFieldId;
	
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spDataPaperDataSetAfterCreationActions(
	pDataSetInstanceId bigint,
	pUid int
) TO iusrpmt;
