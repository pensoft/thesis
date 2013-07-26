DROP TYPE ret_spSyncOldDocument CASCADE;
CREATE TYPE ret_spSyncOldDocument AS (
	result int
);

CREATE OR REPLACE FUNCTION spSyncOldDocument(
	pDocumentId int,
	pUid int
)
  RETURNS ret_spSyncOldDocument AS
$BODY$
DECLARE
	lRes ret_spSyncOldDocument;	
	lFigureWrapperObjectId bigint = 236;
	lTableWrapperObjectId bigint = 237;
BEGIN
	IF NOT EXISTS (
		SELECT *
		FROM pwt.document_template_objects 
		WHERE document_id = pDocumentId AND object_id = lFigureWrapperObjectId
	) THEN
		PERFORM spSyncDocumentObjectRoot(lFigureWrapperObjectId, pDocumentId, pUid);
		PERFORM spImportOldFigures(pDocumentId);
	END IF;
	
	IF NOT EXISTS (
		SELECT *
		FROM pwt.document_template_objects 
		WHERE document_id = pDocumentId AND object_id = lTableWrapperObjectId
	) THEN
		PERFORM spSyncDocumentObjectRoot(lTableWrapperObjectId, pDocumentId, pUid);
		PERFORM spImportOldTables(pDocumentId);
	END IF;
	
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
  
  GRANT EXECUTE ON FUNCTION spSyncOldDocument(
	pDocumentId int,
	pUid int
) TO iusrpmt;
