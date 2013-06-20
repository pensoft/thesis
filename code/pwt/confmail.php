<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$hash = $_REQUEST['hash'];
$cn = Con();
if ($hash) {
	$cn->Execute('SELECT ConfMail(\'' . q($hash) . '\') as cm');
	$cn->MoveFirst();
	if (!(int)$cn->mRs['cm']) {
		header('Location: /');
		exit;
	} else {
		header('Location: /login.php?confirmed=1');
	}
} else {
	header('Location: /');
	exit;
}

//~ $inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.simplepage'));
//~ $inst->Display();

?>