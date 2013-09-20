<?php
$gDontRedirectToLogin = true;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cArticle_Comments_Form_Controller();
echo $lController->Display();
?>