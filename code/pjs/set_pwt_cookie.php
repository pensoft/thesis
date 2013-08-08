<?php
$docroot = getenv('DOCUMENT_ROOT');
$gDontRedirectToLogin = 1;
require_once($docroot . '/lib/static.php');

global $rewrite;


$lController = new cSetcookie_Controller();
echo $lController->Display();

?>