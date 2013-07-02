<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');
session_write_close();
ini_set('display_errors', 'off');

$gInstanceId = ( int ) $_REQUEST ['instance_id'];
$gDocumentId = ( int ) $_REQUEST ['document_id'];

$gDocument = new cdisplay_document(array (
		'ctype' => 'cdisplay_document',
		'instance_id' => $gInstanceId,
		'get_data_from_request' => false,
		'get_object_mode_from_request' => false,
		'lock_operation_code' => LOCK_AUTO_LOCK,
		'tree_templs' => getDocumentTreeDefaultTempls(),
		'dont_redir_to_view' => 1,
		'preview_mode' => 1 
));

$lTree = $gDocument->m_documentTree;

$lResult = array (
	'html' => $lTree->Display() 
);
displayAjaxResponse($lResult);
?>