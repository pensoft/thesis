ALTER TABLE export_common ADD COLUMN is_generated int DEFAULT 0;
ALTER TABLE export_common ADD COLUMN has_results int DEFAULT 0;
ALTER TABLE export_common ADD COLUMN upload_started int DEFAULT 0;
ALTER TABLE export_common ADD COLUMN generating_started int DEFAULT 0;

ALTER TABLE keys_export ADD CONSTRAINT keys_export_createuid_fkey FOREIGN KEY (createuid)   REFERENCES usr (id) MATCH SIMPLE    ON UPDATE NO ACTION ON DELETE NO ACTION;

INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES (58, '/resources/exports/eol_export/', 'Eol Export', 3, 1, 1);
INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES (59, '/resources/exports/wiki_export/', 'Wiki Export', 3, 1, 1);
ALTER SEQUENCE secsites_id_seq RESTART WITH 60;


INSERT INTO secgrpacc(gid, sid, type) VALUES (2, 58, 6);
INSERT INTO secgrpacc(gid, sid, type) VALUES (2, 59, 6);

-- Махаме правата за старите експорти - за да няма грешки
DELETE FROM secgrpacc WHERE sid = 49;
DELETE FROM secgrpacc WHERE sid = 52;
UPDATE secgrpacc SET name = '*' || name WHERE id = 49;
UPDATE secgrpacc SET name = '*' || name WHERE id = 52;

-- Eol export old
ALTER TABLE eol_export RENAME TO eol_export_old;

ALTER TABLE eol_export_old DROP CONSTRAINT eol_export_pkey;
ALTER TABLE eol_export_old ADD CONSTRAINT eol_export_old_pkey PRIMARY KEY(id);
ALTER TABLE eol_export_old DROP CONSTRAINT eol_export_createuid_fkey;
ALTER TABLE eol_export_old ADD CONSTRAINT eol_export_old_createuid_fkey FOREIGN KEY (createuid)   REFERENCES usr (id) MATCH SIMPLE    ON UPDATE NO ACTION ON DELETE NO ACTION;

-- Eol Export

CREATE TABLE eol_export() INHERITS (export_common)
WITH (
  OIDS=TRUE
);
ALTER TABLE eol_export OWNER TO postgres;
GRANT ALL ON TABLE eol_export TO postgres;
GRANT ALL ON TABLE eol_export TO iusrpmt;

ALTER TABLE eol_export ALTER COLUMN type_id SET DEFAULT 1;
ALTER TABLE eol_export ADD CONSTRAINT eol_export_pkey PRIMARY KEY(id);
ALTER TABLE eol_export ADD CONSTRAINT eol_export_createuid_fkey FOREIGN KEY (createuid)   REFERENCES usr (id) MATCH SIMPLE    ON UPDATE NO ACTION ON DELETE NO ACTION;
  
-- Wiki Export old
ALTER TABLE wiki_export RENAME TO wiki_export_old;  

ALTER TABLE wiki_export_old DROP CONSTRAINT wiki_export_pkey;
ALTER TABLE wiki_export_old ADD CONSTRAINT wiki_export_old_pkey PRIMARY KEY(id);
ALTER TABLE wiki_export_old DROP CONSTRAINT wiki_export_createuid_fkey;
ALTER TABLE wiki_export_old ADD CONSTRAINT wiki_export_old_createuid_fkey FOREIGN KEY (createuid)   REFERENCES usr (id) MATCH SIMPLE    ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE wiki_export_old DROP CONSTRAINT wiki_export_wiki_username_id_fkey;
ALTER TABLE wiki_export_old ADD CONSTRAINT wiki_export_old_wiki_username_id_fkey FOREIGN KEY (wiki_username_id) REFERENCES wiki_login (id) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

-- Wiki export
CREATE TABLE wiki_export(
	wiki_username_id integer
) INHERITS (export_common)
WITH (
  OIDS=TRUE
);
ALTER TABLE wiki_export OWNER TO postgres;
GRANT ALL ON TABLE wiki_export TO postgres;
GRANT ALL ON TABLE wiki_export TO iusrpmt;

ALTER TABLE wiki_export ALTER COLUMN type_id SET DEFAULT 2;
ALTER TABLE wiki_export ADD CONSTRAINT wiki_export_pkey PRIMARY KEY(id);
ALTER TABLE wiki_export ADD CONSTRAINT wiki_export_createuid_fkey FOREIGN KEY (createuid)   REFERENCES usr (id) MATCH SIMPLE    ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE wiki_export ADD CONSTRAINT wiki_export_wiki_username_id_fkey FOREIGN KEY (wiki_username_id) REFERENCES wiki_login (id) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

INSERT INTO wiki_export(title, article_id, createdate, lastmod, createuid, "xml", is_uploaded, upload_time, upload_has_errors, upload_msg, wiki_username_id)
	SELECT title, article_id, createdate, lastmod, createuid, "xml", is_uploaded, upload_time, upload_has_errors, upload_msg, wiki_username_id
	FROM wiki_export_old;

-- Export types
	
ALTER TABLE export_types ADD COLUMN xsl_file varchar;
ALTER TABLE export_types ADD COLUMN results_xpath_expr varchar;

UPDATE export_types SET xsl_file = 'eol_export_new.xsl', results_xpath_expr = '/eol:response/eol:taxon' WHERE id = 1;
UPDATE export_types SET xsl_file = 'mediawiki.xsl', results_xpath_expr = '/mw:mediawiki//mw:page' WHERE id = 2;
UPDATE export_types SET xsl_file = 'keys_export.xsl', results_xpath_expr = '/keys/key' WHERE id = 3;

-- Finalized articles
ALTER TABLE articles ADD COLUMN is_finalized int DEFAULT 0;
ALTER TABLE articles ALTER COLUMN is_finalized SET NOT NULL;
UPDATE articles SET journal_id = 1 WHERE journal_id IS NULL;
ALTER TABLE articles ALTER COLUMN journal_id SET NOT NULL;

CREATE TABLE finalized_articles(
	article_id int PRIMARY KEY REFERENCES articles(id),
	article_title varchar NOT NULL,
	article_authors varchar NOT NULL,
	article_journal_issue int NOT NULL,
	article_fpage int NOT NULL,
	article_lpage int NOT NULL,
	article_doi varchar NOT NULL,
	article_pensoft_id int NOT NULL,
	createdate timestamp without time zone DEFAULT now()
);

GRANT ALL ON finalized_articles TO iusrpmt;

CREATE TABLE finalized_articles_exports(
	article_id int REFERENCES finalized_articles(article_id),
	export_type_id int REFERENCES export_types(id),
	export_id int,
	CONSTRAINT finalized_articles_exports_pkey PRIMARY KEY (article_id, export_type_id)
);
GRANT ALL ON finalized_articles_exports TO iusrpmt;

ALTER TABLE journals ADD COLUMN export_types int[];
ALTER TABLE journals ADD COLUMN wiki_username_id int DEFAULT 1 REFERENCES wiki_login(id);

INSERT INTO secsites(id, url, name, cnt, ord, type) VALUES (60, '/finalized_articles/', 'Finalized articles', 2, 10, 1);
ALTER SEQUENCE secsites_id_seq RESTART WITH 61;


INSERT INTO secgrpacc(gid, sid, type) VALUES (2, 60, 6);

UPDATE export_common SET upload_started = 1 WHERE is_uploaded = 1;
UPDATE export_common SET generating_started = 1 WHERE is_generated = 1;

ALTER TABLE export_common ADD COLUMN generate_pid int DEFAULT 0;
ALTER TABLE export_common ADD COLUMN upload_pid int DEFAULT 0;

