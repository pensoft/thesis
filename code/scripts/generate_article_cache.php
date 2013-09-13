<?php
$gDontRedirectToLogin = true;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 'On');
ini_set('log_errors', 'On');

$lArticleId = (int)$argv[1];
$lArticleIds = array();
if(!(int)$lArticleId){
	$lCon = new DBCn();
	$lCon->Open();
	
	$lSql = '
		SELECT id
		FROM pjs.articles		
	';
	$lCon->Execute($lSql);
	while(!$lCon->Eof()){
		$lArticleIds[] = $lCon->mRs['id'];
		$lCon->MoveNext();
	}
}else{
	$lArticleIds[] = $lArticleId;
}

foreach ($lArticleIds as $lCurrentArticleId) {
	echo 'Article ' . $lCurrentArticleId . " cache generation start \n";
	$lPreviewGenerator = new carticle_preview_generator(array(	
		'document_id' => $lCurrentArticleId,
	));
	$lPreviewGenerator->GetData();
	var_dump($lPreviewGenerator->m_errCnt, $lPreviewGenerator->m_errMsg);
	echo 'Article ' . $lCurrentArticleId . " cache generation end \n";
}



?>