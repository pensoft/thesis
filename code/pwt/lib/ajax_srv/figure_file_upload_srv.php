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

	$lData = UploadFigurePhoto('uploadfile', PATH_PWT_DL, $gDocumentId);
	$lResult['file_id'] = $lData['file_id'];
	$lResult['pic_id'] = $lData['file_id'];
	$lResult['file_name'] = $lData['file_name'];
	$lResult['html'] = 'big_' . $lData['file_id'];	
	$lResult['plate_pic'] = 'c288x206y_' . $lData['file_id'];	
	$lResult['img_dims'] = getImageDimensions(PATH_PWT_DL . 'big_' . $lData['file_id'] . '.jpg');
	$lResult['err_msg'] = $lData['err_msg'];
	if($lResult['err_msg']){
		$lResult['err_cnt'] = 1;
	}
	displayAjaxResponse($lResult);

	//~ trigger_error($lData['file_id'], E_USER_NOTICE);
	//~ trigger_error($lData['file_name'], E_USER_NOTICE);
}

?>
