<?php
require_once(getenv("DOCUMENT_ROOT") . "/lib/static.php");
ProccessHistory();
UserRedir($user);

$lResult = array();
$lTemplateId = (int) $_REQUEST['template_id'];
$lIds = explode(';', $_REQUEST['ids']);
$lIds = array_map('parseToInt', $lIds);
if(!is_array($lIds) || !$lTemplateId){
	$lResult['msg'] = getstr('pwt_admin.templates.someRequiredParametersAreMissing');
}else{
	$lIds = implode($lIds, ',');


	$lCon = Con();
	$lSql = 'SELECT * FROM spSaveTemplateObjectsOrder(' . (int)$lTemplateId . ', ARRAY[' . $lIds . ']::bigint[], ' . (int)$user->id . ')';


	if(!$lCon->Execute($lSql)){
		$lResult['msg'] = getstr($lCon->GetLastError());
	}else{
		$lCon->MoveFirst();		
		$lResult['msg'] = getstr('pwt_admin.templates.objectsOrderSavedSuccessfully');
	}
}
echo json_encode($lResult);
exit;

?>