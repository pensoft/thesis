<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$hash = $_GET['hash'];
$cn = Con();

if ($hash) {
	$cn->Execute('SELECT ConfMail(\'' . q($hash) . '\') as cm');
	$cn->MoveFirst();
	if (!(int)$cn->mRs['cm']) {
		header('Location: /');
		exit;
	}
} else {
	header('Location: /');
	exit;
}

$t = array (
	'metatitle' => 'Registration confirmation',
	
	'contents' => array(
		'ctype' => 'csimple',
		'msg' => getstr('regprof.registrationConfirmed'),
		'templs' => array(
			G_DEFAULT => 'global.system_msg',
		),
	),
);

$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.simplepage'));
$inst->Display();

?>