<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
session_write_close();

ini_set('display_errors', 'off');

$timelimit = (int)UPLOAD_TIME_LIMIT;
if (!$timelimit)
	$timelimit = 200;
set_time_limit($timelimit);

//~ $gDocumentId = 1573;
$gDocumentId = (int)$_REQUEST['document_id'];
$gInstanceId = (int)$_REQUEST['instance_id'];
$gTemplateId = 2;
//~ $gInstanceId = 89403;
//~ $gInstanceId = 89369;

if(isset($_FILES['uploadfile']) && (int)$gDocumentId && $gInstanceId) {
	$lResult = array(
		'err_cnt' => 0,
		'err_msg' => '',
		'html' => '',
		'file_path' => '',
	);

	$lData = CreateNewTTMaterialFromSpreadSheet('uploadfile', PATH_PWT_UPLOADED_FILES, $gDocumentId);
	$lResult['file_path'] = PATH_PWT_UPLOADED_FILES . $lData['file_name'];

	if($lResult['file_path'] && !$lResult['err'] ) {
		$lImportMaterial = new cimport_materials(
			array(
				'document_id' => $gDocumentId,
				'template_id' => $gTemplateId,
				'instance_id' => $gInstanceId,
				'file_path' => $lResult['file_path'],

			)
		);

		echo $lImportMaterial->Display();
	}
}

exit;

?>
