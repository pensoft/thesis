<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$url = $_REQUEST['url'];
header('Location:' . $url . 'send/');
?>