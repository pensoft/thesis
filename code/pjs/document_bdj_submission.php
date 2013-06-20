<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cDocument_BDJ_Submission();
echo $lController->Display();
?>