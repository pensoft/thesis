<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'on');

$gDocumentId = (int)$_REQUEST['document_id'];

if(!$gDocumentId)
	exit;


$lCon = new DBCn();
$lCon->Open();
$lSql = 'SELECT * FROM pwt.spdeletedocument(' . (int)$gDocumentId . ', ' . (int)$user->id . ')';
$lCon->Execute($lSql);
$lCon->MoveFirst();

$lResult['result'] = (int)$lCon->mRs['spdeletedocument'];

displayAjaxResponse($lResult);
?>