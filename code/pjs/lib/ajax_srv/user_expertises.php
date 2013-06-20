<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');


$lController = new cUser_Expertises_PopUp_Srv();
echo $lController->Display();
?>