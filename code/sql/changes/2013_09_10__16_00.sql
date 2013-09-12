CREATE TABLE pjs.article_metric_types(
	id serial PRIMARY KEY,
	name varchar
);
GRANT ALL ON pjs.article_metric_types TO iusrpmt;

INSERT INTO pjs.article_metric_types(name) VALUES 
	('HTML'), ('PDF'), ('XML'), ('Figure'), ('Table'), ('Suppl. File');

CREATE TABLE pjs.article_metrics(
	id bigserial PRIMARY KEY,
	item_id bigint NOT NULL,
	item_type int REFERENCES pjs.article_metric_types(id) NOT NULL,
	view_cnt int DEFAULT 0,
	view_unique_cnt int DEFAULT 0,
	download_cnt int DEFAULT 0,
	download_unique_cnt int DEFAULT 0
);

GRANT ALL ON pjs.article_metrics TO iusrpmt;

CREATE TABLE pjs.article_metrics_detail_types(
	id serial PRIMARY KEY,
	name varchar
);

GRANT ALL ON pjs.article_metrics_detail_types TO iusrpmt;

INSERT INTO pjs.article_metrics_detail_types(name) VALUES ('View'), ('Download');

CREATE TABLE pjs.article_metrics_details(
	id bigserial PRIMARY KEY,
	metric_id bigint REFERENCES pjs.article_metrics(id) NOT NULL,
	detail_type int REFERENCES pjs.article_metrics_detail_types(id) NOT NULL,
	ip inet,
	createdate timestamp DEFAULT now()
);

GRANT ALL ON pjs.article_metrics_details TO iusrpmt;

ALTER TABLE pjs.article_sup_files ADD COLUMN file_name varchar;
ALTER TABLE pjs.article_figures ADD COLUMN pic_id bigint;

ALTER TABLE pjs.article_figures ADD COLUMN display_label varchar;
ALTER TABLE pjs.article_figures ADD COLUMN plate_instance_id bigint;
ALTER TABLE pjs.article_tables ADD COLUMN display_label varchar;
ALTER TABLE pjs.article_sup_files ADD COLUMN display_label varchar;

/*
	Modified sps
	pjs.spSaveArticleXml
	pjs.spCreateArticleMetric
	pjs.spRegisterArticleMetricDetail
	
	pjs.spSaveArticlePreview
	pjs.spSaveArticleTablePreview
	pjs.spSaveArticleFigurePreview
	pjs.spSaveArticleSupFilePreview	
	pjs.spSaveArticlePlatePreview
	pwt.spGetPlatePartLetter	
*/