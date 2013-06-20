<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
set_time_limit(160);
// ini_set('display_errors', 'Off');

// echo phpinfo();
// exit;

$gDocumentId = (int)$_REQUEST['document_id'];
if(!$gDocumentId)
	$gDocumentId = 240;

$gMode = (int)$_REQUEST['mode'];
if(!in_array($gMode, array(1, 2))){
	$gMode = 1;
}

$lDocumentSerializer = new cdocument_serializer1(array(
	'document_id' => $gDocumentId,
	'mode' => (int)$gMode,
));
$lDocumentSerializer->GetData();
$lXML = $lDocumentSerializer->getXml();
header('Content-type: application/xml');
echo($lXML);

?>