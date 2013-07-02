DROP TYPE pwt.ret_spStoreDocumentXml CASCADE;
CREATE TYPE pwt.ret_spStoreDocumentXml AS (
	result int
);

CREATE OR REPLACE FUNCTION pwt.spStoreDocumentXml(	
	pDocumentId int,
	pDocumentXml xml
)
  RETURNS pwt.ret_spStoreDocumentXml AS
$BODY$
DECLARE
	lRes pwt.ret_spStoreDocumentXml;
	lModifiedInstanceExists boolean;
BEGIN
	lRes.result = 1;
	
	lModifiedInstanceExists = false;
	
	IF EXISTS (
		SELECT *
		FROM pwt.document_object_instances
		WHERE document_id = pDocumentId AND is_modified = true AND is_confirmed = true
	)THEN 
		lModifiedInstanceExists = true;
	END IF;
	
	
	UPDATE pwt.documents SET
		doc_xml = pDocumentXml,
		xml_is_dirty = lModifiedInstanceExists
	WHERE id = pDocumentId;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spStoreDocumentXml(
	pDocumentId int,
	pDocumentXml xml
) TO iusrpmt;
