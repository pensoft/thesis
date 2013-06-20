<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);

$gTaxonName = trim(rawurldecode($_REQUEST['taxon_name']));
$lUrl = LIAS_BROWSE_URL . rawurldecode($gTaxonName);
$t = array (
	'content' => array(
		'ctype' => 'csimple',
		'url' => $lUrl,
		'postfields' => '',
		'taxon_name' => $gTaxonName,
		'templs' => array(
			G_DEFAULT => 'external_details.liasIframe',
		),
		'cache' => 'lias_iframe',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	),
);		



$inst = new cpage($t, array(G_MAINBODY => 'global.externalFrameset'));
$inst->Display();
	
?>