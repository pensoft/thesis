<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
global $rewrite;

$lController = new cEmail_Notifications_Controller();
echo $lController->Display();
?>