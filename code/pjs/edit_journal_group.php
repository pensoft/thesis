<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
global $rewrite;

$lController = new cEdit_Journal_Group_Controller();
echo $lController->Display();
?>