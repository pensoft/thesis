<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');
session_write_close();

$gInstanceId = 		(int)$_REQUEST['real_instance_id'];
$gRootInstanceId = 	(int)$_REQUEST['root_instance_id'];
$gDocumentId = 		(int)$_REQUEST['document_id'];
$gLevel = 			(int)$_REQUEST['level'];
$gAutoSaveOn = 		(int)$_REQUEST['auto_save_on'];
$gExplicitFieldId = (int)$_REQUEST['explicit_field_id'];
$gInPopup = 		(int)$_REQUEST['in_popup'];

// check if document is deleted
$lDocumentState = getDocumentState($gDocumentId, $gInstanceId);
if($lDocumentState == DELETED_DOCUMENT_STATE) {
	$lRes['err_cnt'] = 1;
	$lRes['err_msg'] = getstr('pwt.document_is_deleted');
	displayAjaxResponse($lRes);
	exit();
}

checkIfDocumentIsLockedByTheCurrentUserForAjax(0, $gDocumentId);

$lResult = array(
	'err_cnt' => 0,
	'err_msg' => '',
	'validation_err_cnt' => 0,
	'action_is_successful' => 1
);
if($gInstanceId && $gDocumentId){
	$gGetObjectsModeFromRequest = true;
	$lSaveDocument = new cdocument_saver(array(
		'document_id' => $gDocumentId,
		'root_instance_id' => $gInstanceId,
		'instance_ids' => $_REQUEST['instance_ids'],
		'auto_save_on' => $gAutoSaveOn,
		'explicit_field_id' => $gExplicitFieldId,
		'in_popup' => $gInPopup
	));
	$lSaveDocument->GetData();
	$gGetDataFromRequest = true;
	$lPreviewGenerator = new cinstance_preview_generator(array(
		'template_xsl_dirname' => GetDocumentXslDirName($gDocumentId),
		'document_id' => $gDocumentId,
		'document_xml' => getDocumentXml($gDocumentId, SERIALIZE_INTERNAL_MODE, false, false, $gRootInstanceId),
	));
	if((int) $lSaveDocument->hasErrors()){
		$lResult['err_cnt'] = 1;
		$lResult['action_is_successful'] = 0;
		$lResult['validation_err_cnt'] = 1;
		$lResult['err_msg'] = br2nl($lSaveDocument->GetErrorMsg());
		if($lSaveDocument->HasValidationErrors()){
			$lFieldValidationInfo = $lSaveDocument->GetFieldValidationInfo();
		}

		$lDisplayUnconfirmedInstances = false;
		if($gInPopup){
			$lDisplayUnconfirmedInstances = true;
		}
		$lInstance = new cdocument_instance(array(
			'templs' => getDocumentInstanceDefaultTempls(),
			'field_templs' => getDocumentFieldDefaultTempls($gInPopup),
			'container_templs' => getDocumentContainerDefaultTempls(),
			'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
			'action_templs' => getDocumentActionsDefaultTempls(),
			'tabbed_element_templs' => getDocumentTabbedElementDefaultTempls(),
			'display_unconfirmed_objects' => $lDisplayUnconfirmedInstances,

			'instance_id' => $gInstanceId,
			'mode' => INSTANCE_EDIT_MODE,

			'root_instance_id' => $gRootInstanceId,
			'level' => $gLevel,
			'get_data_from_request' => true,
			'get_object_mode_from_request' => true,
			'field_validation_info' => $lFieldValidationInfo,
			'use_preview_generator' => true,
			'preview_generator' => $lPreviewGenerator,
		));
		$lResult['instance_html'] = $lInstance->Display();
	}else{
		/*
			Here we dont care if we are in popup
			because if there are no errors the popup will be closed and the object
			will be displayed in non popup mode
		*/
		if((int)$_REQUEST['get_instance_html']){
			$lInstance = new cdocument_instance(array(
				'templs' 				=> getDocumentInstanceDefaultTempls(),
				'field_templs' 			=> getDocumentFieldDefaultTempls(),
				'container_templs' 		=> getDocumentContainerDefaultTempls(),
				'custom_html_templs' 	=> getDocumentCustomHtmlItemsDefaultTempls(),
				'action_templs' 		=> getDocumentActionsDefaultTempls(),
				'tabbed_element_templs' => getDocumentTabbedElementDefaultTempls(),

				'instance_id' => $gInstanceId,
				'mode' => (int)$_REQUEST['mode_after_successful_save'],
				'root_instance_id' => $gRootInstanceId,
				'level' => $gLevel,
				'field_validation_info' => $lFieldValidationInfo,
				'use_preview_generator' => true,
				'preview_generator' => $lPreviewGenerator,
			));
			$lResult['instance_html'] = $lInstance->Display();
		}
		$lResult['container_id'] = GetInstanceContainerId($gInstanceId);
		$lResult['parent_instance_id'] = GetInstanceParentInstanceId($gInstanceId);
	}
	$lPreviewGenerator->SetTemplate($lResult['instance_html']);
	$lResult['instance_html'] = $lPreviewGenerator->Display();
}else{
	$lResult = array(
		'err_cnt' => 1,
		'action_is_successful' => 0,
		'err_msg' => getstr('pwt.missingRequiredParameters'),
	);
}

displayAjaxResponse($lResult);








?>