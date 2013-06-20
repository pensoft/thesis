<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

global $user;

$lRedirUrl = $_GET['redirurl'];
$lLogout = (int)$_GET['logout'];
if(!(int)$lLogout) {
	$cn = Con();
	$cn->Execute('SELECT autolog_hash FROM usr WHERE id = ' . (int)$user->id);
	$cn->MoveFirst();
	$lAutoLogHash = $cn->mRs['autolog_hash'];

	if(!$lAutoLogHash) {
		header("Location: $lRedirUrl");
	}
}

if(!$lRedirUrl){
	$lRedirUrl = '/index.php';
}
$c = new csimple(array(
	'ctype' => 'csimple',
	'templs' => array( G_DEFAULT => 'registerfrm.setcookie' ),
	'hash' => $lAutoLogHash,
	'logout' => $lLogout,
	'redirurl' => $lRedirUrl,
));


$t = array(
	'contents' => $c,
);

$inst = new cpage($t, array(G_MAINBODY => 'global.setcookie'));
$inst->Display();

?>