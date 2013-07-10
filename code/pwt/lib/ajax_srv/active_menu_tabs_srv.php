<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$lTabId    = (int)$_REQUEST['tab_id'];
$lInstanceId = $lTabId;
$lIsActive = (int)$_REQUEST['is_active'];

if(!isset($_SESSION['activemenutabids']))
	$_SESSION['activemenutabids'] = array();

if($lIsActive){
	//Activate all the parents
	$lSql = '
		SELECT i.id 
		FROM pwt.document_object_instances i
		JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND i.pos ILIKE p.pos || '%' AND p.id <> i.id
		WHERE i.id = ' . $lInstanceId;
	$lCon = new DBCn();
	$lCon->Open();
	$lCon->Execute($lSql);
	while(!$lCon->Eof()){
		$lParentId = (int)$lCon->mRs['id'];
		$_SESSION['activemenutabids'][$lParentId] = $lParentId;
		$lCon->MoveNext();		
	}
	
	
	$_SESSION['activemenutabids'][$lTabId] = $lTabId;
}else {
	if(array_key_exists($lTabId, $_SESSION['activemenutabids']))
		unset($_SESSION['activemenutabids'][$lTabId]);
}
//displayAjaxResponse($_SESSION['activemenutabids']);
?>