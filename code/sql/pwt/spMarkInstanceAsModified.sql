DROP TYPE pwt.ret_spMarkInstanceAsModified CASCADE;
CREATE TYPE pwt.ret_spMarkInstanceAsModified AS (
	result int
);

CREATE OR REPLACE FUNCTION pwt.spMarkInstanceAsModified(
	pInstanceId bigint,
	pDocumentId int
)
  RETURNS pwt.ret_spMarkInstanceAsModified AS
$BODY$
DECLARE
	lRes pwt.ret_spMarkInstanceAsModified;	
BEGIN
	lRes.result = 1;
	
	UPDATE pwt.documents SET
		last_content_change = now()
	WHERE id = pDocumentId;

	
	IF EXISTS (
		SELECT *
		FROM pwt.document_object_instances 
		WHERE id = pInstanceId AND document_id = pDocumentId AND is_confirmed = true
	) THEN
		UPDATE pwt.document_object_instances p SET
			is_modified = true,
			lastmod_date = now()
		FROM pwt.document_object_instances c
		WHERE p.document_id = c.document_id AND c.pos ILIKE p.pos || '%'
			AND c.id = pInstanceId AND c.document_id = pDocumentId;
		
		
		UPDATE pwt.documents SET 
			xml_is_dirty = TRUE, 
			generated_doc_html = 0 
		WHERE id = pDocumentId;
	END IF;
	RETURN lRes;
	
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pwt.spMarkInstanceAsModified(
	pInstanceId bigint,
	pDocumentId int
) TO iusrpmt;
