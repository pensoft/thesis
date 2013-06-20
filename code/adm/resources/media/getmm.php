<?php

$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$fname = trim($_GET['filename']);
preg_match('/_(\d+)\./', $fname, $m);
$guid = $m[1];

$att = new cgetatt(
	array (
		'fileid' => $guid,
		'templs' => array (
			G_ENTERCODE => 'story.attach_entercode',
			G_WRONGCODE => 'story.attach_wrongcode',
			G_ABONACCESS => 'story.attach_abonaccess',
		),
	)	
);

$att->Display();

?>