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
		's' => array(
			'-thumbnail', 
			'100', 
			'-thumbnail', 
			'80'
		),
		'mx50' => array(
			'-thumbnail', 
			'50',
		),
		'm80' => array(
			'-thumbnail', 
			'x160',
			'-thumbnail', 
			'x160<',
			'resize',
			'50%',
			'-gravity',
			'center',
			'crop',
			'80x80+0+0',
		),//'-thumbnail \'x160\' -thumbnail \'160x<\' -resize 50% -gravity center -crop 80x80+0+0',
		'd200x150' => array(
			'-thumbnail', 
			escapeshellarg('200x150>'),
		),//'-thumbnail ' . escapeshellarg('200x150>'),
	),
	'imageextensions' => $gImageExtensions,
));

$i->Display();

?>