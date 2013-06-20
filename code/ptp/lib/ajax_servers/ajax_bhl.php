<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);
session_write_close();

$gTaxonName = trim(s($_REQUEST['taxon_name']));

$gBHL = new ctaxon_bhl (
	array(
		'icon_ajax_url' => AJAX_MENU_LINK_SRV . '?taxon_name=' . $gTaxonName . '&site_name=biodev&type=2',
		'templs' => array(
			G_HEADER => 'external_details.bhlHead',
			G_FOOTER => 'external_details.bhlFoot',
			G_STARTRS => 'external_details.bhlStart',
			G_ENDRS => 'external_details.bhlEnd',
			G_ROWTEMPL => 'external_details.bhl_title_row',
			G_VOLUME_TEMPL => 'external_details.bhl_volume',
			G_PAGE_TEMPL => 'external_details.bhl_page',
			G_NODATA_WRONG_XML => 'external_details.bhl_nodata_wrong_xml',
			G_NODATA => 'external_details.bhl_nodata',
		),
		'taxon_name' => $gTaxonName,
		'pagesize' => 7,
		'cache' => 'extdetails_bhl',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	)
);

$gBHL->GetDataC();
if( $gBHL->GetResultCount() ){
	$gBHL->AddNewAjaxRequiredObjectId('biodevLink');
}

SerializeAjaxOutput($gBHL);

?>