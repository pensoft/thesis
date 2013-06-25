<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

$gCommentId = (int)$_REQUEST['comment_id'];

if(!$gCommentId)
	exit;
	
$lCon = Con();
$lSql = 'SELECT * FROM pwt.spdeletecomment(' . (int)$gCommentId . ', ' . (int)$user->id . ')';
$lCon->Execute($lSql);
$lCon->MoveFirst();

$lResult['result'] = (int)$lCon->mRs['spdeletecomment'];

displayAjaxResponse($lResult);
?>