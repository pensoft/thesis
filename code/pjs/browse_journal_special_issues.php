<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cBrowse_Journal_Special_Issues_Controller();
echo $lController->Display();
?>