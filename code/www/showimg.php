<?php

require_once(getenv("DOCUMENT_ROOT") . "/lib/static.php");

$f = basename(s($_GET['filename']));

$i = new cgetimage(array(
	'fname' => $f,
	'fpref_commands' => array(
		'oo' => array(
			'-quality', 
			'80',
		),
		'big' => array(
			'-quality', 
			'80',
			'-thumbnail',
			escapeshellarg('1024x1024>'), // Tazi se polzva za osnova
		),
		'gb' => array(
			'-quality',
			'60', 
			'-thumbnail',
			escapeshellarg('450x450>'),
		),
		'sg198' => array(
			'-thumbnail',
			'198',
		),//'-thumbnail 198',
	),
));

$i->Display();

?>