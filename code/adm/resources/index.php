<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
HtmlStart();
	header('Location: /resources/articles/');
	exit;
HtmlEnd();
?>