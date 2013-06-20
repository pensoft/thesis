<?php
error_reporting((int)ERROR_REPORTING);
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$gTaxonName = trim($_REQUEST['taxon_name']);
$t = array (
	'content' => array(
		'ctype' => 'csimple_extlinks',
		'menus_templs' => array(
			G_STARTRS => 'article_html.taxonLinksMenuStart',
			G_ENDRS => 'article_html.taxonLinksMenuEnd',
			G_ROWTEMPL => 'article_html.taxonLinksMenuRow',
			G_ROWTEMPL_AJAX => 'article_html.taxonLinksMenuAjax',
		),
		'menus' => array(
			'general_menu' => array(
				'label' => 'General',
				'ajax_link_template_type' => 3,
				'links' => array(
					'gbif' => STATIC_CLINKS_MENU_LINK,
					'ncbi' => STATIC_CLINKS_MENU_LINK, 
					'eol' => STATIC_CLINKS_MENU_LINK, 
					'biodev' => STATIC_CLINKS_MENU_LINK, 
					'wikipedia' => STATIC_CLINKS_MENU_LINK,
					//~ 'gbif','ncbi', 'col', 'itis', 'bold', 'eol', 'wikipedia', 'zoobank', 'ipni', 'fungorum', 'biodev', 'pubmed', 'google_scholar', 'wikimedia', 'yahoo_images',
				),//Vsichki linkove				
			),
		),
		'content_is_static' => true,
		'taxon_name' => $gTaxonName,
		'templs' => array(
			G_DEFAULT => 'article_html.taxonMenu',
		),
		'cache' => 'taxon_menu',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	),
);		

$inst = new cpage($t, array(G_MAINBODY => 'global.taxonMenu'));
$inst->Display();
	
?>