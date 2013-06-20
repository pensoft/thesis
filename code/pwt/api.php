<?php
$gDontRedirectToLogin = true;

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

ini_set('display_errors', 'Off');

// $gDocumentId = (int)$_REQUEST['document_id'];
// // if(!$gDocumentId)
// // 	$gDocumentId = 253;

// $gMode = (int)$_REQUEST['mode'];
// if(!in_array($gMode, array(1, 2))){
// 	$gMode = 2;
// }

// $lDocumentSerializer = new cdocument_serializer(array(
// // 	'document_id' => 183,
// 	'document_id' => $gDocumentId,
// 	'mode' => $gMode,
// ));
// $lDocumentSerializer->GetData();
// $lDocumentXml = $lDocumentSerializer->getXml();
// file_put_contents('./import2.xml', $lDocumentXml);


$gAction = $_REQUEST['action'];
$gXml = $_REQUEST['xml'];
// $gXml = file_get_contents('./import2.xml');
// var_dump($gXml);
// var_dump($gXml);
// exit;
// $gAction = 'process_document';
// $gXml = $lDocumentXml;

$lApi = new capi(array(
	'action' => $gAction,
	'xml' => $gXml,
	'username' => $_REQUEST['username'],
	'password' => $_REQUEST['password']
));
header("Content-type: text/xml");
echo $lApi->GetResult();

?>