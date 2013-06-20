<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cManage_Journal_About_Pages_Controller();
echo $lController->Display();
?>