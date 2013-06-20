<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');
session_write_close();

$lAction = $_REQUEST['action'];
$lInstanceId = (int)$_REQUEST['instance_id'];

if($lAction == 'check_has_materials') {
	$lCon = new DBCn();
	$lCon->Open();
	$lCon->Execute('select count(id) as cnt from pwt.document_object_instances where parent_id = ' . $lInstanceId);
	if($lCon->mRs['cnt'] > 0) {
		echo 'ok';
		exit;
	} else {
		echo 'no_materials';
		exit;
	}
}

?>