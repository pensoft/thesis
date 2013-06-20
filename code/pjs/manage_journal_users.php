<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cManage_Journal_Users_Controller();
echo $lController->Display();
?>