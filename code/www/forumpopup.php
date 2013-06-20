<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
$t = array (
	'forumpop' => array (
		'ctype' => 'cforum',
		'showtype' => 4,
		'openforum' => true,
		'dsggroup' =>  (int)$_GET['dsgid'],
		'msgid' =>  (int)$_GET['msgid'],
		'templs' => array(
			G_ROWTEMPL => 'forum.popup_row',
		),
	),
);
$inst = new cpage($t, array(G_MAINBODY => 'global.forumpopup'));
$inst->Display();
	
?>