<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
if(is_file($docroot . $_REQUEST['path_fields'])) {
	require_once($docroot . $_REQUEST['path_fields']);
} else {
	echo 'error loading form fields';
	exit;
}

global $gFormFields;
$lField = $_REQUEST['field'];
$lFieldKey = $_REQUEST['field_key'];

if(!$lField || !$lFieldKey)
	exit;

$flds = $gFormFields[$lFieldKey];

$lkfor = new kfor($flds, '', '', NULL, 1, NULL, 1);
$lResult = $lkfor->CheckField($lField);
echo json_encode($lResult);

?>