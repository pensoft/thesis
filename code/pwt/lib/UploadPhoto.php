<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
session_write_close();

$timelimit = (int)UPLOAD_TIME_LIMIT;
if (!$timelimit) 
	$timelimit = 200;
set_time_limit($timelimit);

$lDocumentId = (int)$_REQUEST['document_id'];
$lPlateVal = (int)$_REQUEST['plateval'];
$lPlateId = (int)$_REQUEST['plateid'];
$lTitle = $_REQUEST['title'];
$lDesc = $_REQUEST['description'];
$lPref = $_REQUEST['image_pref'];
$lPhotoId = (int)$_REQUEST['photo_id'];
$lPosition = (int)$_REQUEST['position'];

if(isset($_FILES['uploadfile'])) {
	$lResult = array(
		'err_cnt' => 0,
		'err_msg' => '',
		'html' => '',
		'pic_id' => '',
		'plate_id' => '',
		'img_dims' => '',
	);
	
	if($lPhotoId)
		$lData = UploadFigurePic('uploadfile', PATH_PWT_DL, $lPhotoId, $lDocumentId, (int)$lPlateVal, (int)$lPlateId, $lTitle, $lDesc, $lPosition, $lError);
	else
		$lData = UploadFigurePic('uploadfile', PATH_PWT_DL, 0, $lDocumentId, (int)$lPlateVal, (int)$lPlateId, $lTitle, $lDesc, $lPosition, $lError);
	
	if ($lData['photo_id']) {
		$lResult['pic_id'] = $lData['photo_id'];
		if ($lPref) {
			$lResult['html'] = $lPref . '_' . $lData['photo_id'];
			$lResult['plate_id'] = (int)$lData['plate_id'];
			displayAjaxResponse($lResult);
		} else  {
			$lResult['html'] = 'big_' . $lData['photo_id'];
			$lResult['plate_id'] = (int)$lData['plate_id'];
			$lResult['img_dims'] = getImageDimensions(PATH_PWT_DL . 'big_' . $lData['photo_id'] . '.jpg');
			displayAjaxResponse($lResult);
		}
	} elseif ($lData['error_msg']) {
		$lResult['err_cnt'] = 1;
		$lResult['err_msg'] = $lData['error_msg'];
		displayAjaxResponse($lResult);
	}

}
else {
	$lResult['err_cnt'] = 1;
	$lResult['err_msg'] = getstr('admin.articles.error_picTooBigMaxSize') . (MAX_FIGURE_PIC_FILE_SIZE / (1024 * 1024)) . ' MB';
	displayAjaxResponse($lResult);
}
?>
