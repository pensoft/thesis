<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);
session_write_close();

$gTaxonName = trim(s($_REQUEST['taxon_name']));
$gSiteName = trim(s($_REQUEST['site_name']));
$gRowNum = (int) $_REQUEST['type'];


$lAjaxMenuLink = new cajaxmenulink(array(
	'site_name' => $gSiteName,
	'taxon_name' => $gTaxonName,
	'templs' => array(
		G_ROWTEMPL0 => 'external_details.ajaxMenuLinkRow',
		G_ROWTEMPL1 => 'external_details.ajaxExtLinkRow',
		G_ROWTEMPL2 => 'external_details.ajaxRightColIconLink',
		G_ROWTEMPL3 => 'article_html.taxonLinksMenuRowAjax',
	),
	'cache' => 'cajaxmenulink',
	'cachetimeout' => CACHE_TIMEOUT_LENGTH,
));

$lAjaxMenuLink->GetDataC();
$lAjaxMenuLink->setUseDisplayRowNum($gRowNum);


SerializeAjaxOutput($lAjaxMenuLink);

?>