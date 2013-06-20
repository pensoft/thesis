<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cEdit_Journal_Issue_Controller();
echo $lController->Display();
?>