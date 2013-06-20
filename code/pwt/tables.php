<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gInstanceId = (int) $_REQUEST['instance_id'];
$gDocumentId = (int) $_REQUEST['document_id'];

if(! $gInstanceId && $gDocumentId){
	$gInstanceId = getDocumentFirstInstanceId($gDocumentId);
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
		saveDocumentXML($gDocumentId);
	}
}

$lCon = new DBCn();
$lCon->Open();
$lSql = '
SELECT min(move_position) as min, max(move_position) as max
FROM pwt.tables
WHERE document_id = ' . $gDocumentId . ';
';
$lCon->Execute($lSql);
$lMinPos = (int)$lCon->mRs['min'];
$lMaxPos = (int)$lCon->mRs['max'];

$lFigures = new crs(array(
	'ctype' => 'crs',
	'templs' => array(
		G_HEADER => 'tables.document_tables_head',
		G_ROWTEMPL => 'tables.document_tables_row',
		G_FOOTER => 'tables.document_tables_foot',
		G_NODATA => 'global.empty'
	),
	'max_position' => $lMaxPos,
	'min_position' => $lMinPos,
	'sqlstr' => '
			SELECT * FROM pwt.tables WHERE document_id = ' . $gDocumentId . ' ORDER BY move_position ASC
		',
	'document_id' => $gDocumentId
));
$lFigures->GetData();
$lFiguresData = $lFigures->Display();

$gDocument = new cdisplay_document(array(
	'ctype' => 'cdisplay_document',
	'instance_id' => $gInstanceId,
	'get_data_from_request' => $gGetDataFromRequest,
	'get_object_mode_from_request' => $gGetObjectsModeFromRequest,
	'save_msg' => $lSaveMsg,
	'lock_operation_code' => LOCK_AUTO_LOCK,
	'field_validation_info' => $lFieldValidationInfo,
	'templs' => getDocumentTablesTempls(),
	'field_templs' => getDocumentFieldDefaultTempls(),
	'container_templs' => getDocumentContainerDefaultTempls(),
	'instance_templs' => getDocumentInstanceDefaultTempls(),
	'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
	'action_templs' => getDocumentActionsDefaultTempls(),
	'tree_templs' => getDocumentTreeDefaultTempls(),
	'path_templs' => getDocumentPathDefaultTempls(),
	'comments_templ' => 'comments',
	'comments_form_templ' => 'commentform',
	'tables_structure' => $lFiguresData
));

$gDocumentId = $gDocument->getDocumentId();
$gInstanceId = $gDocument->getInstanceId();

$lPageArray = array(
	'document' => $gDocument,
	'path' => $gDocument->getDocumentPath(),
	'document_id' => $gDocumentId
);

$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(
	G_MAINBODY => 'global.document_page'
));
$inst->Display();
?>