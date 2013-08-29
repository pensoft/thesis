<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
error_reporting((int)ERROR_REPORTING);

$gUrl =  trim(rawurldecode($_REQUEST['url']));
echo executeExternalQuery($gUrl);

?>