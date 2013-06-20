<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . 'lib/static.php');

$lController = new cReminders_Manager_Controller();
echo $lController->Display();

?>