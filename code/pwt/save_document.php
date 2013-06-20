<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$gDocumentId = (int)$_REQUEST['document_id'];
$gRootInstanceId = (int)$_REQUEST['instance_id'];
$lSaveDocument = new cdocument_saver(array(
	'document_id' => $gDocumentId,
	'root_instance_id' => $gRootInstanceId
));
$lSaveDocument->GetData();

if((int)$lSaveDocument->hasErrors()){
	$lContents = new csimple(array(
		'document_id' => $gDocumentId,
		'instance_id' => $gRootInstanceId,
		'err_msg' => $lSaveDocument->getErrorMsg(),
		'templs' => array(
			G_DEFAULT => 'document.error_while_saving_msg',
		)
	));
}else{
	$lContents = new csimple(array(
		'document_id' => $gDocumentId,
		'instance_id' => $gRootInstanceId,
		'templs' => array(
			G_DEFAULT => 'document.successful_saving_msg',
		)
	));
	saveDocumentXML( $gDocumentId );
}

$lPageArray = array(
	'contents' => $lContents
);

$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(G_MAINBODY => 'global.simple_page'));
$inst->Display();
?>