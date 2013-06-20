<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
//~ var_dump($_POST);
//~ exit;

$url = $_REQUEST['url'];
if (!$url) $url = '/';

if ((int)$_REQUEST['logout']) {
	unset($_SESSION['suser']);
	header('Location: ' . $url);
	exit;
}

$usrname = $_REQUEST['uname'];
$passwd = $_REQUEST['upass'];

if ($user->id || !($usrname && $passwd)) {
	header('Location: ' . $url);
	exit;
}

$user = new clogin($usrname, $passwd, $_SERVER['REMOTE_ADDR']);
$_SESSION['suser'] = serialize($user);
header('Location: ' . $url);
exit;

?>