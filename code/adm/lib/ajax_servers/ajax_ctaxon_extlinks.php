<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);
session_write_close();

$gTaxonName = trim(s($_REQUEST['taxon_name']));
$gDataBase = trim(s($_REQUEST['database']));
$gDataBaseTitle = trim(s($_REQUEST['database_title']));
$gResultCount = (int)$_REQUEST['results_count'];

$gTaxonExtLinks =new ctaxon_extlinks(
	array(	
		'ctype' => 'ctaxon_extlinks',
		'templs' => array(
			G_STARTRS => 'external_details.extLinksStart',
			G_ENDRS => 'external_details.extLinksEnd',
			G_ROWTEMPL => 'external_details.extLinksRow',
		),
		'taxon_name' => $gTaxonName,
		'database' => $gDataBase,
		'database_title' => $gDataBaseTitle,
		'results_count' => $gResultCount,
		'cache' => 'extdetails_ncbiinfo_ajax',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	)
);

SerializeAjaxOutput($gTaxonExtLinks);

?>