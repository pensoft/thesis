<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);
session_write_close();

$gTaxonName = trim(s($_REQUEST['taxon_name']));

$gWikimedia = new ctaxon_mediawikiimages (
	array(
		'ctype' => 'ctaxon_mediawikiimages',
		'icon_ajax_url' => AJAX_MENU_LINK_SRV . '?taxon_name=' . $gTaxonName . '&site_name=wikimedia&type=2',
		'icon_div_id' => 'wikimediaLink',
		'title_label' => 'Images from Wikimedia',
		'itemsonrow' => 5,
		'templs' => array(
			G_STARTRS => 'external_details.imagesStart',
			G_ENDRS => 'external_details.imagesEnd',
			G_ROWTEMPL => 'external_details.imagesRow',
		),
		'taxon_name' => $gTaxonName,
		'cache' => 'ctaxon_mediawikiimages_ajax',
		'cachetimeout' => CACHE_TIMEOUT_LENGTH,
	)
);

$gWikimedia->GetDataC();
if( $gWikimedia->GetResultCount() ){
	$gWikimedia->AddNewAjaxRequiredObjectId('wikimediaLink');
}

SerializeAjaxOutput($gWikimedia);

?>