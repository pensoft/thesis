CREATE OR REPLACE FUNCTION pwt.spGetDocumentTitle(pDocumentId bigint, pInstanceId bigint)
RETURNS int AS
$BODY$
	
	DECLARE
		lTitleValID bigint;
		lTitleObjectID int[];
		lTitleVal varchar;
	BEGIN
		lTitleObjectID = ARRAY[9, 153]::int[];
		lTitleValID = 3;
		
		IF (pDocumentId IS NULL) THEN
			SELECT INTO pDocumentId document_id FROM pwt.document_object_instances WHERE id = pInstanceId;
		END IF;

		
		SELECT INTO lTitleVal max(value_str)
			FROM pwt.v_getfieldsbyobjects 
			WHERE 
				document_id = pDocumentId
				AND object_id = ANY (lTitleObjectID)
				AND field_id = lTitleValID;
				
		UPDATE pwt.documents 
			SET name = COALESCE(lTitleVal, name)
			WHERE
				id = pDocumentId;
		
	
	RETURN 1;
	END

$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spGetDocumentTitle(
	pDocumentId bigint, 
	pInstanceId bigint
) TO iusrpmt;
