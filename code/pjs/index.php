<?php
$docroot = getenv('DOCUMENT_ROOT');
$gDontRedirectToLogin = 1;
require_once($docroot . '/lib/static.php');

header('Location:/journals.php?journal_id=1');
exit();
$lController = new cIndex_Controller();
echo $lController->Display();
?>