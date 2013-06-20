<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');
ini_set('display_errors', 'off');
session_write_close();

$gAction = $_REQUEST['action'];
$gDocumentId = (int) $_REQUEST['document_id'];
$gInstanceId = (int) $_REQUEST['instance_id'];
$pFieldId = (int) $_REQUEST['field_id'];
$pValue = $_REQUEST['field_value'];
$pFieldType = $_REQUEST['field_type'];
$gRootInstanceId = (int) $_REQUEST['root_instance_id'];

$lResult = array(
	'err_msg' => '',
	'html' => ''
);

switch ($gAction) {
	default :
	case 'update_instance_field_value' :
		checkIfDocumentIsLockedByTheCurrentUserForAjax($gInstanceId, 0);
		$lResult['html'] = updateInstanceFieldValue($gInstanceId, $pFieldId, $pValue, $pFieldType);
		displayAjaxResponse($lResult);
		break;
}

?>