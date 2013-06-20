<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . 'lib/static.php');

$lController = new cEmail_Messanger_Controller();
$lController->Display();
?>