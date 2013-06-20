DROP TYPE ret_spSyncDocumentObjectFieldsRecursive CASCADE;
CREATE TYPE ret_spSyncDocumentObjectFieldsRecursive AS (
	result int
);

CREATE OR REPLACE FUNCTION spSyncDocumentObjectFieldsRecursive(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int
)
  RETURNS ret_spSyncDocumentObjectFieldsRecursive AS
$BODY$
DECLARE
	lRes ret_spSyncDocumentObjectFieldsRecursive;
	--lSid int;
	lRecord record;
BEGIN
	PERFORM spSyncDocumentObjectFields(pObjectId, pDocumentId, pUid);
	
	FOR lRecord IN 
		SELECT *
		FROM pwt.object_subobjects
		WHERE object_id = pObjectId
	LOOP
		PERFORM spSyncDocumentObjectFieldsRecursive(lRecord.subobject_id, pDocumentId, pUid);
	END LOOP;

	lRes.result = 1;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spSyncDocumentObjectFieldsRecursive(
	pObjectId bigint,
	pDocumentId bigint,
	pUid int
) TO iusrpmt;
