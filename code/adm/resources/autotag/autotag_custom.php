<?php
error_reporting(E_ALL);
set_time_limit(60 * 60 * 10);//Za da ne timeoutne - 2 min
ini_set('memory_limit', '500M');
$docroot = getenv("DOCUMENT_ROOT");
// $docroot = getenv("./../../");
require_once($docroot . '/lib/static.php');

$lCon = new DBCn();
$lCon->Open();
$lCon->Execute('SELECT * FROM article_autotag_temp WHERE id = 5;');
$lXml = $lCon->mRs['after_xml'];



// $lXml = s($_POST['xml']);
// $lXml = ($_REQUEST['xml']);
$lArticleId = 3183;
$lRuleID = 7;
$lPreIndex = 1;
$lGetArticleContent = 1;


$gAutotag = new cautotag($lArticleId, $lRuleID, $lXml, $lGetArticleContent, $lPreIndex);

$gAutotag->GetData();
$gAutotag->ProcessMatches();
exit;

?>