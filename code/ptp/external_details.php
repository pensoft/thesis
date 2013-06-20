<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);
session_write_close();
$gQuery = trim(s($_REQUEST['query']));
$gSearchType = (int) $_REQUEST['type'];
global $gTimeLogger;
$gTimeLogger->RegisterTaxon($gQuery);
$gTimeLogger->RegisterObject(0, 'cpage', 0, array('query' => $gQuery, 'search_type' => $lSearchType));
if( $gQuery ){
	$lLeftCol =  new csimple_extlinks(array(
		'templs' => array(
			G_DEFAULT => 'external_details.leftcol',
		),
		'menus_templs' => array(
			G_STARTRS => 'external_details.leftLinksMenuStart',
			G_ENDRS => 'external_details.leftLinksMenuEnd',
			G_ROWTEMPL => 'external_details.leftLinksMenuRow',
			G_ROWTEMPL_AJAX => 'external_details.leftLinksMenuRowAjax',
		),
		'menus' => array(
			'general_menu' => array(
				'label' => 'General',
				'links' => array(
					'gbif' => AJAX_CLINKS_MENU_LINK,
					'eol' => AJAX_CLINKS_MENU_LINK,
					'col' => AJAX_CLINKS_MENU_LINK,
					'itis' => AJAX_CLINKS_MENU_LINK,
					'worms' => AJAX_CLINKS_MENU_LINK,
					'wikipedia' => AJAX_CLINKS_MENU_LINK,
					'wikispecies' => AJAX_CLINKS_MENU_LINK,
					'iucn' => AJAX_CLINKS_MENU_LINK,
					'biolib' => AJAX_CLINKS_MENU_LINK,
					'plazi' => AJAX_CLINKS_MENU_LINK,
					'daisie' => AJAX_CLINKS_MENU_LINK,
					'invasive' => AJAX_CLINKS_MENU_LINK,
				),
			),
			'taxonomy_menu' => array(
				'label' => 'Taxonomy',
				'links' => array(
					'fungorum' => AJAX_CLINKS_MENU_LINK,
					'ipni' => AJAX_CLINKS_MENU_LINK,
					'algaebase' => AJAX_CLINKS_MENU_LINK, 
					'tropicos' => AJAX_CLINKS_MENU_LINK, 
					'usda' => AJAX_CLINKS_MENU_LINK, 
					'gymnosperm' => AJAX_CLINKS_MENU_LINK, 
					'zoobank' => AJAX_CLINKS_MENU_LINK, 
					'fa' => AJAX_CLINKS_MENU_LINK, 
					'tol' => AJAX_CLINKS_MENU_LINK, 
					'treebase' => AJAX_CLINKS_MENU_LINK, 
					'chilobase' => AJAX_CLINKS_MENU_LINK, 
					'hymenopterans' => AJAX_CLINKS_MENU_LINK,
					//~ 'diptera' => AJAX_CLINKS_MENU_LINK,
					'lias' => AJAX_CLINKS_MENU_LINK,
				),
			),
			'sequences_menu' => array(
				'label' => 'Gene Sequences',
				'links' => array(
					'ncbi' => STATIC_CLINKS_MENU_LINK, 
					'bold' => STATIC_CLINKS_MENU_LINK,
				),
			),
			'images_menu' => array(
				'label' => 'Images',
				'links' => array(
					'morphbank' => AJAX_CLINKS_MENU_LINK, 
					'wikimedia' => AJAX_CLINKS_MENU_LINK, 
					'yahoo_images' => AJAX_CLINKS_MENU_LINK,
				),
			),
			'literature_menu' => array(
				'label' => 'Literature',
				'links' => array(
					'google_scholar' => AJAX_CLINKS_MENU_LINK, 
					'pubmed' => AJAX_CLINKS_MENU_LINK, 
					'biodev' => AJAX_CLINKS_MENU_LINK,
				),
			),
		),				
		'taxon_name' => $gQuery,
		'cache' => 'extdetails_leftcol',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	));

	$lMap =new csimple(
		array(	
			'ctype' => 'csimple',
			'templs' => array(
				G_DEFAULT => 'external_details.mapAjax',
			),
			'taxon_name' => $gQuery,
			'ajax_link' => AJAX_TAXON_MAP_SRV . '?taxon_name=' . $gQuery,
			'cache' => 'csimple_ajax_taxonmap',
			'cachetimeout' => CACHE_TIMEOUT_LENGTH,
		)
	);

	$lNCBI =new csimple(
		array(	
			'ctype' => 'csimple',
			'templs' => array(
				G_DEFAULT => 'external_details.ncbiAjax',
			),
			'taxon_name' => $gQuery,
			'ajax_link' => AJAX_NCBI_SRV . '?taxon_name=' . $gQuery,
			'cache' => 'csimple_ajax_ncbi',
			'cachetimeout' => CACHE_TIMEOUT_LENGTH,
		)
	);

	$gBHL =new csimple(
		array(	
			'ctype' => 'csimple',
			'templs' => array(
				G_DEFAULT => 'external_details.bhlAjax',
			),
			'taxon_name' => $gQuery,
			'ajax_link' => AJAX_BHL_SRV . '?taxon_name=' . $gQuery,
			'cache' => 'csimple_ajax_bhl',
			'cachetimeout' => CACHE_TIMEOUT_LENGTH,
		)
	);

	$gImages =new csimple(
		array(	
			'ctype' => 'csimple',
			'templs' => array(
				G_DEFAULT => 'external_details.morphbankAjax',
				//~ G_DEFAULT => 'external_details.wikimediaAjax',
			),
			'taxon_name' => $gQuery,
			'ajax_link' => AJAX_MORPHBANK_SRV . '?taxon_name=' . $gQuery,
			'cache' => 'csimple_ajax_morphbank',
			//~ 'ajax_link' => AJAX_WIKIMEDIA_SRV . '?taxon_name=' . $gQuery,
			//~ 'cache' => 'csimple_ajax_wikimedia',
			'cachetimeout' => CACHE_TIMEOUT_LENGTH,
		)
	);

	$gProfileNodata = new csimple(
		array(
			'ctype' => 'csimple',
			'templs' => array(
				G_DEFAULT => 'external_details.RightColNoData',
			),
		)
	);

	$lRightCol =  new csimple(array(
		'templs' => array(
			G_DEFAULT => 'external_details.rightcol',
		),
		'map' => $lMap,
		'ncbiinfo' => $lNCBI,
		'images' => $gImages,
		'profile_nodata' => $gProfileNodata,
		'topmenu' => array(
			'ctype' => 'csimple',
			'templs' => array(
				G_DEFAULT => 'external_details.topMenu',
			),
		),
		'bhl' => $gBHL,
		'taxon_name' => $gQuery,
		'cache' => 'extdetails_rightcol',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	));
	
	
}else{
	$lLeftCol = new csimple(
		array(
			'ctype' => 'csimple',
			'templs' => array(
				G_DEFAULT => 'external_details.defaultTaxonPage',
			),
			'topmenu' => array(
				'ctype' => 'csimple',
				'templs' => array(
					G_DEFAULT => 'external_details.topMenu',
				),
			),
		)
	);
}

$lTemplate = 'global.externaldetails';

$t = array (
	'leftcol' => $lLeftCol,
	'rightcol' => $lRightCol
);						


$inst = new cpage($t, array(G_MAINBODY => $lTemplate));
$inst->Display();
$gTimeLogger->RegisterObjectEvent(0, 'finished_retrieving_data');
$gTimeLogger->RegisterObjectEvent(0, 'finished_parsing_data');
$gTimeLogger->Display();
?>