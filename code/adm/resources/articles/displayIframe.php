<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
header('Location: ' . PTP_URL . '/displayIframe.php?' . $_SERVER['QUERY_STRING'] );
exit;
?>