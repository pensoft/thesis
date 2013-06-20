<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cDocument_Edit_Controller();
echo $lController->Display();
?>