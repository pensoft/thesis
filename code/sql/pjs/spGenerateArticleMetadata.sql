CREATE OR REPLACE FUNCTION pjs."spGenerateArticleMetadata"(pDocumentId bigint)
  RETURNS int AS
$BODY$
	DECLARE
		cAuthorRoleType int := 11;
		lAuthors text;
		lAuthorsFormatNames text;
		lDocumentId bigint;
		lDocumentName text;
		lDocumentAbstract text;
		lDocumentKeywords text;
	BEGIN
		SELECT INTO lAuthors, lAuthorsFormatNames aggr_concat_coma(a.author_name), aggr_concat_semicolon(a.author_format_names) 
			FROM (
				SELECT (du.first_name || ' ' || du.last_name) as author_name, (du.last_name || ',' || du.first_name) as author_format_names 
				FROM pjs.document_users du
				WHERE du.document_id = pDocumentId AND du.role_id = cAuthorRoleType AND du.state_id = 1
				ORDER BY du.ord
			) as a;
		
		SELECT INTO
			lDocumentId, 
			lDocumentName,
			lDocumentAbstract,
			lDocumentKeywords
			
			d.id, 
			(regexp_replace(d.name::text, E'<[^>]*?>', '', 'g')), 
			(regexp_replace(d.abstract::text, E'<[^>]*?>', '', 'g')), 
			(regexp_replace(d.keywords::text, E'<[^>]*?>', '', 'g'))
		FROM pjs.documents d 
		WHERE d.id = pDocumentId 
			AND d.is_published = TRUE;
		
		IF(EXISTS(SELECT * FROM pjs.article_metadata WHERE document_id = pDocumentId)) THEN
			UPDATE pjs.article_metadata 
				SET
					title = lDocumentName,
					abstract = lDocumentAbstract,
					keywords = lDocumentKeywords,
					authors = lAuthors,
					authors_format_names = lAuthorsFormatNames
			WHERE document_id = pDocumentId;
		ELSE
			INSERT INTO pjs.article_metadata(document_id, title, abstract, keywords, authors, authors_format_names)
				VALUES(lDocumentId, lDocumentName, lDocumentAbstract, lDocumentKeywords, lAuthors, lAuthorsFormatNames);
		END IF;
		/*INSERT INTO pjs.article_metadata(document_id, title, abstract, keywords, authors, authors_format_names) 
		SELECT 
			d.id, 
			(regexp_replace(d.name::text, E'<[^>]*?>', '', 'g')), 
			(regexp_replace(d.abstract::text, E'<[^>]*?>', '', 'g')), 
			(regexp_replace(d.keywords::text, E'<[^>]*?>', '', 'g')), 
			lAuthors,
			lAuthorsFormatNames
		FROM pjs.documents d 
		WHERE d.id = pDocumentId 
			AND d.is_published = TRUE;
		*/
		RETURN 1;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION pjs."spGenerateArticleMetadata"(pDocumentId bigint) TO iusrpmt;
