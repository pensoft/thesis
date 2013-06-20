<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);
session_write_close();

$gTaxonName = trim(s($_REQUEST['taxon_name']));

$gWikimedia = new ctaxon_morphbank (
	array(
		'ctype' => 'ctaxon_morphbank',
		'icon_ajax_url' => AJAX_MENU_LINK_SRV . '?taxon_name=' . $gTaxonName . '&site_name=morphbank&type=2',
		'icon_div_id' => 'morphbankLink',
		'ajax_link' => AJAX_WIKIMEDIA_SRV . '?taxon_name=' . $gTaxonName,
		'title_label' => 'Images from Morphbank',
		'itemsonrow' => 5,
		'templs' => array(
			G_STARTRS => 'external_details.imagesStart',
			G_ENDRS => 'external_details.imagesEnd',
			G_ROWTEMPL => 'external_details.imagesRow',
			G_NODATA => 'external_details.imagesNoData',
		),
		'taxon_name' => $gTaxonName,
		'cache' => 'ctaxon_morphbank_ajax',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	)
);

$gWikimedia->GetDataC();
if( $gWikimedia->GetResultCount() ){
	$gWikimedia->AddNewAjaxRequiredObjectId('morphbankLink');
}else{
	$gWikimedia->AddNewAjaxRequiredObjectId('wikimediaAjax');
}

SerializeAjaxOutput($gWikimedia);

?>