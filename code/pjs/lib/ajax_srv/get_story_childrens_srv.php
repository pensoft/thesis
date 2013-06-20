<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cGet_Story_Childrens_Ajax_Srv();
echo $lController->Display();
?>