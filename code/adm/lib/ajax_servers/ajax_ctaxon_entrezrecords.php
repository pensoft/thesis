<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);
session_write_close();

$gTaxonName = trim(s($_REQUEST['taxon_name']));
$gTaxonId = trim(s($_REQUEST['taxon_id']));
$gAllowedDB = array();
foreach( $_REQUEST['allowed_databases'] as $lName => $lLabel){
	$gAllowedDB[s($lName)] = s($lLabel);
}

$gEntrezRecords =new ctaxon_entrezrecords(
	array(	
		'ctype' => 'ctaxon_entrezrecords',
		'templs' => array(
			G_STARTRS => 'external_details.entrezRecordsStart',
			G_ENDRS => 'external_details.entrezRecordsEnd',
			G_ROWTEMPL => 'external_details.entrezRecordsRow',
			G_AJAX_DEFAULT => 'external_details.entrezRecordsAjax',
		),
		'taxon_id' => $gTaxonId,
		'allowed_databases' => $gAllowedDB,//Bazite za koito she izkarvame broikata
		'taxon_name' => $gTaxonName,
		'cache' => 'entrezrecords_ncbiinfo_ajax',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	)
);

SerializeAjaxOutput($gEntrezRecords);

?>