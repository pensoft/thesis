<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . 'lib/static.php');

$lController = new cSubmission_Updater_Controller($argv[1]);
$lController->Display();

?>