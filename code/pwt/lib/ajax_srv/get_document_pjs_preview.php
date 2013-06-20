<?php
$docroot = getenv('DOCUMENT_ROOT');

$gDontRedirectToLogin = 1;
require_once($docroot . '/lib/static.php');


$gDocumentId = (int)$_REQUEST['document_id'];
$gXml = $_REQUEST['xml'];
$gReadOnlyPreview = (int)$_REQUEST['readonly_preview'];

$lSql = 'SELECT xsl_dir_name
FROM pwt.templates t
JOIN pwt.documents d ON d.template_id = t.id
WHERE d.id =  ' . (int)$gDocumentId;

$lCon = new DBCn();
$lCon->Open();
$lCon->Execute($lSql);
$lXSLPath = $lCon->mRs['xsl_dir_name'];

$lResult = array(
	'err_cnt' => 0,
	'err_msgs' => array(),
	'read_only' => $gReadOnlyPreview,
);
if($gDocumentId){
	header("Content-type: text/html");
// 	var_dump($gXml);
	$lMarkContentEditableFields = $gReadOnlyPreview ? 0 : 1;
	$lResult['preview'] = getDocumentPreview($gDocumentId, 0, $lXSLPath, $gXml, $lMarkContentEditableFields, false, false, false);

}else{
	$lResult['err_cnt']++;
	$lResult['err_msgs'][] = getstr('pjs.noDocumentIdSupplied');

}

displayAjaxResponse($lResult);
?>