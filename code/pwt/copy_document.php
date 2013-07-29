<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

global $user;

if((int)$user->staff) {
	$lDocumentId = (int) $_REQUEST['document_id'];
	$lCopy = new cdocument_copy(array('document_id' => $lDocumentId));
	$lCopy->GetData();
	echo 'copy finished';
	echo $lCopy->GetNewDocumentId();
} else {
	echo 'you have no permissions';
}

?>