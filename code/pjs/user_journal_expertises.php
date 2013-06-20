<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cUser_Journal_Expertises_Controller();
echo $lController->Display();
?>