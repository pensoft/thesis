<?php
$gDontRedirectToLogin = true;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 'On');
ini_set('log_errors', 'On');

$lArticleId = (int)$argv[1];

if(!(int)$lArticleId){
	echo 'No article id specified';
	exit;
}

$lPreviewGenerator = new carticle_preview_generator(array(	
	'document_id' => $lArticleId,
));

$lPreviewGenerator->GetData();
var_dump($lPreviewGenerator->m_errCnt, $lPreviewGenerator->m_errMsg);

?>