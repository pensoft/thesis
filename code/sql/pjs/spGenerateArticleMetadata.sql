CREATE OR REPLACE FUNCTION pjs."spGenerateArticleMetadata"(pDocumentId bigint)
  RETURNS int AS
$BODY$
	DECLARE
		cAuthorRoleType int := 11;
		lAuthors varchar;
	BEGIN
		SELECT INTO lAuthors aggr_concat_coma(a.author_name)
			FROM (
				SELECT (du.first_name || ' ' || du.last_name) as author_name 
				FROM pjs.document_users du
				WHERE du.document_id = pDocumentId AND du.role_id = cAuthorRoleType AND du.state_id = 1
				ORDER BY du.ord
			) as a;
		
		INSERT INTO pjs.article_metadata(document_id, title, abstract, keywords, authors) 
		SELECT 
			d.id, 
			(regexp_replace(d.name::text, E'<[^>]*?>', '', 'g')), 
			(regexp_replace(d.abstract::text, E'<[^>]*?>', '', 'g')), 
			(regexp_replace(d.keywords::text, E'<[^>]*?>', '', 'g')), 
			lAuthors
		FROM pjs.documents d 
		WHERE d.id = pDocumentId 
			AND d.is_published = TRUE;
		
		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spGenerateArticleMetadata"(pDocumentId bigint) TO iusrpmt;
