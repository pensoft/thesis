<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');


$gDocumentId = (int)$_REQUEST['document_id'];
if($gDocumentId){
	header("Content-type: text/xml");
	echo getDocumentXml($gDocumentId);
}

?>