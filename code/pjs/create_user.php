<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cCreate_User_Controller();
echo $lController->Display();
?>