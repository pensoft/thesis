<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$fname = trim($_GET['filename']);
preg_match('/_(\d+)\./', $fname, $m);
$guid = $m[1];

$att = new cgetfile(
	array (
		'fileid' => $guid,
		'templs' => array (
			G_ENTERCODE => 'global.empty',
			G_WRONGCODE => 'global.empty',
			G_ABONACCESS => 'global.empty',
		),
	)	
);

$att->Display();

?>