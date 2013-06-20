<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
session_write_close();

$timelimit = (int)UPLOAD_TIME_LIMIT;
if (!$timelimit)
	$timelimit = 200;
set_time_limit($timelimit);

$gDocumentId = (int)$_REQUEST['document_id'];
checkIfDocumentIsLockedByTheCurrentUserForAjax(0, $gDocumentId);

if(isset($_FILES['uploadfile'])) {
	$lResult = array(
		'err_cnt' => 0,
		'err_msg' => '',
		'html' => '',
		'file_id' => '',
		'file_name' => '',
	);

	$lData = UploadFile('uploadfile', PATH_PWT_DL, $gDocumentId);
	$lResult['file_id'] = $lData['file_id'];
	$lResult['file_name'] = $lData['file_name'];
	displayAjaxResponse($lResult);

	//~ trigger_error($lData['file_id'], E_USER_NOTICE);
	//~ trigger_error($lData['file_name'], E_USER_NOTICE);
}

?>
