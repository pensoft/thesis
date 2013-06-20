<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cBrowse_Articles_Controller();
echo $lController->Display();
?>