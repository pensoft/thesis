DROP TYPE ret_spFinalizeArticle CASCADE;
CREATE TYPE ret_spFinalizeArticle AS (
	result int
);


CREATE OR REPLACE FUNCTION spFinalizeArticle(
	pId int,
	pUID int,
	pTitle varchar,
	pAuthors varchar,
	pJournalId int,
	pIssue int,
	pFpage int,
	pLpage int,
	pDoi varchar,
	pPensoftId int
)
  RETURNS ret_spFinalizeArticle AS
$BODY$
DECLARE
	lRes ret_spFinalizeArticle;
	lIsFinalized int;
	lJournalExports int[];
	lJournalWikiUserId int;
	lExportId int;
	lExportType int;
	lIter int;
	
	lKeysExportType int;
	lEolExportType int;
	lWikiExportType int;
BEGIN
	SELECT INTO lKeysExportType id FROM export_types WHERE name = 'keys';
	SELECT INTO lEolExportType id FROM export_types WHERE name = 'eol';
	SELECT INTO lWikiExportType id FROM export_types WHERE name = 'wiki';
	
	SELECT INTO lIsFinalized is_finalized FROM articles WHERE id = pId;
	IF lIsFinalized = 1 THEN
		RAISE EXCEPTION 'articles.articleIsAlreadyFinalized';
	END IF;
	
	IF EXISTS (SELECT * FROM finalized_articles WHERE article_id = pId) THEN
		RAISE EXCEPTION 'articles.thereIsARecordForThisArticleInFinalizedArticles';
	END IF;
	
	UPDATE articles SET 
		is_finalized = 1,
		journal_id = pJournalId,
		lastmod = now()
	WHERE id = pId;
	
	INSERT INTO finalized_articles(article_id, article_title, article_authors, article_journal_issue, article_fpage, article_lpage, article_doi, article_pensoft_id)
		VALUES 					(pId, pTitle, pAuthors, pIssue, pFpage, pLpage, pDoi, pPensoftId);
	
	SELECT INTO lJournalExports, lJournalWikiUserId export_types, wiki_username_id FROM journals WHERE id = pJournalId;
	
	FOR lIter in 1 .. array_upper( lJournalExports, 1 ) LOOP
		lExportType = lJournalExports[lIter];
		IF NOT EXISTS (SELECT * FROM finalized_articles_exports WHERE article_id = pId AND export_type_id = lExportType) THEN
			IF lExportType = lEolExportType THEN
				SELECT INTO lExportId nextval('export_common_id_seq'::regclass);
				INSERT INTO eol_export(id, title, article_id, createuid) VALUES (lExportId, 'Eol export for article ' || pId::varchar, pId, pUID);
				INSERT INTO finalized_articles_exports(article_id, export_type_id, export_id) VALUES (pId, lExportType, lExportId);
			ELSIF lExportType = lKeysExportType THEN 
				SELECT INTO lExportId nextval('export_common_id_seq'::regclass);
				INSERT INTO keys_export(id, title, article_id, createuid) VALUES (lExportId, 'Keys export for article ' || pId::varchar, pId, pUID);
				INSERT INTO finalized_articles_exports(article_id, export_type_id, export_id) VALUES (pId, lExportType, lExportId);
			ELSIF lExportType = lWikiExportType THEN 
				SELECT INTO lExportId nextval('export_common_id_seq'::regclass);
				INSERT INTO wiki_export(id, title, article_id, createuid, wiki_username_id) VALUES (lExportId, 'Wiki export for article ' || pId::varchar, pId, pUID, lJournalWikiUserId);
				INSERT INTO finalized_articles_exports(article_id, export_type_id, export_id) VALUES (pId, lExportType, lExportId);
			END IF;
		END IF;
	END LOOP;
	
	lRes.result = 1;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spFinalizeArticle(
	pId int,
	pUID int,
	pTitle varchar,
	pAuthors varchar,
	pJournalId int,
	pIssue int,
	pFpage int,
	pLpage int,
	pDoi varchar,
	pPensoftId int
) TO iusrpmt;
