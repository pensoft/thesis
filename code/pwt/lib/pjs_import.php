<?php
$gDontRedirectToLogin = true;
ini_set('display_errors', 'off');

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lImporter = new cpjs_document_importer(array(
// 	'document_id' => 1652,
// 	'xml' => file_get_contents('./1652.xml'),
// 	'uid' => 8,
	'document_id' => $_REQUEST['document_id'],
	'xml' => $_REQUEST['xml'],
	'uid' => $_REQUEST['user_id'],
));
$lResult = array(
	'result' => $lImporter->ImportDocument(),
	'err_cnt' => $lImporter->GetErrCnt(),
	'err_msg' => $lImporter->GetErrMsg(),
);
displayAjaxResponse($lResult);
// var_dump($lImporter->ImportDocument());
// var_dump($lImporter->m_errMsg);

?>