CREATE TABLE pjs.article_cached_item_types(
	id serial PRIMARY KEY,
	name varchar
);

GRANT ALL ON pjs.article_cached_item_types TO iusrpmt;

INSERT INTO pjs.article_cached_item_types(name) VALUES 
	('Article xml'), ('Article html'), ('Reference html'), ('Figure html'), ('Table html'), ('Sup file html'), ('Taxon html'), ('Author html'),
	('Article figures list html'), ('Article tables list html'), ('Article references list html'), ('Article taxon list html'), 
	('Article authors list html'), ('Article sup files list html'), ('Article content html'), ('Article localities list html'),
	('Article author html');

CREATE TABLE pjs.article_cached_items(
	id bigserial PRIMARY KEY,
	cached_val varchar,
	item_type int REFERENCES pjs.article_cached_item_types(id),
	article_id int,
	lastmoddate timestamp DEFAULT now()
);

GRANT ALL ON pjs.article_cached_items TO iusrpmt;

CREATE TABLE pjs.articles(
	id int PRIMARY KEY REFERENCES pjs.documents(id),
	figures_list_cache_id bigint REFERENCES pjs.article_cached_items(id),
	tables_list_cache_id bigint REFERENCES pjs.article_cached_items(id),
	sup_files_list_cache_id bigint REFERENCES pjs.article_cached_items(id),
	references_list_cache_id bigint REFERENCES pjs.article_cached_items(id),
	taxon_list_cache_id bigint REFERENCES pjs.article_cached_items(id),
	authors_list_cache_id bigint REFERENCES pjs.article_cached_items(id),
	contents_list_cache_id bigint REFERENCES pjs.article_cached_items(id),
	xml_cache_id bigint REFERENCES pjs.article_cached_items(id),
	preview_cache_id bigint REFERENCES pjs.article_cached_items(id),
	localities_list_cache_id bigint REFERENCES pjs.article_cached_items(id),
	createdate timestamp DEFAULT now(),
	pwt_document_id int
);
GRANT ALL ON pjs.articles TO iusrpmt;

ALTER TABLE pjs.article_cached_items  ADD CONSTRAINT article_cached_items_article_id_fk FOREIGN KEY (article_id) REFERENCES pjs.articles(id);

CREATE TABLE pjs.article_figures(
	id bigserial PRIMARY KEY,
	instance_id bigint,
	article_id int REFERENCES pjs.articles(id),
	is_plate boolean DEFAULT false,
	cache_id bigint REFERENCES pjs.article_cached_items(id)
);
GRANT ALL ON pjs.article_figures TO iusrpmt;

CREATE TABLE pjs.article_tables(
	id bigserial PRIMARY KEY,
	instance_id bigint,
	article_id int REFERENCES pjs.articles(id),
	cache_id bigint REFERENCES pjs.article_cached_items(id)
);
GRANT ALL ON pjs.article_tables TO iusrpmt;

CREATE TABLE pjs.article_sup_files(
	id bigserial PRIMARY KEY,
	instance_id bigint,
	article_id int REFERENCES pjs.articles(id),
	cache_id bigint REFERENCES pjs.article_cached_items(id)
);
GRANT ALL ON pjs.article_sup_files TO iusrpmt;


CREATE TABLE pjs.article_authors(
	id bigserial PRIMARY KEY,
	author_uid int REFERENCES public.usr(id),
	article_id int REFERENCES pjs.articles(id),
	cache_id bigint REFERENCES pjs.article_cached_items(id)
);
GRANT ALL ON pjs.article_authors TO iusrpmt;

CREATE TABLE pjs.taxons(
	id bigserial PRIMARY KEY,
	name varchar,
	cache_id bigint REFERENCES pjs.article_cached_items(id)
);
GRANT ALL ON pjs.taxons TO iusrpmt;

CREATE TABLE pjs.references(
	id bigserial PRIMARY KEY,
	name varchar,
	cache_id bigint REFERENCES pjs.article_cached_items(id)
);
GRANT ALL ON pjs.references TO iusrpmt;

CREATE TABLE pjs.article_taxons(
	article_id int REFERENCES pjs.articles(id),
	taxon_id bigint REFERENCES pjs.taxons(id)
);
GRANT ALL ON pjs.article_taxons TO iusrpmt;

CREATE TABLE pjs.article_references(
	article_id int REFERENCES pjs.articles(id),
	instance_id bigint,
	reference_id bigint REFERENCES pjs.references(id)
);
GRANT ALL ON pjs.article_references TO iusrpmt;

/* Stored procedures
	pjs.spSaveArticleFigurePreview
	pjs.spSaveArticleTablePreview
	pjs.spSaveArticleSupFilePreview
	pjs.spSaveArticlePlatePreview
	pjs.spSaveArticleReferencePreview	
	pjs.spSaveArticleTaxonPreview
	pjs.spSaveArticleFiguresListPreview
	pjs.spSaveArticleTablesListPreview
	pjs.spSaveArticleReferencesListPreview
	pjs.spSaveArticleSupFilesListPreview
	pjs.spSaveArticleLocalitiesListPreview
	pjs.spSaveArticleContentsListPreview
	pjs.spSaveArticleTaxonListPreview
	pjs.spSaveArticleAuthorsListPreview
	pjs.spSaveArticlePreview
	pjs.spSaveArticleXml
	pjs.spSaveArticleAuthorPreview
	pjs.spGetArticleContentsInstances
*/