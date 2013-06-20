<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cConfirm_Email_Controller();
echo $lController->Display();
?>