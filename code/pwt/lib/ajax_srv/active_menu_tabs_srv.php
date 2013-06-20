<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$lTabId    = (int)$_REQUEST['tab_id'];
$lIsActive = (int)$_REQUEST['is_active'];

if(!isset($_SESSION['activemenutabids']))
	$_SESSION['activemenutabids'] = array();

if($lIsActive)
	$_SESSION['activemenutabids'][$lTabId] = $lTabId;
else {
	if(array_key_exists($lTabId, $_SESSION['activemenutabids']))
		unset($_SESSION['activemenutabids'][$lTabId]);
}
//displayAjaxResponse($_SESSION['activemenutabids']);
?>