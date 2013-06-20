<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cInvite_Reviewers_Ajax_Srv();
echo $lController->Display();
?>