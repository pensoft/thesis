DROP TYPE IF EXISTS ret_spDeleteDocument CASCADE;
CREATE TYPE ret_spDeleteDocument AS (
	result int
);

CREATE OR REPLACE FUNCTION pjs."spDeleteDocument"(pDocumentId bigint)
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
		
		-- delete from pjs.poll_answers 
		DELETE FROM pjs.poll_answers 
		WHERE rel_element_type = 1 AND rel_element_id IN (
			SELECT
				drruf.id 
			FROM pjs.document_review_round_users_form drruf
			JOIN pjs.document_review_round_users drru ON drru.id = drruf.document_review_round_user_id
			JOIN pjs.document_versions dv ON dv.id = drru.document_version_id AND dv.document_id = pDocumentId
		);
		
		-- delete from pjs.document_review_round_users_form 
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

		delete from pjs.msg where document_id = pDocumentId;
		
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
		
		UPDATE pjs.articles 
		SET 
			figures_list_cache_id = NULL,
			tables_list_cache_id = NULL,
			sup_files_list_cache_id = NULL,
			references_list_cache_id = NULL,
			taxon_list_cache_id = NULL,
			authors_list_cache_id = NULL,
			contents_list_cache_id = NULL,
			xml_cache_id = NULL,
			preview_cache_id = NULL,
			localities_list_cache_id = NULL,
			citation_list_cache_id = NULL
		WHERE id = pDocumentId;
		
		-- delete from article_authors
		DELETE FROM pjs.article_authors WHERE article_id = pDocumentId;
		
		-- delete from pjs.article_figures
		DELETE FROM pjs.article_figures WHERE article_id = pDocumentId;
		
		-- delete from pjs.article_metadata
		DELETE FROM pjs.article_metadata WHERE document_id = pDocumentId;
		
		-- delete from pjs.article_references
		DELETE FROM pjs.article_references WHERE article_id = pDocumentId;
		
		-- delete from pjs.article_sup_files
		DELETE FROM pjs.article_sup_files WHERE article_id = pDocumentId;
		
		-- delete from pjs.article_tables
		DELETE FROM pjs.article_tables WHERE article_id = pDocumentId;
		
		-- delete from pjs.article_taxons
		DELETE FROM pjs.article_taxons WHERE article_id = pDocumentId;
		
		-- delete from pjs.article_instance_localities 
		DELETE FROM pjs.article_instance_localities 
		WHERE locality_id IN (SELECT al.id FROM pjs.article_localities al WHERE article_id = pDocumentId);
		
		-- delete from pjs.article_localities
		DELETE FROM pjs.article_localities WHERE article_id = pDocumentId;
		
		-- delete from pjs.article_cached_items
		DELETE FROM pjs.article_cached_items WHERE article_id = pDocumentId;
		
		-- delete from pjs.articles
		DELETE FROM pjs.articles WHERE id = pDocumentId;
		
		-- delete from documents
		DELETE FROM pjs.documents WHERE id = pDocumentId;
		
		lRes.result = 1;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spDeleteDocument"(pDocumentId bigint) TO iusrpmt;
