<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');


//~ $lController = new cDocument_Creation_Permissions();
$lController = new cManage_Submit_Journal_Documents_Controller();
echo $lController->Display();
?>