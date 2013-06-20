<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$lShowOrHide    = (int)$_REQUEST['show_or_hide'];
$lLeftOrRight   = (int)$_REQUEST['left_or_right'];

if(!isset($_SESSION['columnsstate']))
	$_SESSION['columnsstate'] = array();

$_SESSION['columnsstate'][$lLeftOrRight] = $lShowOrHide;

//displayAjaxResponse($_SESSION['columnsstate']);
?>