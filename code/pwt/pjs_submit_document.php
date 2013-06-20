<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

$lErrorCnt = 0;
$lErrorMsg = '';
$gDocumentId = (int) $_REQUEST['document_id'];

if(! $gDocumentId){
	$lErrorCnt ++;
	$lErrorMsg = getstr('pwt.submitNoDocumentId');
}else{
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT * FROM pwt.spSubmitDocument(' . (int) $gDocumentId . ', ' . (int) $user->id . ')';
	if(! $lCon->Execute($lSql)){
		$lErrorCnt ++;
		$lErrorMsg = getstr($lCon->GetLastError());
	}
}

if($lErrorCnt){
	$lContents = new csimple(array(
		'templs' => array(
			G_DEFAULT => 'submit_document.error'
		),
		'err_msg' => $lErrorMsg
	));
// 	var_dump($lContents->Display());
}else{
	header('Location: ' . PJS_SITE_URL . '/create_document_from_pwt.php?document_id=' . (int) $gDocumentId);
	exit();
}
$lPageArray = array(
	'contents' => $lContents
);

$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(
	G_MAINBODY => 'global.simple_page'
));
$inst->Display();
?>