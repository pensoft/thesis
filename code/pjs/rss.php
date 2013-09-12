<?php
$docroot = getenv('DOCUMENT_ROOT');
$gDontRedirectToLogin = 1;
require_once($docroot . '/lib/static.php');

$lController = new cRSS_Controller();
echo $lController->Display();
?>