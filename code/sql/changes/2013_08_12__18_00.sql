CREATE TABLE pjs.article_cached_item_types(
	id serial PRIMARY KEY,
	name varchar
);

GRANT ALL ON pjs.article_cached_item_types TO iusrpmt;

INSERT INTO pjs.article_cached_item_types(name) VALUES 
	('Article xml'), ('Article html'), ('Reference html'), ('Figure html'), ('Table html'), ('Sup file html'), ('Taxon html'), ('Author html'),
	('Article figures list html'), ('Article tables list html'), ('Article references list html'), ('Article taxon list html'), 
	('Article authors list html'), ('Article sup files list html'), ('Article content html'), ('Article localities list html'),
	('Article author html'), ('Citation list html');

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
	citation_list_cache_id bigint REFERENCES pjs.article_cached_items(id),
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

CREATE TABLE pjs.article_localities(
	id bigserial PRIMARY KEY,
	article_id int REFERENCES pjs.articles(id),
	latitude float,
	longitude float
);
GRANT ALL ON pjs.article_localities TO iusrpmt;

CREATE TABLE pjs.article_instance_localities(
	instance_id bigint,
	locality_id bigint REFERENCES pjs.article_localities(id)	
);
GRANT ALL ON pjs.article_instance_localities TO iusrpmt;

ALTER TABLE pjs.document_users ADD COLUMN zoobank_id varchar;

CREATE TABLE pjs.taxon_sites(
	id serial PRIMARY KEY,
	name varchar,
	picsrc varchar,
	picsrc_no_results varchar,
	display_title varchar,
	is_ubio_site boolean DEFAULT false,
	ubio_site_name varchar,
	taxon_link varchar,
	taxon_link_no_results varchar,
	show_if_not_found boolean DEFAULT false,
	use_post_action boolean DEFAULT false,
	use_post_action_no_results boolean DEFAULT false,
	fields_to_post varchar,
	fields_to_post_no_results varchar
);
GRANT ALL ON pjs.taxon_sites TO iusrpmt;

CREATE TABLE pjs.taxon_sites_match_expressions(
	id serial PRIMARY KEY,
	site_id int REFERENCES pjs.taxon_sites(id) NOT NULL,
	expression varchar,
	ord int NOT NULL DEFAULT 1
);
GRANT ALL ON pjs.taxon_sites_match_expressions TO iusrpmt;

CREATE TABLE pjs.taxon_sites_results(
	id bigserial PRIMARY KEY,
	taxon_id bigint REFERENCES pjs.taxons(id) NOT NULL,
	site_id int REFERENCES pjs.taxon_sites(id) NOT NULL,
	has_results boolean DEFAULT false,
	specific_link_url varchar,
	lastmoddate timestamp DEFAULT now()
);
GRANT ALL ON pjs.taxon_sites_results TO iusrpmt;

CREATE TABLE pjs.taxon_bhl_data(
	id bigserial PRIMARY KEY,
	taxon_id bigint REFERENCES pjs.taxons(id) NOT NULL,
	result_taken_successfully boolean DEFAULT false,
	titles_count int,
	lastmoddate timestamp DEFAULT now()
);
GRANT ALL ON pjs.taxon_bhl_data TO iusrpmt;

CREATE TABLE pjs.taxon_bhl_titles(
	id bigserial PRIMARY KEY,
	taxon_bhl_data_id bigint REFERENCES pjs.taxon_bhl_data(id) NOT NULL,
	title varchar,
	title_url varchar,
	lastmoddate timestamp DEFAULT now() 
);

GRANT ALL ON pjs.taxon_bhl_titles TO iusrpmt;

CREATE TABLE pjs.taxon_bhl_title_items(
	id bigserial PRIMARY KEY,	
	title_id bigint REFERENCES pjs.taxon_bhl_titles(id) NOT NULL,
	volume varchar,
	pages_count int
);
GRANT ALL ON pjs.taxon_bhl_title_items TO iusrpmt;

CREATE TABLE pjs.taxon_bhl_title_item_pages(
	id bigserial PRIMARY KEY,	
	item_id bigint REFERENCES pjs.taxon_bhl_title_items(id) NOT NULL,
	number int,
	url varchar,
	thumbnail_url varchar,
	fullsize_image_url varchar
	
);
GRANT ALL ON pjs.taxon_bhl_title_item_pages TO iusrpmt;

CREATE TABLE pjs.taxon_lias_data(
	id bigserial PRIMARY KEY,
	taxon_id bigint REFERENCES pjs.taxons(id) NOT NULL,
	results int,
	lastmoddate timestamp DEFAULT now()
);

GRANT ALL ON pjs.taxon_lias_data TO iusrpmt;

CREATE TABLE pjs.taxon_lias_data_details(
	id bigserial PRIMARY KEY,
	data_id bigint REFERENCES pjs.taxon_lias_data(id) NOT NULL,
	detail_id varchar,
	detail_name varchar
);

GRANT ALL ON pjs.taxon_lias_data_details TO iusrpmt;

CREATE TABLE pjs.taxon_ncbi_data(
	id bigserial PRIMARY KEY,
	taxon_id bigint REFERENCES pjs.taxons(id) NOT NULL,
	ncbi_id varchar,
	rank varchar,
	division varchar,
	lastmoddate timestamp DEFAULT now()
);

GRANT ALL ON pjs.taxon_ncbi_data TO iusrpmt;

CREATE TABLE pjs.taxon_ncbi_lineage(
	id bigserial PRIMARY KEY,
	ncbi_data_id bigint REFERENCES pjs.taxon_ncbi_data(id) NOT NULL,
	scientific_name varchar,
	tax_id varchar
);

GRANT ALL ON pjs.taxon_ncbi_lineage TO iusrpmt;

CREATE TABLE pjs.taxon_ncbi_related_links(
	id bigserial PRIMARY KEY,
	ncbi_data_id bigint REFERENCES pjs.taxon_ncbi_data(id) NOT NULL,
	db_name varchar,
	title varchar,
	item_id varchar,
	url varchar
);

GRANT ALL ON pjs.taxon_ncbi_related_links TO iusrpmt;

CREATE TABLE pjs.taxon_ncbi_entrez_databases(
	id serial PRIMARY KEY,
	display_name varchar,
	entrez_name varchar
);

GRANT ALL ON pjs.taxon_ncbi_entrez_databases TO iusrpmt;

INSERT INTO pjs.taxon_ncbi_entrez_databases(display_name, entrez_name)
	VALUES ('Nucleotide', 'nucleotide'), ('Nuccore', 'nuccore'), ('PubMed Central', 'pmc'), ('', 'taxonomy'), ('', 'popset'), ('', 'protein');

GRANT ALL ON pjs.taxon_ncbi_entrez_databases TO iusrpmt;


CREATE TABLE pjs.taxon_ncbi_entrez_records(
	id bigserial PRIMARY KEY,
	ncbi_data_id bigint REFERENCES pjs.taxon_ncbi_data(id) NOT NULL,
	db_id int REFERENCES pjs.taxon_ncbi_entrez_databases(id) NOT NULL,
	records int
);

GRANT ALL ON pjs.taxon_ncbi_entrez_records TO iusrpmt;

CREATE TABLE pjs.taxon_gbif_data(
	id bigserial PRIMARY KEY,
	taxon_id bigint REFERENCES pjs.taxons(id) NOT NULL,
	gbif_taxon_id varchar,
	map_iframe_src varchar,
	lastmoddate timestamp DEFAULT now()
);
GRANT ALL ON pjs.taxon_gbif_data TO iusrpmt;

CREATE TABLE pjs.taxon_wikimedia_categories(
	id bigserial PRIMARY KEY,
	taxon_id bigint REFERENCES pjs.taxons(id) NOT NULL,
	category_name varchar,
	lastmoddate timestamp DEFAULT now()
);
GRANT ALL ON pjs.taxon_wikimedia_categories TO iusrpmt;

CREATE TABLE pjs.taxon_wikimedia_category_images(
	id bigserial PRIMARY KEY,
	category_id bigint REFERENCES pjs.taxon_wikimedia_categories(id) NOT NULL,
	image_name varchar,
	url varchar,
	lastmoddate timestamp DEFAULT now()
);
GRANT ALL ON pjs.taxon_wikimedia_category_images TO iusrpmt;

INSERT INTO pjs.taxon_sites(name, picsrc, picsrc_no_results, display_title,
			is_ubio_site, ubio_site_name, taxon_link, taxon_link_no_results, show_if_not_found,
			use_post_action, use_post_action_no_results, fields_to_post, fields_to_post_no_results)
		VALUES 	
			('gbif', '/img/ext_details/gbif_logo.jpg', '/img/ext_details/gbif_logo_BW.jpg', 'Global Biodiversity Information Facility',
			0::boolean, '', 'http://data.gbif.org/search/{encoded_taxon_name}', 'http://data.gbif.org/search/{encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('eol', '/img/ext_details/eol_logo.jpg', '/img/ext_details/eol_logo_BW.jpg', 'Encyclopedia of Life',
			0::boolean, '', 'http://www.eol.org/search?q={encoded_taxon_name}', 'http://www.eol.org/search?q={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('col', '/img/ext_details/col_logo.jpg', '/img/ext_details/col_logo_BW.jpg', 'Catalogue of Life',
			0::boolean, 'Catalog of Life', 'http://www.catalogueoflife.org/search/all/key/{encoded_taxon_name}', 'http://www.catalogueoflife.org/search/all/key/{encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('itis', '/img/ext_details/itis_logo.jpg', '/img/ext_details/itis_logo_BW.jpg', 'Integrated Taxonomic Information System',
			1::boolean, 'ITIS', 'http://www.itis.gov/servlet/SingleRpt/SingleRpt', 'http://www.itis.gov/servlet/SingleRpt/SingleRpt', 1::boolean,
			1::boolean, 1::boolean, 'search_topic=all&search_kingdom=every&search_span=containing&categories=All&source=html&search_credRating=All&search_value={taxon_name}', 'search_topic=all&search_kingdom=every&search_span=containing&categories=All&source=html&search_credRating=All&search_value={taxon_name}'   
			) ,			
	
			('species2000', '/img/ext_details/species2000_logo.jpg', '/img/ext_details/species2000_logo_BW.jpg', 'Species 2000',
			0::boolean, '', 'http://www.sp2000.org/index.php?option=com_search&Itemid=99999999&submit=Search&searchphrase=any&ordering=newest&searchword={encoded_taxon_name}', 'http://www.sp2000.org/index.php?option=com_search&Itemid=99999999&submit=Search&searchphrase=any&ordering=newest&searchword={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('fa', '/img/ext_details/fa_logo.jpg', '/img/ext_details/fa_logo_BW.jpg', 'Fauna Europaea',
			0::boolean, '', 'http://www.faunaeur.org', 'http://www.faunaeur.org', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('worms', '/img/ext_details/worms_logo.jpg', '/img/ext_details/worms_logo_BW.jpg', 'World Register of Marine Species',
			0::boolean, '', 'http://www.marinespecies.org/aphia.php?p=taxlist', 'http://www.marinespecies.org/aphia.php?p=taxlist', 1::boolean,
			1::boolean, 1::boolean, 'tComp=contains&searchpar=0&action=search&rSkips=0&marine=1&tName={taxon_name}', 'tComp=contains&searchpar=0&action=search&rSkips=0&marine=1&tName={taxon_name}'   
			) ,			
	
			('wikipedia', '/img/ext_details/wiki_logo.jpg', '/img/ext_details/wiki_logo_BW.jpg', 'Wikipedia',
			0::boolean, '', 'http://en.wikipedia.org/w/index.php?search={encoded_taxon_name}', 'http://en.wikipedia.org/w/index.php?search={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('wikispecies', '/img/ext_details/wikispecies_logo.gif', '/img/ext_details/wikispecies_logo_BW.gif', 'Wikispecies',
			0::boolean, '', 'http://species.wikimedia.org/wiki/{encoded_taxon_name}', 'http://species.wikimedia.org/wiki/{encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('iucn', '/img/ext_details/iucn_logo.jpg', '/img/ext_details/iucn_logo_BW.jpg', 'IUCN',
			0::boolean, '', 'http://iucn.org/search.cfm?uSearchTerm={encoded_taxon_name}', 'http://iucn.org/search.cfm?uSearchTerm={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('fungorum', '/img/ext_details/fungorum_logo.jpg', '/img/ext_details/fungorum_logo_BW.jpg', 'Index Fungorum',
			1::boolean, 'Index Fungorum', 'http://www.indexfungorum.org/Names/Names.asp?SearchTerm={encoded_taxon_name}', 'http://www.indexfungorum.org/Names/Names.asp?SearchTerm={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('ipni', '/img/ext_details/ipni_logo.jpg', '/img/ext_details/ipni_logo_BW.jpg', 'International Plant Name Index',
			0::boolean, '', 'http://www.ipni.org/ipni/simplePlantNameSearch.do?find_wholeName={encoded_taxon_name}', 'http://www.ipni.org/ipni/simplePlantNameSearch.do?find_wholeName={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('algaebase', '/img/ext_details/algaebase_logo.jpg', '/img/ext_details/algaebase_logo_BW.jpg', 'Algaebase',
			1::boolean, 'AlgaeBase', 'http://www.algaebase.org/search/species/', 'http://www.algaebase.org/search/species/?currentMethod=species&fromSearch=yes&sk=0&displayCount=20&sortBy=Genus&sortBy2=Species&-Search=Search&name={taxon_name}', 0::boolean,
			1::boolean, 1::boolean, 'currentMethod=species&fromSearch=yes&sk=0&displayCount=20&sortBy=Genus&sortBy2=Species&-Search=Search&name={taxon_name}', 'currentMethod=species&fromSearch=yes&sk=0&displayCount=20&sortBy=Genus&sortBy2=Species&-Search=Search&name={taxon_name}'   
			) ,			
	
			('tropicos', '/img/ext_details/tropicos_logo.jpg', '/img/ext_details/tropicos_logo_BW.jpg', 'Tropicos',
			0::boolean, 'Tropicos', 'http://www.tropicos.org/NameSearch.aspx?name={encoded_taxon_name}', 'http://www.tropicos.org/NameSearch.aspx?name={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('usda', '/img/ext_details/usda_logo.jpg', '/img/ext_details/usda_logo_BW.jpg', 'PLANTS Database',
			1::boolean, 'USDA Plants', 'http://www.plants.usda.gov/java/nameSearch?mode=sciname&submit.x=10&submit.y=4&keywordquery={encoded_taxon_name}', 'http://www.plants.usda.gov/java/nameSearch?mode=sciname&submit.x=10&submit.y=4&keywordquery={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('gymnosperm', '/img/ext_details/gymnosperm_logo.jpg', '/img/ext_details/gymnosperm_logo_BW.jpg', 'The Gymnosperm Database',
			0::boolean, '', 'http://www.google.com/custom?ie=UTF-8&oe=UTF-8&cof=S%3Ahttp%3A%2F%2Fwww.conifers.org%2F%3BAH%3Acenter%3BL%3Ahttp%3A%2F%2Fwww.conifers.org%2Fzz%2Fgymn2.gif%3B&domains=conifers.org&sitesearch=conifers.org&&sitesearch=http%3A%2F%2Fwww.conifers.org&sa=Search+this+site&q={encoded_taxon_name}', 'http://www.google.com/custom?ie=UTF-8&oe=UTF-8&cof=S%3Ahttp%3A%2F%2Fwww.conifers.org%2F%3BAH%3Acenter%3BL%3Ahttp%3A%2F%2Fwww.conifers.org%2Fzz%2Fgymn2.gif%3B&domains=conifers.org&sitesearch=conifers.org&&sitesearch=http%3A%2F%2Fwww.conifers.org&sa=Search+this+site&q={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('zoobank', '/img/ext_details/zoobank_logo.jpg', '/img/ext_details/zoobank_logo_BW.jpg', 'ZooBank',
			0::boolean, '', 'http://www.zoobank.org/Search.aspx?search={encoded_taxon_name}', 'http://www.zoobank.org/Search.aspx?search={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('tol', '/img/ext_details/tol_logo.jpg', '/img/ext_details/tol_logo_BW.jpg', 'Tree of Life',
			1::boolean, 'Tree Of Life (TOL)', 'http://tolweb.org/tree/home.pages/searchresults.html?cx=009557456284541951685%3A50nf_5tpvuq&cof=FORID%3A9&ie=UTF-8&sa=Search&q={encoded_taxon_name}', 'http://tolweb.org/tree/home.pages/searchresults.html?cx=009557456284541951685%3A50nf_5tpvuq&cof=FORID%3A9&ie=UTF-8&sa=Search&q={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('treebase', '/img/ext_details/treebase_logo.jpg', '/img/ext_details/treebase_logo_BW.jpg', 'TreeBase',
			1::boolean, 'TreeBase', 'http://www.treebase.org/treebase-web/search/studySearch.html', 'http://www.treebase.org/treebase-web/search/studySearch.html', 0::boolean,
			1::boolean, 1::boolean, 'formName=searchKeyword&searchButton=textKeyword&query=&searchTerm={taxon_name}', 'formName=searchKeyword&searchButton=textKeyword&query=&searchTerm={taxon_name}'   
			) ,			
	
			('landcare', '/img/ext_details/landcare_logo.jpg', '/img/ext_details/landcare_logo_BW.jpg', 'Landcare Research',
			1::boolean, 'Landcare LSIDs', '', 'http://www.landcareresearch.co.nz/search/search.asp?zoom_cat=-1&Submit=GO&zoom_query={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('chilobase', '/img/ext_details/chilobase_logo.jpg', '/img/ext_details/chilobase_logo_BW.jpg', 'Chilobase',
			0::boolean, '', 'http://chilobase.bio.unipd.it/', 'http://chilobase.bio.unipd.it/', 0::boolean,
			1::boolean, 1::boolean, 'TYPE=beginning+with&WORDS={taxon_name}', 'TYPE=beginning+with&WORDS={taxon_name}'   
			) ,			
	
			('hymenopterans', '/img/ext_details/hymenoptera.gif', '/img/ext_details/hymenoptera_BW.gif', 'Hymenoptera Name Server',
			0::boolean, '', 'http://osuc.biosci.ohio-state.edu/hymDB/nomenclator.name_entry?Submit=Submit+Query&text_entry={encoded_taxon_name}', 'http://osuc.biosci.ohio-state.edu/hymDB/nomenclator.name_entry?Submit=Submit+Query&text_entry={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('fishbase', '/img/ext_details/fishbase_logo.jpg', '/img/ext_details/fishbase_logo_BW.jpg', 'FishBase',
			0::boolean, '', 'http://www.fishbase.org/NomenClature/ScientificNameSearchList.php', 'http://www.fishbase.org/NomenClature/ScientificNameSearchList.php', 0::boolean,
			1::boolean, 1::boolean, 'Language=English&crit1_fieldname=SYNONYMS.SynGenus&crit1_fieldtype=CHAR&crit2_fieldname=SYNONYMS.SynSpecies&crit2_fieldtype=CHAR&crit1_operator=EQUAL&crit1_value=&crit2_operator=EQUAL&crit2_value=&gs={taxon_name}&group=summary', 'Language=English&crit1_fieldname=SYNONYMS.SynGenus&crit1_fieldtype=CHAR&crit2_fieldname=SYNONYMS.SynSpecies&crit2_fieldtype=CHAR&crit1_operator=EQUAL&crit1_value=&crit2_operator=EQUAL&crit2_value=&gs={taxon_name}&group=summary'   
			) ,			
	
			('ncbi', '/img/ext_details/ncbi_logo.jpg', '/img/ext_details/ncbi_logo_BW.jpg', 'National Center for Biotechnology Information',
			1::boolean, 'NCBI', 'http://www.ncbi.nlm.nih.gov/gquery/?term={encoded_taxon_name}', 'http://www.ncbi.nlm.nih.gov/gquery/?term={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('bold', '/img/ext_details/bold_logo.jpg', '/img/ext_details/bold_logo_BW.jpg', 'Barcode of Life',
			0::boolean, '', 'http://boldsystems.org/views/taxbrowser.php?taxon={encoded_taxon_name}', 'http://boldsystems.org/views/taxbrowser.php?taxon={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('morphbank', '/img/ext_details/morphbank_logo.jpg', '/img/ext_details/morphbank_logo_BW.jpg', 'Morphbank',
			0::boolean, '', 'http://www.morphbank.net/MyManager/?keywords={encoded_taxon_name}', 'http://www.morphbank.net/MyManager/?keywords={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('wikimedia', '/img/ext_details/wikimedia_logo.jpg', '/img/ext_details/wikimedia_logo_BW.jpg', 'Wikimedia',
			0::boolean, '', 'http://commons.wikimedia.org/wiki/{encoded_taxon_name}', 'http://commons.wikimedia.org/wiki/{encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('yahoo_images', '/img/ext_details/yahoo_logo.jpg', '/img/ext_details/yahoo_logo_BW.jpg', 'Yahoo',
			0::boolean, '', 'http://images.search.yahoo.com/search/images;_ylt=A0WTb_moq.5LsmgAdzuLuLkF?ei=utf-8&iscqry=&fr=sfp&p={encoded_taxon_name}', 'http://images.search.yahoo.com/search/images;_ylt=A0WTb_moq.5LsmgAdzuLuLkF?ei=utf-8&iscqry=&fr=sfp&p={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('google_images', '/img/ext_details/google_logo.jpg', '/img/ext_details/google_logo_BW.jpg', 'Google',
			0::boolean, '', 'http://www.google.com/images?hl=en&source=imghp&gbv=2&aq=f&aqi=g2&aql=&oq=&gs_rfai=&q={encoded_taxon_name}', 'http://www.google.com/images?hl=en&source=imghp&gbv=2&aq=f&aqi=g2&aql=&oq=&gs_rfai=&q={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('google_scholar', '/img/ext_details/google_logo.jpg', '/img/ext_details/google_logo_BW.jpg', 'GoogleScholar',
			0::boolean, '', 'http://scholar.google.com/scholar?q={encoded_taxon_name}', 'http://scholar.google.com/scholar?q={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('pubmed', '/img/ext_details/pubmed_logo.jpg', '/img/ext_details/pubmed_logo_BW.jpg', 'PubMed',
			0::boolean, '', 'http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Search&dopt=DocSum&db=pubmed&term={encoded_pubmed_taxon_name}', 'http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Search&dopt=DocSum&db=pubmed&term={encoded_pubmed_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('biodev', '/img/ext_details/biodev_logo.jpg', '/img/ext_details/biodev_logo_BW.jpg', 'Biodiversity Heritage Library',
			0::boolean, '', 'http://www.biodiversitylibrary.org/Search.aspx?searchCat=&searchTerm={encoded_taxon_name}', 'http://www.biodiversitylibrary.org/Search.aspx?searchCat=&searchTerm={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('biolib', '/img/ext_details/biolib_logo.jpg', '/img/ext_details/biolib_logo_BW.jpg', 'BioLib',
			0::boolean, 'BioLib', 'http://www.biolib.cz/en/formsearch/?action=execute&string={encoded_taxon_name}&searchtype=2&searchrecords=1&searchsynonyms=1&searchvnames=1&selecttaxonid=null&taxonid=', '', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('ubio', '/img/ext_details/ubio_logo.jpg', '/img/ext_details/ubio_logo_BW.jpg', 'uBio',
			0::boolean, '', 'http://www.ubio.org/browser/search.php?search_all={encoded_taxon_name}', 'http://www.ubio.org/browser/search.php?search_all={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('plazi', '/img/ext_details/plazi_logo.jpg', '/img/ext_details/plazi_logo_BW.jpg', 'Plazi',
			0::boolean, '', 'http://plazi.org:8080/GgSRS/search?taxonomicName.isNomenclature=isNomenclature&taxonomicName.exactMatch=exactMatch&taxonomicName.taxonomicName={encoded_taxon_name}', 'http://plazi.org:8080/GgSRS/search?taxonomicName.isNomenclature=isNomenclature&taxonomicName.exactMatch=exactMatch&taxonomicName.taxonomicName={encoded_taxon_name}', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('daisie', '/img/ext_details/daisie_logo.jpg', '/img/ext_details/daisie_logo_BW.jpg', 'DAISIE',
			0::boolean, '', 'http://www.europe-aliens.org/speciesSearch.do?speciesPhrase={encoded_taxon_name}', 'http://www.europe-aliens.org/speciesSearch.do?speciesPhrase={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('invasive', '/img/ext_details/invasive_logo.png', '/img/ext_details/invasive_logo_BW.png', 'Invasive.org',
			0::boolean, '', 'http://www.invasive.org/search/results.cfm?cx=004971884014326696348%3Alwck86z8tsg&ie=UTF-8&cof=FORID%3A10&ie=UTF-8&q={encoded_taxon_name}&sa=GO&siteurl=www.invasive.org%252Fspecies.cfm', 'http://www.invasive.org/search/results.cfm?cx=004971884014326696348%3Alwck86z8tsg&ie=UTF-8&cof=FORID%3A10&ie=UTF-8&q={encoded_taxon_name}&sa=GO&siteurl=www.invasive.org%252Fspecies.cfm', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('lias', '/img/ext_details/lias_logo.jpg', '/img/ext_details/lias_logo_BW.jpg', 'Lias',
			0::boolean, '', '{lias_iframe_url}{encoded_taxon_name}', '{lias_iframe_url}{encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			) ,			
	
			('diptera', '/img/ext_details/diptera_logo.jpg', '/img/ext_details/diptera_logo_BW.jpg', 'Diptera',
			0::boolean, '', 'http://130.225.211.25/diptera/names/FMPro?-db=names.fp5&-format=nomenclatorresult.html&-lay=www%20detail&-sortfield=unsorted&-op=cn&Name={encoded_taxon_name}&-max=10&-find=&-lop=and', 'http://130.225.211.25/diptera/names/FMPro?-db=names.fp5&-format=nomenclatorresult.html&-lay=www%20detail&-sortfield=unsorted&-op=cn&Name={encoded_taxon_name}&-max=10&-find=&-lop=and', 0::boolean,
			0::boolean, 0::boolean, '', ''   
			),
			
			('ion', '/img/ext_details/ion_logo.jpg', '/img/ext_details/ion_logo_BW.jpg', 'ION',
			0::boolean, '', 'http://www.organismnames.com/query.htm?q={encoded_taxon_name}', 'http://www.organismnames.com/query.htm?q={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			),
			
			('pmc', '/img/ext_details/pmc_logo.jpg', '/img/ext_details/pmc_logo_BW.jpg', 'PMC',
			0::boolean, '', 'http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Search&dopt=DocSum&db=PMC&term={encoded_taxon_name}', 'http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Search&dopt=DocSum&db=PMC&term={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			),
			
			('crossref', '/img/ext_details/crossref_logo.jpg', '/img/ext_details/crossref_logo_BW.jpg', 'CrossRef',
			0::boolean, '', 'http://search.labs.crossref.org/?q={encoded_taxon_name}', 'http://search.labs.crossref.org/?q={encoded_taxon_name}', 1::boolean,
			0::boolean, 0::boolean, '', ''   
			)
			;
/*
			pjs.taxon_sites(name, picsrc, picsrc_no_results, display_title,
			is_ubio_site, ubio_site_name, taxon_link, taxon_link_no_results, show_if_not_found,
			use_post_action, use_post_action_no_results, fields_to_post, fields_to_post_no_results)
*/
			
UPDATE pjs.taxon_sites SET 
	show_if_not_found = true;

INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '\<span\s+class\="moreMatches"\>No\s+scientific\s+names\s+matching\s+\<span\s+class\="subject"\>"{taxon_name}"\<\/span\>\<\/span\>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'gbif';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '\<span\s+class\="moreMatches"\>No\s+common\s+names\s+matching\s+\<span\s+class\="subject"\>"{taxon_name}"\<\/span\>\<\/span\>', 2
			FROM pjs.taxon_sites 
			WHERE name = 'gbif';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '\<span\s+class\="moreMatches"\>No\s+countries\s+with\s+names\s+matching\s+\<span\s+class\="subject"\>"{taxon_name}"\<\/span\>\<\/span\>', 3
			FROM pjs.taxon_sites 
			WHERE name = 'gbif';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '\<span\s+class\="moreMatches"\>No\s+datasets\s+with\s+names\s+matching\s+\<span\s+class\="subject"\>"{taxon_name}"\<\/span\>\<\/span\>', 4
			FROM pjs.taxon_sites 
			WHERE name = 'gbif';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<h3>No\s+search\s+results\s+were\s+found<\/h3>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'eol';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<div\s+class="results_counter"\s+align="right">\s+Records\s+found:\s+0<\/div>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'col';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'Total\s*0\s*results\s*found\.\s*', 1
			FROM pjs.taxon_sites 
			WHERE name = 'species2000';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'No\s+taxa\s+found\s+that\s+satisfy\s+the\s+criteria\s+specified\s+in\s+the\s+previous screen\.', 1
			FROM pjs.taxon_sites 
			WHERE name = 'worms';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'There\s+were\s+no\s+results\s+matching\s+the\s+query\.', 1
			FROM pjs.taxon_sites 
			WHERE name = 'wikipedia';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'There\s+is\s+currently\s+no\s+text\s+in\s+this\s+page\.', 1
			FROM pjs.taxon_sites 
			WHERE name = 'wikispecies';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<div\s*class="results"\s*>\s*No\s*results found!', 1
			FROM pjs.taxon_sites 
			WHERE name = 'iucn';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '((<b>No\s+Plant\s+Names\s+were\s+found\s+in\s+IPNI\s+matching\s+these\s+search\s+terms\.<\/b>)|(Full\s+name\s+is\s+invalid\.))', 1
			FROM pjs.taxon_sites 
			WHERE name = 'ipni';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<br>Your\s+search\s+-\s+<b>{taxon_name}<\/b>\s+-\s+did\s+not\s+match\s+any\s+documents\.\s+\s+<br>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'gymnosperm';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<span\s+id="ctl00_ContentPlaceHolder_ActResults"><h3>No\s+Matching\s+Nomenclatural\s+Acts\s+Found\.<\/h3><\/span>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'zoobank';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<span\s+id="ctl00_ContentPlaceHolder_PubResults"><h3>No\s+Matching\s+Publications\s+Found\.<\/h3><\/span>', 2
			FROM pjs.taxon_sites 
			WHERE name = 'zoobank';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<span\s+id="ctl00_ContentPlaceHolder_AuthResults"><h3>No\s+Matching\s+Authors\s+Found\.<\/h3><\/span>', 3
			FROM pjs.taxon_sites 
			WHERE name = 'zoobank';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<TR><TD\s+align=center\s+valign=top>\s+<TABLE\s+border="0"\s+cellspacing="0"\s+cellpadding="2"\s+width="100%">\s+<TR><TD\s+valign=top\s+nowrap>\s+<BR><BR><CENTER>No\s+results!<\/CENTER><BR><BR>\s+<\/TD><\/TR>\s+<\/TABLE>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'chilobase';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '(The\s+name\s+entered,\s+<strong>{taxon_name}<\/strong>,\s+was\s+not\s+found\s+in\s+the\s+database\.\s+Please\s+check\s+the\s+spelling.\s+We\s+would\s+appreciate\s+hearing\s+of\s+names\s+that\s+are\s+not\s+recorded\.)|(\<TABLE\s+BORDER\=1\s+BGCOLOR\="#AACCFF"\s+CELLPADDING\=5\>\s+\<TR\>\<TD\s+COLSPAN\=2\s+BGCOLOR\="#AACCFF"\s+ALIGN\=center\s+BORDER\=1\>\s+\<BR\>\s+\<TABLE\s+CELLPADDING\=5\s+border\=0\>\<TR\>\<TD\>\s+\<IMG\s+SRC\="http\:\/\/iris\.biosci\.ohio-state\.edu\/gifs\/wasp2\.gif"\>\<\/td\>\s+\<TD\>\<center\>\<H2\>\<FONT\s+color\="#006600"\>\<STRONG\>Hymenoptera\s+Name\s+Server\<\/STRONG\>\<\/FONT\>\<BR\>\s+\<FONT\s+SIZE\=-1\s+color\="#006600"\>\<em\>&nbsp;&nbsp;&nbsp;version\s+1\.5\s+&nbsp;&nbsp;&nbsp;19\.xii\.2007\<\/EM\>\<\/FONT\>\<\/center\>\<\/H2\>\<\/TD\>\<\/TR\>\<\/TABLE\>\<\/TD\>\<\/TR\>\s+\<\/TABLE\>)', 1
			FROM pjs.taxon_sites 
			WHERE name = 'hymenopterans';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'The\s+name\s+you\s+requested\s+could\s+not\s+be\s+found\.', 1
			FROM pjs.taxon_sites 
			WHERE name = 'bold';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '(<div\s+class="noarticletext">\s+<p>This\s+page\s+does\s+not\s+currently\s+exist\.\s+You\s+can\s+)|(\<p\>\<span\s+class\="plainlinks\s+nourlexpansion"\>This\s+page\s+does\s+not\s+currently\s+exist\.\s+You\s+can)', 1
			FROM pjs.taxon_sites 
			WHERE name = 'wikimedia';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<div\s+class=yschalrtz>We\s+did\s+not\s+find\s+results\s+for\s+"<strong>{taxon_name}<\/strong>"', 1
			FROM pjs.taxon_sites 
			WHERE name = 'yahoo_images';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'Your\s+search\s+-\s+<b>{taxon_name}<\/b>\s+-\s+did\s+not\s+match\s+any\s+documents\.', 1
			FROM pjs.taxon_sites 
			WHERE name = 'google_images';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'Your\s+search\s+-\s+<b>{taxon_name}<\/b>\s+-\s+did\s+not\s+match\s+any\s+articles\.', 1
			FROM pjs.taxon_sites 
			WHERE name = 'google_scholar';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<span\s+class="pageheader">Search\s+Results\s+for\s+"<span\s+id="ctl00_mainContentPlaceHolder_searchResultsLabel">{taxon_name}<\/span>"<\/span>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'biodev';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<span\s+id="ctl00_mainContentPlaceHolder_spanTitleSummary">\s+<a\s+href="#Titles">Titles<\/a>\s+found\s+:\s+0<br\s+\/><\/span>', 2
			FROM pjs.taxon_sites 
			WHERE name = 'biodev';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<span\s+id="ctl00_mainContentPlaceHolder_spanAuthorSummary">\s+<a\s+href="#Authors">Authors<\/a>\s+found\s+:\s+0<br\s+\/><\/span>', 3
			FROM pjs.taxon_sites 
			WHERE name = 'biodev';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<span\s+id="ctl00_mainContentPlaceHolder_spanNameSummary">\s+<a\s+href="#Names">Names<\/a>\s+found\s+:\s+0<br\s+\/><\/span>', 4
			FROM pjs.taxon_sites 
			WHERE name = 'biodev';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<span\s+id="ctl00_mainContentPlaceHolder_spanSubjectSummary">\s+<a\s+href="#Subjects">Subjects<\/a>\s+found\s+:\s+0<br\s+\/><\/span>', 5
			FROM pjs.taxon_sites 
			WHERE name = 'biodev';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'Search\s+Results<\/span><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;No\s+Results\s+for\s+<b>{taxon_name}<\/b><br><br><br>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'ubio';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<P\s+ALIGN=CENTER><B><FONT\s+SIZE="\+1"\s+COLOR="#FFFFFF">No\s+Records\s+Found<\/FONT><\/B>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'diptera';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<table\s+border="1"\s+cellpadding="1"\s+cellspacing="1"\s+align="center"\s+width="800">\s+<tr\s+bgcolor="EAF2F7"\s+class="t_header2">\s+<td\s+width="200"\s+height="25"><div\s+align="center"><strong>&nbsp;\s+Scientific\s+Name<\/strong><\/div><\/td>\s+<td\s+width="200"><div\s+align="center"><strong>&nbsp;\s+Author<\/strong><\/div><\/td>\s+<td\s+width="200"><div\s+align="center"><strong>&nbsp;\s+Valid\s+Name<\/strong><\/div><\/td>\s+<td\s+width="200"><div\s+align="center"><strong>&nbsp;\s+English\s+Name<\/strong><\/div><\/td>\s+<\/tr>\s+<\/table>\s+<table\s+border="0"\s+cellpadding="1"\s+cellspacing="1"\s+align="center"\s+width="800">\s+<tr>\s+<td\s+colspan=4\s+align="center"><span\s+class="t_value2">\s+[\s+<a\s+href="javascript:history.go(-1)">Go\s+Back<\/a>\s+]\s+[\s+<a\s+href="search.php">Go\s+Search<\/a>\s+]\s+[\s+<a\s+href="#GoTop">Go\s+Top\s+<\/a>]<\/span><\/td>\s+<\/tr>\s+<\/table>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'fishbase';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<span\s+class="AlertText">No\s+result\s+were\s+found<\/span>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'tropicos';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<h3>Scientific\s+names<\/h3>\s+<div\s+class="clbarl2"><div\s+class="clbarbodyl2">\s+No\s+records\s+found<\/div><\/div>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'biolib';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '<h3>Vernacular\s+names<\/h3>\s+<div\s+class="clbarl2"><div\s+class="clbarbodyl2">\s+No\s+records\s+found<\/div><\/div>', 2
			FROM pjs.taxon_sites 
			WHERE name = 'biolib';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '\<li\s+class\="info"\>No\s+items\s+found\.\<\/li\>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'pubmed';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '\<td\s+class\="searchErrorMessage"\>\s*No\s+treatment\s+yet\s+on\s+plazi\:\s+But\s+you\s+can\s+help\s+to\s+make\s+it\s+accessible\!\<\/td\>\s+\<\/tr\>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'plazi';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'display\s+Results:\s+no\s+rows!\<br\/\>', 1
			FROM pjs.taxon_sites 
			WHERE name = 'morphbank';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'No\s+Results\s+Found', 1
			FROM pjs.taxon_sites 
			WHERE name = 'daisie';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'Your\s+search\s+\-\s+\<b\>{taxon_name}\<\/b\>\s+\-\s+did\s+not\s+match\s+any\s+documents\.', 1
			FROM pjs.taxon_sites 
			WHERE name = 'invasive';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'Page\s+1\s+of\s+0\s+\(\<b\>0\<\/b\>\s+names\)', 1
			FROM pjs.taxon_sites 
			WHERE name = 'ion';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, '"No\s+items\s+found"', 1
			FROM pjs.taxon_sites 
			WHERE name = 'pmc';
		INSERT INTO pjs.taxon_sites_match_expressions(site_id, expression, ord)
			SELECT id, 'Page\s+1\s+of\s+0\s+results', 1
			FROM pjs.taxon_sites 
			WHERE name = 'crossref';
		
		

CREATE TABLE pjs.taxon_preview_generation_errors(
	id bigserial PRIMARY KEY,
	taxon_id bigint REFERENCES pjs.taxons(id) NOT NULL,
	err_msg varchar,
	processed boolean DEFAULT false,
	createdate timestamp DEFAULT now()
);

GRANT ALL ON pjs.taxon_preview_generation_errors TO iusrpmt;

CREATE TABLE pjs.taxon_eol_data(
	id bigserial PRIMARY KEY,
	taxon_id bigint REFERENCES pjs.taxons(id) NOT NULL,
	eol_taxon_id varchar,
	lastmoddate timestamp DEFAULT now()
);
GRANT ALL ON pjs.taxon_eol_data TO iusrpmt;

CREATE TABLE pjs.taxon_eol_images(
	id bigserial PRIMARY KEY,
	eol_data_id bigint REFERENCES pjs.taxon_eol_data(id) NOT NULL,	
	url varchar
);
GRANT ALL ON pjs.taxon_eol_images TO iusrpmt;



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
	pjs.spSaveArticleLocality
	pjs.spCreateArticle
	pjs.spSaveArticleCitationListPreview
	
	pjs.spSaveTaxonNCBIBaseData
	pjs.spSaveTaxonNCBIRelatedLink
	pjs.spSaveTaxonNCBIEntrezRecords
	pjs.spSaveTaxonNCBILineage
	pjs.spSaveTaxonGBIFBaseData
	pjs.spSaveTaxonBHLBaseData
	pjs.spSaveTaxonBHLTitle
	pjs.spSaveTaxonBHLItem
	pjs.spSaveTaxonBHLPage
	pjs.spClearTaxonWikimediaCategories
	pjs.spSaveTaxonWikimediaCategory
	pjs.spSaveTaxonWikimediaPhoto
	pjs.spSaveTaxonLiasBaseData
	pjs.spSaveTaxonLiasDetail
	pjs.spSaveTaxonUbioSiteResult
	pjs.spSaveTaxonSiteResult
	pjs.spSaveTaxonPreview
	pjs.spClearArticleTaxa
	pjs.spSaveArticleTaxon
	pjs.spNormalizeTaxonName
	pjs.spGetTaxonId
	pjs.spSaveTaxonEOLBaseData
	pjs.spSaveTaxonEOLImage	
	
	The following line should be added to the pwt conf
	require_once('ptp_conf.php');
	also the PTP_URL should be set to the correct url of ptp
*/