DROP TYPE IF EXISTS ret_spDeleteDocument CASCADE;
CREATE TYPE ret_spDeleteDocument AS (
	result int
);

CREATE OR REPLACE FUNCTION spDeleteDocument(pDocumentId bigint)
  RETURNS ret_spDeleteDocument AS
$BODY$
	DECLARE
		lRes ret_spDeleteDocument;	
	BEGIN		

		-- setting current_round_id = null
		UPDATE pjs.documents SET current_round_id = NULL WHERE id = pDocumentId;
		
		-- setting decision_round_user_id = null
		UPDATE pjs.document_review_rounds SET decision_round_user_id = NULL, create_from_version_id = NULL WHERE document_id = pDocumentId;
		
		-- delete from document_user_invitations
		DELETE FROM pjs.document_user_invitations WHERE document_id = pDocumentId;
		
		DELETE FROM pjs.document_review_round_users_form 
		WHERE document_review_round_user_id IN (
			SELECT drru.id FROM pjs.document_review_round_users drru
			JOIN pjs.document_versions dv ON dv.id = drru.document_version_id AND dv.document_id = pDocumentId
		);
		
		-- delete from document_review_round_users
		DELETE FROM pjs.document_review_round_users WHERE document_version_id IN (SELECT id FROM pjs.document_versions WHERE document_id = pDocumentId);
		
		-- delete from document_review_rounds
		DELETE FROM pjs.document_review_rounds WHERE document_id = pDocumentId;
		
		-- delete from pwt_document_version_changes
		DELETE FROM pjs.pwt_document_version_changes 
		WHERE pwt_document_version_id IN (
			SELECT pdv.id FROM pjs.pwt_document_versions pdv 
			JOIN pjs.document_versions dv ON dv.id = pdv.version_id
			WHERE dv.document_id = pDocumentId
		);
		
		-- delete from pwt_document_versions
		DELETE FROM pjs.pwt_document_versions WHERE version_id IN (SELECT id FROM pjs.document_versions WHERE document_id = pDocumentId);
		
		-- delete from document_versions
		DELETE FROM pjs.document_versions WHERE document_id = pDocumentId;
		
		-- delete from document_users
		DELETE FROM pjs.document_users WHERE document_id = pDocumentId;
		
		-- delete from pwt_documents_msg
		DELETE FROM pjs.pwt_documents_msg WHERE document_id = pDocumentId;
		
		-- delete from pwt_documents
		DELETE FROM pjs.pwt_documents WHERE document_id = pDocumentId;
		
		-- delete from document_media
		DELETE FROM pjs.document_media WHERE document_id = pDocumentId;
		
		-- delete from documents
		DELETE FROM pjs.documents WHERE id = pDocumentId;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spDeleteDocument(pDocumentId bigint) TO iusrpmt;
