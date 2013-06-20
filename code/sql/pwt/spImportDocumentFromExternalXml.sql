DROP TYPE ret_spImportDocumentFromExternalXml CASCADE;
CREATE TYPE ret_spImportDocumentFromExternalXml AS (
	result int
);

CREATE OR REPLACE FUNCTION spImportDocumentFromExternalXml(
	pDocumentId int,
	pXml xml,
	pUid int
)
  RETURNS ret_spImportDocumentFromExternalXml AS
$BODY$
DECLARE
	lRes ret_spImportDocumentFromExternalXml;
	
	lRootObjects xml[];
	lAuthorObjectId bigint;
	lSubmittingAuthorInstanceId int;
	lAuthorIdFieldId bigint;
	lSubmittingAuthorFieldId bigint;
	lIter int;
BEGIN
	lAuthorObjectId = 8;
	lAuthorIdFieldId = 13;
	lSubmittingAuthorFieldId = 248;
	
	lRootObjects := xpath('/document/objects/*[@is_object="1"]', pXml);
	
	FOR lIter IN 
		1 .. coalesce(array_upper(lRootObjects, 1), 0) 
	LOOP
		PERFORM spImportDocumentObjectFromXml(pDocumentId, lRootObjects[lIter], null, pUid);
	END LOOP;
	
	SELECT INTO lSubmittingAuthorInstanceId iv.instance_id
	FROM pwt.instance_field_values iv
	JOIN pwt.document_object_instances i ON i.id = iv.instance_id
	JOIN pwt.instance_field_values iv1 ON iv1.instance_id = i.id AND iv1.field_id = lSubmittingAuthorFieldId AND iv1.value_int = 1
	WHERE i.document_id = pDocumentId AND i.object_id = lAuthorObjectId AND iv.field_id = lAuthorIdFieldId;
	
	IF lSubmittingAuthorInstanceId IS NULL THEN
		RAISE EXCEPTION 'pwt.api.noSubmittingAuthorInXml';
	END IF;
	
	/*-- Set the submitting author to be the one specified in the xml, not the one importing the document
	-- Also mark the document as coming from api
	UPDATE pwt.documents SET
		imported_by_api = true, 
		import_api_uid = pUid		
	WHERE id = pDocumentId;
	*/
	
	lRes.result = 1;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spImportDocumentFromExternalXml(
	pDocumentId int,
	pXml xml,
	pUid int
) TO iusrpmt;
