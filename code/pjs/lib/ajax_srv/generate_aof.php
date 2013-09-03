<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'on');

$lArticleId = (int)$_REQUEST['document_id'];
	
$lQueryResult = executeExternalQuery(PWT_AOF_CACHE_URL, array('document_id' => $lArticleId), '', 300);
//$lQueryResult = json_decode($lQueryResult, true);
//var_dump($lQueryResult);
if(!$lQueryResult || $lQueryResult != 'ok'){
	echo 'err';
} else {
	echo 'ok';
}
exit;
?>