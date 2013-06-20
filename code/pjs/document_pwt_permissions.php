<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');


$lController = new cDocument_Creation_Permissions();
echo $lController->Display();
?>