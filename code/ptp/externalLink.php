<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);

$gQuery = trim(rawurldecode($_REQUEST['url']));
$gTaxonName = $_REQUEST['taxon_name'];
$gPostForm = $_REQUEST['postform'];
$gPostFields = rawurldecode($_REQUEST['postfields']);
$t = array (
	'content' => array(
		'ctype' => 'csimple_extlinks',
		'topmenu' => array(
			'ctype' => 'csimple',
			'templs' => array(
				G_DEFAULT => 'external_details.topMenu',
			),
		),'menus_templs' => array(
			G_STARTRS => 'external_details.extLinkMenuStart',
			G_ENDRS => 'external_details.extLinkMenuEnd',
			G_ROWTEMPL => 'external_details.extLinkMenuRow',
			G_ROWTEMPL_AJAX => 'external_details.extLinkMenuRowAjax',
		),
		'menus' => array(
			'general_menu' => array(
				'label' => 'General',
				'ajax_link_template_type' => 1,
				'links' => array(
					'gbif' => AJAX_CLINKS_MENU_LINK,
					'ncbi' => AJAX_CLINKS_MENU_LINK, 
					'eol' => AJAX_CLINKS_MENU_LINK,
					'wikipedia' => AJAX_CLINKS_MENU_LINK,
					'ubio' => AJAX_CLINKS_MENU_LINK,
				),
			),
		),
		'url' => $gQuery,
		'postform' => $gPostForm,
		'postfields' => $gPostFields,
		'taxon_name' => $gTaxonName,
		'templs' => array(
			G_DEFAULT => 'external_details.extLinkRow',
		),
		'cache' => 'extlink_holder',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	),
);		



$inst = new cpage($t, array(G_MAINBODY => 'global.externalLink'));
$inst->Display();
	
?>