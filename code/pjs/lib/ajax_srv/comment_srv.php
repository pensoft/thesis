<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cComment_Ajax_Srv();
echo $lController->Display();
?>