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
		saveDocumentXML( $gDocumentId );
	}
}

$lXmlVal = new cdocument_xml_validator(array(
	'document_id' => $gDocumentId,
	'templs' => array(
		G_DEFAULT => 'validation.document_errors'
	),
	'generated_xml' => '/generate_xml.php?document_id=' . $gDocumentId,
	'generated_xsd' => '/generate_xsd.php?document_id=' . $gDocumentId
));
$lXmlErrCount = $lXmlVal->GetXmlErrors();
$lXmlInstances = $lXmlVal->GetFieldsInstances();
//~ print_r($lXmlInstances);

$gDocument = new cdisplay_document(
	array(
		'ctype' => 'cdisplay_document',
		'instance_id' => $gInstanceId,
		'get_data_from_request' => $gGetDataFromRequest,
		'get_object_mode_from_request' => $gGetObjectsModeFromRequest,
		'save_msg' => $lSaveMsg,
		'lock_operation_code' => LOCK_AUTO_LOCK,
		'field_validation_info' => $lFieldValidationInfo,
		'templs' => getDocumentXMLValidationTempls(),
		'field_templs' => getDocumentFieldDefaultTempls(),
		'container_templs' => getDocumentContainerDefaultTempls(),
		'instance_templs' => getDocumentInstanceDefaultTempls(),
		'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
		'action_templs' => getDocumentActionsDefaultTempls(),
		'tree_templs' => getDocumentTreeDefaultTempls(),
		'path_templs' => getDocumentPathDefaultTempls(),
		'comments_templ' => 'comments',
		'comments_form_templ' => 'commentform',
		'xml_validation' => $lXmlVal,
		'xml_validation_field_instances' => $lXmlInstances,
		'xml_validation_flag' => 1
	)
);

$gDocumentId = $gDocument->getDocumentId();
$gInstanceId = $gDocument->getInstanceId();
$gDocumentName = $gDocument->getDocumentName();

$lPathTempl = 'validation.document_path';
if(!(int)$lXmlErrCount) {
	$lPathTempl = 'validation.document_path_valid';
}

$lPath = new csimple (
	array(
		'ctype' => 'csimple',
		'document_name' => $gDocumentName,
		'document_id' => $gDocumentId,
		'templs' => array(
			G_DEFAULT => $lPathTempl,
		),
	)
);

$lPageArray = array(
	'title' => strip_tags($gDocument->getDocumentName()) . ' - ',
	'document' => $gDocument,
	'path' => $lPath->Display(),
	'document_id' => $gDocumentId,
	'xml_errors' => (int)$lXmlErrCount,
	'xml_validation' => 1,
);

// Samo sym smenil templeita. Stariq e 'global.document_page'
$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(
	G_MAINBODY => 'global.document_page'
));
$inst->Display();

?>