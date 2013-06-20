<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
// ini_set('display_errors', 'Off');

$gDocumentId = (int)$_REQUEST['document_id'];
if(!$gDocumentId)
	$gDocumentId = 147;

$lDocumentSerializer = new cdocument_xsd_generator(array(
	'document_id' => $gDocumentId,
	'mode' => 1,
));
$lDocumentSerializer->GetData();
header("Content-type: text/xml");
echo $lDocumentSerializer->getXml();

?>