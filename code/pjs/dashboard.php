<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$lController = new cDashboard();
echo $lController->Display();
?>