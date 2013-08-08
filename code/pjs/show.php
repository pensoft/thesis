<?php
$docroot = getenv('DOCUMENT_ROOT');
$gDontRedirectToLogin = 1;
require_once($docroot . '/lib/static.php');

$lController = new cShow_Controller();
echo $lController->Display();
?>