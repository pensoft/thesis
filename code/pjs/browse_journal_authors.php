<?php
$docroot = getenv('DOCUMENT_ROOT');
$gDontRedirectToLogin = 1;
require_once($docroot . '/lib/static.php');

$lController = new cBrowse_Journal_Authors_Controller();
echo $lController->Display();
?>