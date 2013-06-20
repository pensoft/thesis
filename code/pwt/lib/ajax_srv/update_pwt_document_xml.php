<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');
session_write_close();

$lDocumentId = (int)$_REQUEST['document_id'];

if((int)$lDocumentId) {
	saveDocumentXML($lDocumentId);
	echo 'ok';
} else {
	echo 'err';
}

?>