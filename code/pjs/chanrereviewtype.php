<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cChange_Review_Type_Controller();
echo $lController->Display();
?>