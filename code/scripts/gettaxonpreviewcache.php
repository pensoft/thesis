<?php
$gDontRedirectToLogin = true;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 'On');
ini_set('log_errors', 'On');

$lArticleId = (int)$argv[1];
$lPreview = new ctaxon_cache_generator(array(
	'article_id' => $lArticleId,
));
$lPreview->GetData();
var_dump($lPreview->m_errCnt, $lPreview->m_errMsg);

?>