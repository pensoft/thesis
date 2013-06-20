<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cBrowse_Journal_Issue_Documents_Controller();
echo $lController->Display();
?>