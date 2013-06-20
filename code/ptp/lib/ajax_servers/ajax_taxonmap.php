<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);
session_write_close();

$gTaxonName = trim(s($_REQUEST['taxon_name']));

$gMap =new ctaxonmap(
	array(	
		'ctype' => 'ctaxonmap',
		'templs' => array(
			G_STARTRS => 'external_details.mapHead',
			G_ENDRS => 'external_details.mapFoot',
			G_ROWTEMPL => 'external_details.mapRow',
			G_NODATA => 'external_details.mapNoData',
		),
		'taxon_name' => $gTaxonName,
		'cache' => 'extdetails_gbifmap',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	)
);

SerializeAjaxOutput($gMap);

?>