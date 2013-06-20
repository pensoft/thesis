<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

global $rewrite;


$lController = new cLogin_Controller();
echo $lController->Display();

?>