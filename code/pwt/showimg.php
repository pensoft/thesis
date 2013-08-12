<?php
$gDontRedirectToLogin = 1;
require_once(getenv("DOCUMENT_ROOT") . "/lib/static.php");

$f = basename(s($_GET['filename']));

$i = new cgetimage(array(
	'fname' => $f,
	'photospath' => PATH_PWT_DL,
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
		'c67x70y' => array(
			'-thumbnail ' . escapeshellarg('67x70^').' -crop ' . escapeshellarg('67x70+0+0!')
		),
		'c30x30y' => array(
			'-thumbnail ' . escapeshellarg('30x30^').' -crop ' . escapeshellarg('30x30+0+0!')
		),
		'c27x27y' => array(
				'-thumbnail ' . escapeshellarg('27x27^').' -crop ' . escapeshellarg('27x27+0+0!')
		),
	),
));

$i->Display();

?>