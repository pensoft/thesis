<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cTasksPopUp_Controller();
echo $lController->Display();
?>