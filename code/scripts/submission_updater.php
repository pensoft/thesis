<?php
$docroot = getenv('DOCUMENT_ROOT');
$gDontRedirectToLogin = 1;
require_once ($docroot . 'lib/static.php');

$lController = new cSubmission_Updater_Controller($argv[1]);
$lController->Display();

?>