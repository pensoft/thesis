<?php
$docroot = getenv('DOCUMENT_ROOT');
//$gDontRedirectToLogin = 1;
require_once($docroot . '/lib/static.php');

$lController = new cArticles();
echo $lController->Display();
?>