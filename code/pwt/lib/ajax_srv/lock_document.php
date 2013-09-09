<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'on');

$gDocumentId = (int)$_REQUEST['document_id'];
$gAction = $_REQUEST['action'];


switch($gAction){
	case 'autolock_document':{
		$lSql = 'SELECT pwt.spAutoLockDocument(' . (int)$gDocumentId . ', ' . 2 * (int) DOCUMENT_LOCK_TIMEOUT_INTERVAL . ', ' . (int) DOCUMENT_AUTO_UNLOCK_INTERVAL . ', ' . (int)$user->id . ') as result';
		$lCon = new DBCn();
		$lCon->Open();
		$lResult = array(
			'err_cnt' => 0,
			'err_msg' => '',
			'document_is_auto_unlocked' => 0
		);
		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt'] = 1;
			$lResult['err_msg'] = getstr($lCon->GetLastError());
		}
		if(!$lCon->mRs['result']){
			$lResult['document_is_auto_unlocked'] = 1;			
		}
		
		displayAjaxResponse($lResult);
	}
	case 'unlock_document':{
		$lSql = 'SELECT * FROM pwt.spLockDocument(' . (int)$gDocumentId . ', ' . (int)LOCK_EXPLICIT_UNLOCK . ', ' . 2 * (int) DOCUMENT_LOCK_TIMEOUT_INTERVAL . ', ' . (int) DOCUMENT_AUTO_UNLOCK_INTERVAL . ', ' . q($user->id) . ') as res';
		$lCon = new DBCn();
		$lCon->Open();
		$lResult = array(
			'err_cnt' => 0,
			'err_msg' => ''
		);

		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt'] = 1;
			$lResult['err_msg'] = getstr($lCon->GetLastError());
		}elseif(!(int)$lCon->mRs['res']){
			$lResult['err_cnt'] = 1;
			$lResult['err_msg'] = getstr('pwt.youCantUnlockADocumentWhichIsLockedByAnotherUser');
		}
		displayAjaxResponse($lResult);
	}
}

?>