ALTER TABLE journals ADD COLUMN title_abrev text;
ALTER TABLE journals ADD COLUMN issn_print text;
ALTER TABLE journals ADD COLUMN issn_online text;
ALTER TABLE journals ADD COLUMN publisher text;

ALTER TABLE articles ADD COLUMN journal_id int REFERENCES journals(id);

DROP TYPE ret_spArticlesData CASCADE;
CREATE TYPE ret_spArticlesData AS (
	id int,
	title varchar,
	author varchar,
	createdate timestamp,
	lastmod timestamp,
	createuid int,
	content varchar,
	meta_identifier varchar,
	meta_identifier_type varchar,
	meta_pub_year int,
	meta_title varchar,
	meta_authors varchar,
	meta_url_of_pdf varchar,
	meta_part_of_host_publication int,
	meta_journal_type int,
	meta_start_page int,
	meta_end_page int,
	meta_book_title varchar,
	meta_journal_name varchar,
	meta_journal_volume_number varchar,
	meta_publisher_name varchar,
	meta_publisher_location varchar,
	xml_sync_template_id int,
	journal_id int
);

CREATE OR REPLACE FUNCTION spArticlesData(
	pOper int,
	pId int,
	pUID int,
	pTitle varchar,
	pAuthor varchar,
	pContent varchar,
	pStrippedContent varchar,
	pMeta_identifier varchar,
	pMeta_identifier_type varchar,
	pMeta_pub_year int,
	pMeta_title varchar,
	pMeta_authors varchar,
	pMeta_url_of_pdf varchar,
	pMeta_part_of_host_publication int,
	pMeta_journal_type int,
	pMeta_start_page int,
	pMeta_end_page int,
	pMeta_book_title varchar,
	pMeta_journal_name varchar,
	pMeta_journal_volume_number varchar,
	pMeta_publisher_name varchar,
	pMeta_publisher_location varchar,
	pXml_sync_template_id int,
	pJournalId int
)
  RETURNS ret_spArticlesData AS
$BODY$
DECLARE
lItemName varchar;
lRes ret_spArticlesData;
--lSid int;
lCurTime timestamp;
lId int;
lTypeJournal int;
lTypeBook int;
BEGIN
lCurTime := current_timestamp;

lTypeBook = 2;
lTypeJournal = 1;

lId = pId;
IF pOper = 1 THEN -- Insert/Update
	IF pId IS NULL THEN --Insert
		INSERT INTO articles(
				title, 
				author,
				xml_content, 
				createuid, 
				meta_identifier,
				meta_identifier_type,
				meta_pub_year,
				meta_title,
				meta_authors,
				meta_url_of_pdf,
				meta_part_of_host_publication,
				meta_journal_type,
				meta_start_page,
				meta_end_page,
				meta_book_title,
				meta_journal_name,
				meta_journal_volume_number,
				meta_publisher_name, 
				meta_publisher_location,
				xml_sync_template_id,
				journal_id
			) 
			VALUES (
				pTitle, 
				pAuthor, 
				pContent, 
				pUID, 
				pMeta_identifier,
				pMeta_identifier_type,
				pMeta_pub_year,
				pMeta_title,pMeta_authors,
				pMeta_url_of_pdf,
				pMeta_part_of_host_publication,
				pMeta_journal_type,
				CASE WHEN coalesce(pMeta_part_of_host_publication, 0) = 1 THEN pMeta_start_page ELSE null END,
				CASE WHEN coalesce(pMeta_part_of_host_publication, 0) = 1 THEN pMeta_end_page ELSE null END,
				CASE WHEN pMeta_journal_type = lTypeBook AND coalesce(pMeta_part_of_host_publication, 0) = 1 THEN pMeta_book_title ELSE null END,
				CASE WHEN pMeta_journal_type = lTypeJournal THEN pMeta_journal_name ELSE null END,
				CASE WHEN pMeta_journal_type = lTypeJournal THEN pMeta_journal_volume_number ELSE null END,
				CASE WHEN pMeta_journal_type = lTypeBook THEN pMeta_publisher_name ELSE null END, 
				CASE WHEN pMeta_journal_type = lTypeBook THEN pMeta_publisher_location ELSE null END,
				pXml_sync_template_id,
				pJournalId
			);
		lId = currval('articles_id_seq');
	ELSE -- Update
		PERFORM spCreateArticleVersion(pId);
		
		UPDATE articles SET
			author = pAuthor,
			lastmod = now(),
			xml_content = pContent,
			meta_identifier = pMeta_identifier,
			meta_identifier_type = pMeta_identifier_type,
			meta_pub_year = pMeta_pub_year,
			meta_title = pMeta_title,
			meta_authors = pMeta_authors,
			meta_url_of_pdf = pMeta_url_of_pdf,
			meta_part_of_host_publication = coalesce(pMeta_part_of_host_publication, 0),
			meta_journal_type = pMeta_journal_type,
			meta_start_page = CASE WHEN coalesce(pMeta_part_of_host_publication, 0) = 1 THEN pMeta_start_page ELSE null END,
			meta_end_page = CASE WHEN coalesce(pMeta_part_of_host_publication, 0) = 1 THEN pMeta_end_page ELSE null END,
			meta_book_title = CASE WHEN pMeta_journal_type = lTypeBook AND coalesce(pMeta_part_of_host_publication, 0) = 1 THEN pMeta_book_title ELSE null END,
			meta_journal_name = CASE WHEN pMeta_journal_type = lTypeJournal THEN pMeta_journal_name ELSE null END,
			meta_journal_volume_number = CASE WHEN pMeta_journal_type = lTypeJournal THEN pMeta_journal_volume_number ELSE null END,
			meta_publisher_name = CASE WHEN pMeta_journal_type = lTypeBook THEN pMeta_publisher_name ELSE null END,
			meta_publisher_location = CASE WHEN pMeta_journal_type = lTypeBook THEN pMeta_publisher_location ELSE null END,
			xml_sync_template_id = pXml_sync_template_id,
			journal_id = pJournalId
		WHERE id = pId;
	END IF;
	
	IF NOT EXISTS (SELECT * FROM article_vectors WHERE article_id = lId ) THEN
		INSERT INTO article_vectors(article_id, title_vector, all_vector)
		VALUES (
			lId, 
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
		WHERE article_id = lId;
	END IF;
ELSEIF pOper = 3 THEN --delete
	DELETE FROM article_vectors WHERE article_id = pId;
	DELETE FROM article_versions WHERE article_id = pId;
	DELETE FROM xml_sync WHERE article_id = pId;
	DELETE FROM articles WHERE id = pId;
END IF;

SELECT INTO lRes id, title, author, createdate, lastmod, createuid, xml_content, meta_identifier,meta_identifier_type,meta_pub_year,meta_title,meta_authors,meta_url_of_pdf,meta_part_of_host_publication,meta_journal_type,meta_start_page,meta_end_page,meta_book_title,meta_journal_name,meta_journal_volume_number,meta_publisher_name, meta_publisher_location, xml_sync_template_id, journal_id
FROM articles WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spArticlesData(
	pOper int,
	pId int,
	pUID int,
	pTitle varchar,
	pAuthor varchar,
	pContent varchar,
	pStrippedContent varchar,
	pMeta_identifier varchar,
	pMeta_identifier_type varchar,
	pMeta_pub_year int,
	pMeta_title varchar,
	pMeta_authors varchar,
	pMeta_url_of_pdf varchar,
	pMeta_part_of_host_publication int,
	pMeta_journal_type int,
	pMeta_start_page int,
	pMeta_end_page int,
	pMeta_book_title varchar,
	pMeta_journal_name varchar,
	pMeta_journal_volume_number varchar,
	pMeta_publisher_name varchar,
	pMeta_publisher_location varchar,
	pXml_sync_template_id int,
	pJournalId int
) TO iusrpmt;


DROP TYPE ret_spJournals CASCADE;
CREATE TYPE ret_spJournals AS (
	id int,
	name varchar,
	pensoft_id int,
	pensoft_title varchar,
	xml_file_name varchar,
	title_abrev varchar,
	issn_print varchar,
	issn_online varchar,
	publisher varchar
);

CREATE OR REPLACE FUNCTION spJournals(
	pOper int,
	pId int,
	pName varchar,
	pPensoftId int,
	pPensoftTitle varchar,
	pXmlFileName varchar,
	pTitleAbrev varchar,
	pIssnPrint varchar,
	pIssnOnline varchar,
	pPublisher varchar
)
  RETURNS ret_spJournals AS
$BODY$
DECLARE
lRes ret_spJournals;
--lSid int;
lCurTime timestamp;
lId int;
BEGIN

lId = pId;
IF pOper = 1 THEN -- Insert/Update	
	IF lId IS NULL THEN --Insert
		INSERT INTO journals(
			name, pensoft_id, pensoft_title, xml_file_name,
			title_abrev, issn_print, issn_online, publisher
		) VALUES (
			pName, pPensoftId, pPensoftTitle, pXmlFileName,
			pTitleAbrev, pIssnPrint, pIssnOnline, pPublisher
		);
		lId = currval('journals_id_seq');
	ELSE -- Update
		UPDATE journals SET
			name = pName,
			pensoft_id = pPensoftId,
			pensoft_title = pPensoftTitle,
			xml_file_name = pXmlFileName,
			title_abrev = pTitleAbrev,
			issn_print = pIssnPrint,
			issn_online = pIssnOnline,
			publisher = pPublisher
		WHERE id = pId;
	END IF;
ELSEIF pOper = 3 THEN
	DELETE FROM journals WHERE id = lId;
ELSEIF pOper = 4 THEN --Copy	
END IF;


SELECT INTO lRes id, name, pensoft_id, pensoft_title, xml_file_name, title_abrev, issn_print, issn_online, publisher FROM journals WHERE id = lId;


RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spJournals(
	pOper int,
	pId int,
	pName varchar,
	pPensoftId int,
	pPensoftTitle varchar,
	pXmlFileName varchar,
	pTitleAbrev varchar,
	pIssnPrint varchar,
	pIssnOnline varchar,
	pPublisher varchar
) TO iusrpmt;
