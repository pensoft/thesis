<?php
require_once(getenv("DOCUMENT_ROOT") . "/lib/static.php");
ProccessHistory();
UserRedir($user);
	
$lCon = Con();
$lSql = 'SELECT *, default_allow_nulls::int as int_default_allow_nulls FROM fields WHERE id = ' . (int)$_REQUEST['field_id'];
$lResult = array();

$lCon->Execute($lSql);
$lCon->MoveFirst();
$lResult['default_label'] = $lCon->mRs['default_label'];
$lResult['default_control_type'] = (int)$lCon->mRs['default_control_type'];
$lResult['default_allow_nulls'] = (int)$lCon->mRs['int_default_allow_nulls'];
echo json_encode($lResult);
exit;

?>