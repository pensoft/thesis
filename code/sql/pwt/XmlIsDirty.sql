CREATE OR REPLACE FUNCTION pwt."XmlIsDirty"(
	pOper int,
	pDocumentId int,	
	pInstanceId bigint
)
RETURNS int AS
$BODY$
	DECLARE		
		lRootInstanceId bigint;
	BEGIN
		
		IF pOper = 1 THEN
			-- get root instance id (so we can update is_modified flag)
			SELECT INTO lRootInstanceId doi1.id 
			FROM pwt.document_object_instances doi
			JOIN pwt.document_object_instances doi1 ON doi1.pos = substring(doi.pos from 1 for 2) AND doi1.document_id = pDocumentId
			WHERE doi.id = pInstanceId;
		
			-- SET XML to Dirty mode
			UPDATE pwt.documents SET xml_is_dirty = TRUE, generated_doc_html = 0 WHERE id = pDocumentId;
			UPDATE pwt.document_object_instances SET is_modified = TRUE WHERE id = lRootInstanceId AND document_id = pDocumentId;
		ELSE
			-- The XML is regenerated and we must clear the flags
			UPDATE pwt.documents SET xml_is_dirty = FALSE WHERE id = pDocumentId;
			UPDATE pwt.document_object_instances SET is_modified = FALSE WHERE document_id = pDocumentId;
		END IF;
		
		RETURN 1;
		
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt."XmlIsDirty"(
	pOper int,
	pDocumentId int,	
	pInstanceId bigint
) TO iusrpmt;
