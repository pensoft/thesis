DROP TYPE ret_spCreateArticle CASCADE;
CREATE TYPE ret_spCreateArticle AS (
	article_id bigint
);

CREATE OR REPLACE FUNCTION spCreateArticle(
	pDocumentId bigint,	
	pDocumentXml varchar
)
  RETURNS ret_spCreateArticle AS
$BODY$
	DECLARE		
		lRes ret_spCreateArticle;				
	BEGIN				
		IF NOT EXISTS (
			SELECT * 
			FROM pjs.articles
			WHERE id = pDocumentId 
		) THEN
			IF NOT EXISTS (
				SELECT * 
				FROM pjs.documents
				WHERE id = pDocumentId 
			) THEN
				RAISE EXCEPTION 'pjs.noSuchDocument';
			END IF;
			
			INSERT INTO pjs.articles(id, pwt_document_id)
				SELECT d.id, pwt_id
				FROM pjs.documents d 
				JOIN pjs.pwt_documents pd ON pd.document_id = d.id
				WHERE d.id = pDocumentId;
			
		END IF;
		PERFORM spSaveArticleXml(pDocumentId, pDocumentXml);
			
		lRes.article_id = pDocumentId;
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spCreateArticle(
	pDocumentId bigint,	
	pDocumentXml varchar
) TO iusrpmt;
