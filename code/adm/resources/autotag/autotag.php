<?php
error_reporting(0);
set_time_limit(60*20);//Za da ne timeoutne - 2 min
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$lXml = s($_POST['xml']);
$lArticleId = (int) $_REQUEST['id'];
$lRuleID = (int) $_REQUEST['rule_id'];
$lPreIndex = (int) $_REQUEST['preindex'];
$lGetArticleContent = (int) $_REQUEST['get_article_content'];
$lAutotagType = (int) $_REQUEST['autotag_type'];

switch( $lAutotagType ){
	default:
	case (int) INTERNAL_AUTOTAG_TYPE:{
		$gAutotag = new cautotag($lArticleId, $lRuleID, $lXml, $lGetArticleContent, $lPreIndex);
		break;
	};
	case (int) UBIO_AUTOTAG_TYPE:{
		$gAutotag = new cubio_autotag($lArticleId, $lRuleID, $lXml, $lGetArticleContent, $lPreIndex);
		break;
	};
}

$gAutotag->Display();
exit;

?>