<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lController = new cArticle_Preview();
echo $lController->Display();
?>