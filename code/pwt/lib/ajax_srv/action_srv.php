<?php
$gTryToChangeUserWithoutSessionChange = $_REQUEST['try_to_change_user_without_session_change'];

$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

session_write_close();


// var_dump($user);
$gInstanceId = (int) $_REQUEST['instance_id'];
$gActionId = (int) $_REQUEST['action_id'];
$gDocumentId = (int) $_REQUEST['document_id'];

// check if document is deleted
$lDocumentState = getDocumentState($gDocumentId, $gInstanceId);
if($lDocumentState == DELETED_DOCUMENT_STATE) {
	$lRes['err_cnt'] = 1;
	$lRes['err_msg'] = getstr('pwt.document_is_deleted');
	displayAjaxResponse($lRes);
	exit();
}

checkIfDocumentIsLockedByTheCurrentUserForAjax((int)$gInstanceId, $gDocumentId);

if(! $gInstanceId || ! $gActionId)
	exit();

$lCon = Con();
$lSql = 'SELECT * FROM pwt.actions WHERE id = ' . (int) $gActionId;
$lCon->Execute($lSql);
$lEvalCode = $lCon->mRs['eval_code'];

eval($lEvalCode);

saveDocumentXML( getDocumentIdByInstanceId($gInstanceId) );

?>