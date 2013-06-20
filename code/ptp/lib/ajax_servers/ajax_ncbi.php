<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);
session_write_close();

$gTaxonName = trim(s($_REQUEST['taxon_name']));

$lNCBI =new ctaxon_ncbiinfo(
	array(	
		'ctype' => 'ctaxon_ncbiinfo',
		'icon_ajax_url' => AJAX_MENU_LINK_SRV . '?taxon_name=' . $gTaxonName . '&site_name=ncbi&type=2',
		'call_sub_ajax_queries' => 1,
		'templs' => array(
			G_STARTRS => 'external_details.ncbiStart',
			G_ENDRS => 'external_details.ncbiEnd',
			G_ROWTEMPL => 'external_details.ncbiRow',
		),
		'lineage_templs' => array(
			G_STARTRS => 'external_details.ncbiLineageStart',
			G_ENDRS => 'external_details.ncbiLineageEnd',
			G_ROWTEMPL => 'external_details.ncbiLineageRow',
		),
		'link_templs' => array(
			G_STARTRS => 'external_details.extLinksStart',
			G_ENDRS => 'external_details.extLinksEnd',
			G_ROWTEMPL => 'external_details.extLinksRow',
		),
		'link_ajax_templs' => array(
			G_DEFAULT => 'external_details.extLinksAjax',
		),
		'link_result_count' => 5,
		'link_database' => EUTILS_PUBMED_DB,
		'link_database_title' => 'PubMed',
		'entrezrecords_templs' => array(
			G_STARTRS => 'external_details.entrezRecordsStart',
			G_ENDRS => 'external_details.entrezRecordsEnd',
			G_ROWTEMPL => 'external_details.entrezRecordsRow',
		),
		'entrezrecords_ajax_templs' => array(
			G_DEFAULT => 'external_details.entrezRecordsAjax',
		),
		'entrez_records_allowed_databases' => array(
			EUTILS_PMC_DB => EUTILS_PMC_DISPLAY_NAME, 
			EUTILS_TAXONOMY_DB => '', 
			EUTILS_PROTEIN_DB => '',
			EUTILS_POPSET_DB => '', 
			EUTILS_NUCCORE_DB => EUTILS_NUCCORE_DISPLAY_NAME
		),
		'taxon_name' => $gTaxonName,
		'cache' => 'extdetails_ncbiinfo',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	)
);

$lNCBI->GetDataC();
if( $lNCBI->GetResultCount() ){
	$lNCBI->AddNewAjaxRequiredObjectId('ncbiLink');
	if( $lNCBI->GetVal('call_sub_ajax_queries') ){
		$lNCBI->AddNewAjaxRequiredObjectId('extlinks_ajax');
		$lNCBI->AddNewAjaxRequiredObjectId('entrez_records_ajax');
	}
}

SerializeAjaxOutput($lNCBI);

?>