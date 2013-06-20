<?php
//$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cCreate_Pwt_Document();
echo $lController->Display();
?>