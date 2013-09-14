<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cGenerate_XML_Controller();
echo $lController->Display();
?>