<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$lTabId    = (int)$_REQUEST['tab_id'];
$lInstanceId = $lTabId;
$lIsActive = (int)$_REQUEST['is_active'];

if($lIsActive){
	MarkActiveTab($lTabId);	
}else {
	MarkInactiveTab($lTabId);
}
//displayAjaxResponse($_SESSION['activemenutabids']);
?>