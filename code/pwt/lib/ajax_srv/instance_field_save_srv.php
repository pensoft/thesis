<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'on');

$gDocumentId = (int)$_REQUEST['document_id'];
$gInstanceId = (int)$_REQUEST['instance_id'];
$gFieldId = (int)$_REQUEST['field_id'];
$gValue = $_REQUEST['field_value'];

saveInstanceFieldValue($gInstanceId, $gFieldId, $gValue);

?>