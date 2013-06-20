<?php
//~ $docroot = getenv('DOCUMENT_ROOT');
$docroot = '/var/www/pensoft/nedko.pmt/code/pwt/';
require_once($docroot . '/lib/static.php');

//echo '<pre>' . getDocumentPreview(162) . '</pre>';
//updateDocumentPreviewCash($lCon->mRs['id']);

$lSql = 'SELECT id FROM pwt.documents WHERE generated_doc_html = 0 and doc_xml is not null';

$lCon = new DBCn;
$lCon->Open();
$lCon->Execute($lSql);
$lCon->MoveFirst();

//var_dump($lCon);

while(!$lCon->Eof()){
	updateDocumentPreviewCash( (int)$lCon->mRs['id'] );
	$lCon->MoveNext();
}
?>