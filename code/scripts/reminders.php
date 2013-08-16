<?php
//ini_set('display_errors', true);
//error_reporting(E_ALL);
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
//$docroot = '/var/www/pensoft/victorp.pmt/code/pjs/';
require_once ($docroot . 'lib/static.php');

$lController = new cReminders_Manager_Controller(array());
echo $lController->Display();

?>