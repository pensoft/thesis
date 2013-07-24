-- Function: system."spDeletePWTdocumentForReal"(integer, integer)

-- DROP FUNCTION system."spDeletePWTdocumentForReal"(integer, integer);

CREATE OR REPLACE FUNCTION system."spDeletePWTdocumentForReal"(pdocumentid integer, puid integer)
  RETURNS integer AS
$BODY$
	DECLARE
		lHasRights int;
		lRet int;
	BEGIN
		lRet := 0;
		SELECT INTO lHasRights count(id) FROM pwt.documents WHERE id = pdocumentid AND createuid = pUid;
		IF lHasRights > 0 THEN
			DELETE FROM pwt.msg 						WHERE document_id = pDocumentId;
			DELETE FROM pwt.document_users 				WHERE document_id = pDocumentId;
			DELETE FROM pwt.document_versions 			WHERE document_id = pDocumentId;
			DELETE FROM pwt.pjs_revision_details 		WHERE document_id = pDocumentId;
			DELETE FROM pwt.document_revisions 			WHERE document_id = pDocumentId;
			DELETE FROM pwt.instance_field_values 		WHERE document_id = pDocumentId;
			DELETE FROM pwt.citations 					WHERE document_id = pDocumentId;
			DELETE FROM pwt.activity 					WHERE document_id = pDocumentId;
			DELETE FROM pwt.document_object_instances 	WHERE document_id = pDocumentId;
			DELETE FROM pwt.document_template_objects 	WHERE document_id = pDocumentId;
			DELETE FROM pwt.lock_history 				WHERE document_id = pDocumentId;
			DELETE FROM pwt.media 						WHERE document_id = pDocumentId;
			DELETE FROM pwt.plates 						WHERE document_id = pDocumentId;
			DELETE FROM pwt.tables 						WHERE document_id = pDocumentId;
			DELETE FROM pwt.activity 					WHERE document_id = pDocumentId;
			DELETE FROM pwt.documents 					WHERE 		   id = pDocumentId;
			lRet := 1;
		END IF;
			RETURN lRet;
	END
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION system."spDeletePWTdocumentForReal"(integer, integer)
  OWNER TO postgres;
GRANT EXECUTE ON FUNCTION system."spDeletePWTdocumentForReal"(integer, integer) TO public;
GRANT EXECUTE ON FUNCTION system."spDeletePWTdocumentForReal"(integer, integer) TO postgres;
