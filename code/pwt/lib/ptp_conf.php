<?php
define('PTP_URL', 'http://ptp.pensoft.eu');


define('TAXON_NAME_SEARCH_TYPE', 1);
define('TAXON_MAP_SRV', "http://data.gbif.org/species/nameSearch?maxResults=1&view=xml&returnType=nameIdMap&exactOnly=true&query=");
define('EUTILS_ESEARCH_SRV', "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?");
define('EUTILS_ELINK_SRV', "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/elink.fcgi?");
define('EUTILS_ESUMMARY_SRV', "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?");
define('EUTILS_EFETCH_SRV', "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?");
define('EUTILS_EGQUERY_SRV', "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/egquery.fcgi");
define('EUTILS_PUBMED_DB', 'pubmed');
define('EUTILS_NUCLEOTIDE_DB', 'nucleotide');
define('EUTILS_NUCCORE_DB', 'nuccore');
define('EUTILS_NUCCORE_DISPLAY_NAME', 'Nucleotide');
define('EUTILS_PMC_DB', 'pmc');
define('EUTILS_PMC_DISPLAY_NAME', 'PubMed Central');
define('EUTILS_TAXONOMY_DB', 'taxonomy');
define('EUTILS_POPSET_DB', 'popset');
define('EUTILS_PROTEIN_DB', 'protein');
define('EUTILS_TOOL_NAME', 'pensoft');
define('PUBMED_LINK_PREFIX', 'http://www.ncbi.nlm.nih.gov/pubmed/');
define('NUCLEOTIDE_LINK_PREFIX', 'http://www.ncbi.nlm.nih.gov/nucleotide/');
define('NCBI_SUBTREE_LINK', 'http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Search&dopt=DocSum');
define('NCBI_TAXONOMY_LINEAGE_URL', 'http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Undef&lvl=3&keep=1&srchmode=1&unlock');
define('GBIF_TAXON_LINK', 'http://data.gbif.org/species/');

define('BHL_TAXON_LINK', 'http://www.biodiversitylibrary.org/api2/httpquery.ashx?op=NameGetDetail&apikey=6594870d-639b-4db6-be38-c8e7b431f952&name=');
define('BHL_MORPHBANK_LINK', 'http://services.morphbank.net/mb/request?method=search&objecttype=Image&geolocated=true&firstResult=0&user=&group=&change=&lastDateChanged=&numChangeDays=1&id=&format=svc&keywords=');
define('BHL_TAXON_EXTERNAL_LINK', 'http://biodiversitylibrary.org/name/');

define('CP1251CONVERT', 0);

define('UBIO_FIND_URL', 'http://www.ubio.org/webservices/service.php');
define('UBIO_KEY_CODE', '1d2d95dde12cb0bce651bd0787a18e9239e296c9');
define('TP_NAMESPACE_URL', 'http://www.plazi.org/taxpub');
define('YAHOO_APPID', 'mFFf6BXV34FnE6QZPa5Ld1t13lbWn4qJYBHuuHwH54hVRPUn4YWRg.UpN6mqKvN7');
define('YAHOO_IMAGES_URL', 'http://search.yahooapis.com/ImageSearchService/V1/imageSearch?appid=' . YAHOO_APPID . '&query=');
define('UBIO_TAXONFINDER_URL', 'http://www.ubio.org/webservices/service.php?function=taxonFinder&freeText=');


define('UBIO_LINK_NCBI_TITLE', 'NCBI');
define('UBIO_LINK_ITIS_TITLE', 'ITIS');
define('UBIO_LINK_ITISCANADA_TITLE', 'ITIS Canada');
define('UBIO_LINK_CUSTAR_TITLE', 'Cu*Star');
define('UBIO_LINK_GRIN_TITLE', 'Germplasm Resources Information Network (GRIN)');
define('UBIO_LINK_TOL_TITLE', 'Tree Of Life (TOL)');
define('UBIO_LINK_USDAPLANTS_TITLE', 'USDA Plants');
define('UBIO_LINK_TROPICOS_TITLE', 'Tropicos');
define('UBIO_LINK_BIOLIB_TITLE', 'BioLib');
define('UBIO_LINK_CATALOGOFLIFE_TITLE', 'Catalog of Life');
define('UBIO_LINK_FORESTRYIMAGES_TITLE', 'ForestryImages.org');
define('UBIO_LINK_TREEBASE_TITLE', 'TreeBase');
define('UBIO_LINK_MICROSCOPEIMAGELINKS_TITLE', 'Microscope Image Links');
define('UBIO_LINK_MORPHBANK', 'morphbank images');
define('UBIO_LINK_ANIMALDIVERSITY_TITLE', 'Animal Diversity Web');
define('UBIO_LINK_LANDCARE_TITLE', 'Landcare LSIDs');
define('UBIO_LINK_NEOTROPICAL_TITLE', 'Neotropical Herbarium Specimens');
define('UBIO_LINK_NOMENCLATORZOOLOGICUS_TITLE', 'Nomenclator Zoologicus');
define('UBIO_LINK_INDEXFUNGORUM_TITLE', 'Index Fungorum');
define('UBIO_LINK_ALKAEBASE_TITLE', 'AlgaeBase');


define('GOOGLE_MAPS_QUERY_URL', 'http://maps.google.com/maps?q=');
define('WIKIMEDIA_COMMONS_API_URL', 'http://commons.wikimedia.org/w/api.php');
//~ define('CACHE_TIMEOUT_LENGTH', 60*12);//4 min
define('NO_RESULT_CACHE_DIVISOR', 12);//1 min
define('BHL_MAX_READ_LEN', 200*1024);//200k
//~ define('DISABLE_CACHE', 1);

define('TAXON_EXTERNAL_LINK_BASE_LINK', PTP_URL . '/externalLink.php');
define('TAXON_NAME_BASE_LINK', PTP_URL . '/external_details.php');
define('TAXON_NAME_LINK', TAXON_NAME_BASE_LINK . '?type=1&amp;query=');


define('XML_FORMATTING_SYMBOL', " ");
define('XML_FORMATTING_SYMBOL_PER_LEVEL', 4);


define('AJAX_CLINKS_MENU_LINK', 1);
define('STATIC_CLINKS_MENU_LINK', 2);
define('AJAX_SERVERS_PATH', PTP_URL . '/lib/ajax_servers/');
define('AJAX_MENU_LINK_SRV', AJAX_SERVERS_PATH . 'ajax_menu_link.php');
define('AJAX_TAXON_MAP_SRV', AJAX_SERVERS_PATH . 'ajax_taxonmap.php');
define('AJAX_NCBI_SRV', AJAX_SERVERS_PATH . 'ajax_ncbi.php');
define('AJAX_BHL_SRV', AJAX_SERVERS_PATH . 'ajax_bhl.php');
define('AJAX_WIKIMEDIA_SRV', AJAX_SERVERS_PATH . 'ajax_wikimedia.php');
define('AJAX_MORPHBANK_SRV', AJAX_SERVERS_PATH . 'ajax_morphbank.php');
define('AJAX_EXT_LINKS_SRV', AJAX_SERVERS_PATH . 'ajax_ctaxon_extlinks.php');
define('AJAX_ENTREZ_RECORDS_SRV', AJAX_SERVERS_PATH . 'ajax_ctaxon_entrezrecords.php');

define('XML_FIGURES_AND_TABLES_NODE_NAME', 'article_figs_and_tables');
define('ARTICLE_PHOTOS_PROPID', 20);

define('IFRAME_PROXY_URL', PTP_URL . '/displayIframe.php');
define('NCBI_TAXON_URL', 'http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&lvl=3&lin=f&keep=1&srchmode=1&unlock&id=');

define('MORPHBANK_AJAX_CHECK_URL', 'http://morphbank10.sc.fsu.edu/MyManager/image.php?id=imageTab&numPerPage=20&keywords=');
define('LIAS_WEBSERVICE_URL', 'http://services.snsb.info/axis2/services/LiasNamesService/SearchTaxonNames?LIASString=');
define('LIAS_BROWSE_URL', 'http://liasnames.lias.net/DiversityTaxonNames_Fungi_LIASnames_List.jsp?SearchBy=Name&Name=');
define('LIAS_IFRAME_URL',  PTP_URL . '/LiasIframe.php?taxon_name=');
define('LIAS_FRAMES_BASE_URL', 'http://liasnames.lias.net/');
define('LIAS_TOP_FRAME_URL',  LIAS_FRAMES_BASE_URL . 'LiasNamesMenu.html');
define('LIAS_LEFT_FRAME_URL', LIAS_FRAMES_BASE_URL . 'empty2.html');

define('PHOTO_DISPLAY_PREFIX', 'oo_');
define('USE_PERL_EXECS', 0);
define('PERL_EXEC_ADDRESS', SITE_URL . '/cgi-bin/test.pl');

define('PENSOFT_SITE_URL', 'http://www.pensoft.net/');
define('CACHE_TIMEOUT_LENGTH', 14 * 24 * 3600);
//~ define('CACHE_TIMEOUT_LENGTH', 1);
define('GBIF_SITE_ID', 1);
define('NCBI_SITE_ID', 24);
define('WIKIMEDIA_SITE_ID', 27);
define('LIAS_SITE_ID', 38);
define('BHL_SITE_ID', -1);
?>