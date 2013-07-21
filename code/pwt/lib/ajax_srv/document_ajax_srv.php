<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');
session_write_close();

$gAction = $_REQUEST['action'];
$gDocumentId = $_REQUEST['document_id'];
$gInstanceId = $_REQUEST['instance_id'];
$gRootInstanceId = $_REQUEST['root_instance_id'];
$gMode = (int)$_REQUEST['mode'];
$gLevel = (int)$_REQUEST['level'];
$gActionId = (int)$_REQUEST['action_id'];

if($gAction == 'move_up_in_tree' || $gAction == 'move_down_in_tree'){
	$lOper = 1;
	if($gAction == 'move_up_in_tree'){
		$lOper = 2;
	}
	checkIfDocumentIsLockedByTheCurrentUserForAjax($gInstanceId, 0);
	$lResult = MoveInstanceInDocumentTree($gInstanceId, $lOper);
}elseif($gAction == 'display_instance_contents'){
	$lResult = array(
		'err_cnt' => 0,
		'err_msg' => '',
		'html' => '',
	);
// 	var_dump('Before :' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6));
	if($gDocumentId && $gInstanceId && $gLevel && $gRootInstanceId){
		$lCon = new DBCn();
		$lCon->Open();
		$lSql = 'SELECT is_confirmed::int as is_confirmed FROM pwt.document_object_instances WHERE id = ' . (int)$gInstanceId;
		$lCon->Execute($lSql);
		$lDisplayUnconfirmedObjects = false;
		if(!(int)$lCon->mRs['is_confirmed']){
			$lDisplayUnconfirmedObjects = true;
		}


		$lInstance = new cdocument_instance(array(
			'templs' => getDocumentInstanceDefaultTempls(),
			'field_templs' => getDocumentFieldDefaultTempls(),
			'container_templs' => getDocumentContainerDefaultTempls(),
			'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
			'action_templs' => getDocumentActionsDefaultTempls(),
			'tabbed_element_templs' => getDocumentTabbedElementDefaultTempls(),
			'display_unconfirmed_objects' => $lDisplayUnconfirmedObjects,

			'instance_id' => $gInstanceId,
			'mode' => $gMode,
			'level' => $gLevel,
			'root_instance_id' => $gRootInstanceId,
			'get_data_from_request' => FALSE,
		));
		$lResult['html'] = $lInstance->Display();
	}else{
		$lResult['err_cnt'] = 1;
		$lResult['err_msg'] = getstr('pwt.instance.missingRequiredParameters');
	}
// 	var_dump('After :' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6));
}elseif($gAction == 'get_action_details'){
	$lResultParameters = getActionParameters($gInstanceId, $gActionId);
	$lCon = new DBCn();
	$lCon->Open();
	$lResult = array();

	$lSql = 'SELECT a.*, c.type as eval_return_type
		FROM pwt.actions a
		JOIN pwt.actions_eval_code_return_types c ON c.id = a.eval_code_return_type
		WHERE a.id = ' . (int) $gActionId;
	$lCon->Execute($lSql);

	$lResult = array(
		'err_cnt' => 0,
		'err_msg' => '',
		'parameters' => $lResultParameters,
	);

	$lResult['callback'] = $lCon->mRs['callback_eval_code_js'];
	$lResult['eval_return_type'] = $lCon->mRs['eval_return_type'];

}elseif($gAction == 'get_classification_root'){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT id, rootnode FROM taxon_categories WHERE id = ' . (int)$_REQUEST['selected_value'];
	$lCon->Execute($lSql);
	$lResult['root'] = (int)$lCon->mRs['rootnode'];
	if(!$lResult['root']){
		$lResult['root'] = (int)$lCon->mRs['id'];
	}
}elseif($gAction == 'get_reference_parent_instance_id'){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT i.id
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.parent_id = i.document_template_object_id AND dto.object_id = ' . (int)REFERENCE_OBJECT_ID . '
		WHERE i.document_id = ' . (int)$_REQUEST['document_id'];
	$lCon->Execute($lSql);
// 	var_dump($lSql);
	$lResult['instance_id'] = (int)$lCon->mRs['id'];
}elseif($gAction == 'get_sup_files_parent_instance_id'){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT i.id
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.parent_id = i.document_template_object_id AND dto.object_id = ' . (int)SUP_FILE_OBJECT_ID . '
		WHERE i.document_id = ' . (int)$_REQUEST['document_id'];
	$lCon->Execute($lSql);
// 	var_dump($lSql);
	$lResult['instance_id'] = (int)$lCon->mRs['id'];
}elseif($gAction == 'get_figures_parent_instance_id'){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT i.id
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.parent_id = i.document_template_object_id AND dto.object_id = ' . (int)FIGURE_OBJECT_ID . '
		WHERE i.document_id = ' . (int)$_REQUEST['document_id'];
	$lCon->Execute($lSql);
// 	var_dump($lSql);
	$lResult['instance_id'] = (int)$lCon->mRs['id'];
}elseif($gAction == 'get_tables_parent_instance_id'){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT i.id
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.parent_id = i.document_template_object_id AND dto.object_id = ' . (int)TABLE_OBJECT_ID . '
		WHERE i.document_id = ' . (int)$_REQUEST['document_id'];
	$lCon->Execute($lSql);
// 	var_dump($lSql);
	$lResult['instance_id'] = (int)$lCon->mRs['id'];
}

displayAjaxResponse($lResult);
?>