<?php
$docroot = getenv('DOCUMENT_ROOT');
$gDontRedirectToLogin = 1;
require_once($docroot . '/lib/static.php');
//echo '<pre>' . getDocumentPreview(162) . '</pre>';
//updateDocumentPreviewCash($lCon->mRs['id']);

$lSql = '	SELECT d.id, t.xsl_dir_name
			FROM pwt.documents d
			JOIN pwt.templates t ON t.id = d.template_id
			WHERE d.generated_doc_html = 0 and d.doc_xml is not null';

$lCon = new DBCn;
$lCon->Open();
$lCon->Execute($lSql);
$lCon->MoveFirst();

//var_dump($lCon);

while(!$lCon->Eof()){
	updateDocumentPreviewCash( (int)$lCon->mRs['id'],  $lCon->mRs['xsl_dir_name']);
	$lCon->MoveNext();
}
?>