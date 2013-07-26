<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gInstanceId = (int) $_REQUEST['instance_id'];
$gDocumentId = (int) $_REQUEST['document_id'];

if(! $gInstanceId && $gDocumentId){

	$lDocumentState = getDocumentState($gDocumentId);
	if($lDocumentState == DELETED_DOCUMENT_STATE){
		header('Location:/index.php');
		exit;
	}

	$gInstanceId = getDocumentFirstInstanceId($gDocumentId);
}

if(!$gDocumentId && $gInstanceId) {
	$lDocumentIdByInstanceId = getInstanceDocumentId($gInstanceId);
	$lDocumentState = getDocumentState($lDocumentIdByInstanceId);
	if($lDocumentState == DELETED_DOCUMENT_STATE){
		header('Location:/index.php');
		exit;
	}
}

if(! $gInstanceId){
	header('Location: /index.php');
	exit();
}

$gGetDataFromRequest = false;
$gGetObjectsModeFromRequest = false;
$lSaveMsg = '';
$lFieldValidationInfo = array();
if($_REQUEST['perform_save_action'] && $gDocumentId){
	$gGetObjectsModeFromRequest = true;
	$lSaveDocument = new cdocument_saver(array(
		'document_id' => $gDocumentId,
		'root_instance_id' => $gInstanceId,
		'instance_ids' => $_REQUEST['instance_ids']
	));
	$lSaveDocument->GetData();
	$gGetDataFromRequest = true;
	if((int) $lSaveDocument->hasErrors()){
		$lSaveMsg = $lSaveDocument->GetErrorMsg();
		if($lSaveDocument->HasValidationErrors()){
			$lFieldValidationInfo = $lSaveDocument->GetFieldValidationInfo();
		}
	}else{
		$gGetDataFromRequest = false;
		$lSaveMsg = getstr('pwt.save.successfulSaveMsg');
// 		saveDocumentXML( $gDocumentId );
	}
}

$gDocument = new cdisplay_document(
	array(
		'ctype' => 'cdisplay_document',
		'instance_id' => $gInstanceId,
		'get_data_from_request' => $gGetDataFromRequest,
		'get_object_mode_from_request' => $gGetObjectsModeFromRequest,
		'save_msg' => $lSaveMsg,
		'lock_operation_code' => LOCK_AUTO_LOCK,
		'field_validation_info' => $lFieldValidationInfo,
		'templs' => getDocumentDefaultTempls(1),
		'field_templs' => getDocumentFieldDefaultTempls(),
		'container_templs' => getDocumentContainerDefaultTempls(),
		'instance_templs' => getDocumentInstanceDefaultTempls(),
		'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
		'action_templs' => getDocumentActionsDefaultTempls(),
		'tabbed_element_templs' => getDocumentTabbedElementDefaultTempls(),
		'tree_templs' => getDocumentTreeDefaultTempls(),
		'path_templs' => getDocumentPathDefaultTempls(),
		'comments_templ' => 'comments',
		'comments_form_templ' => 'commentform',
	)
);


$gDocumentId = $gDocument->getDocumentId();
$gInstanceId = $gDocument->getInstanceId();

if($gInstanceId == getDocumentMetadataInstanceId($gDocumentId)){
	$lInstanceId = getDocumentFirstInstanceId($gDocumentId);
	header("Location: /display_document.php?document_id=" . (int) $gDocumentId . '&instance_id=' . $lInstanceId);
	exit();
}
checkDocumentMenuAndColumnsState($gDocumentId);
MarkActiveTab($gInstanceId); 
session_write_close();

$lPageArray = array(
	'title' => strip_tags($gDocument->getDocumentName()) . ' - ',
	'document' => $gDocument,
	'path' => $gDocument->getDocumentPath(),
	'document_id' => $gDocumentId,
);

$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(
	G_MAINBODY => 'global.document_page'
));
$inst->Display();
?>