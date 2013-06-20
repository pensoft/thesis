<?php
require_once(getenv("DOCUMENT_ROOT") . '/lib/static.php');

$fname = trim($_GET['filename']);
preg_match('/_(\d+)\./', $fname, $m);
$guid = $m[1];

$att = new cgetatt(
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