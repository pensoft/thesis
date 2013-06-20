DROP TYPE ret_spFinalizedArticlesData CASCADE;
CREATE TYPE ret_spFinalizedArticlesData AS (
	id int,
	title varchar,
	author varchar,
	createdate timestamp,
	lastmod timestamp,
	createuid int,
	content varchar,
	
	cached_title varchar,
	cached_authors varchar,
	cached_issue int,
	cached_fpage int,
	cached_lpage int,
	cached_doi varchar,
	cached_pensoft_id int,
	
	xml_sync_template_id int,
	journal_id int
);

CREATE OR REPLACE FUNCTION spFinalizedArticlesData(
	pOper int,
	pId int,
	pUID int,
	pTitle varchar,
	pAuthor varchar,
	pContent varchar,
	pStrippedContent varchar,
	
	pCachedTitle varchar,
	pCachedAuthors varchar,
	pCachedIssue int,
	pCachedDoi varchar,
	pCachedFpage int,
	pCachedLpage int,	
	pCachedPensoftId int,
	
	pXml_sync_template_id int,
	pJournalId int
)
  RETURNS ret_spFinalizedArticlesData AS
$BODY$
DECLARE	
	lRes ret_spFinalizedArticlesData;	
	lCurTime timestamp;	
BEGIN
lCurTime := current_timestamp;

IF NOT EXISTS (SELECT * FROM finalized_articles WHERE article_id = pId) THEN
	RAISE EXCEPTION 'admin.finalized_articles.thisArticleIsNotFinalized';
END IF;
IF pOper = 1 THEN -- Insert/Update	
	PERFORM spCreateArticleVersion(pId);
	
	UPDATE articles SET
		author = pAuthor,
		lastmod = now(),
		xml_content = pContent,		
		xml_sync_template_id = pXml_sync_template_id,
		journal_id = pJournalId
	WHERE id = pId;	
	
	UPDATE finalized_articles SET
		article_title = pCachedTitle, 
		article_authors = pCachedAuthors, 
		article_journal_issue = pCachedIssue, 
		article_fpage = pCachedFpage, 
		article_lpage = pCachedLpage, 
		article_doi = pCachedDoi, 
		article_pensoft_id = pCachedPensoftId
	WHERE article_id = pId;
	
	IF NOT EXISTS (SELECT * FROM article_vectors WHERE article_id = pId ) THEN
		INSERT INTO article_vectors(article_id, title_vector, all_vector)
		VALUES (
			pId, 
			to_tsvector('english', coalesce(pTitle, '')) 
				|| to_tsvector('english', coalesce(pAuthor, '')) ,
			to_tsvector('english', coalesce(pTitle, '')) 
				|| to_tsvector('english', coalesce(pAuthor, '')) 
				|| to_tsvector('english', coalesce(pStrippedContent, '')) 
		);
	ELSE 
		UPDATE article_vectors SET
			title_vector =  to_tsvector('english', coalesce(pTitle, '')) 
				|| to_tsvector('english', coalesce(pAuthor, '')) ,
			all_vector = to_tsvector('english', coalesce(pTitle, '')) 
				|| to_tsvector('english', coalesce(pAuthor, '')) 
				|| to_tsvector('english', coalesce(pStrippedContent, '')) 
		WHERE article_id = pId;
	END IF;
ELSEIF pOper = 3 THEN --delete
	DELETE FROM finalized_articles_exports WHERE article_id = pId;
	DELETE FROM finalized_articles WHERE article_id = pId;
	DELETE FROM export_common WHERE article_id = pId;	
	
	DELETE FROM article_vectors WHERE article_id = pId;
	DELETE FROM article_versions WHERE article_id = pId;
	DELETE FROM xml_sync WHERE article_id = pId;
	DELETE FROM articles WHERE id = pId;
END IF;


SELECT INTO lRes a.id, a.title, a.author, a.createdate, a.lastmod, a.createuid, a.xml_content, 
	f.article_title, f.article_authors, f.article_journal_issue, f.article_fpage, f.article_lpage, f.article_doi, f.article_pensoft_id,
	a.xml_sync_template_id, a.journal_id
FROM articles a 
JOIN finalized_articles f ON f.article_id = a.id
WHERE a.id = pId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spFinalizedArticlesData(
	pOper int,
	pId int,
	pUID int,
	pTitle varchar,
	pAuthor varchar,
	pContent varchar,
	pStrippedContent varchar,
	
	pCachedTitle varchar,
	pCachedAuthors varchar,
	pCachedIssue int,
	pCachedDoi varchar,
	pCachedFpage int,
	pCachedLpage int,	
	pCachedPensoftId int,
	
	pXml_sync_template_id int,
	pJournalId int
) TO iusrpmt;
