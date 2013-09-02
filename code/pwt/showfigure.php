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
		'singlefig' => array(
			'-quality', 
			'90',
			'-thumbnail',
			escapeshellarg('663x663>'),
		),
		'twocolumn' => array(
			'-quality', 
			'90',
			'-thumbnail',
			escapeshellarg('296x296>'),
		),
		'singlefigmini' => array(
			'-quality', 
			'90',
			'-thumbnail',
			escapeshellarg('96x80>'),
		),
		'twocolumnmini' => array(
			'-quality', 
			'90',
			'-thumbnail',
			escapeshellarg('45x45>'),
		),
		'plateportraitmini' => array(
			'-quality', 
			'90',
			'-thumbnail',
			escapeshellarg('45x90>'),
		),
		'singlefigAOF' => array(
			'-quality',
			'90', 
			'-thumbnail',
			escapeshellarg('384x400>'),
		),
		'gb' => array(
			'-quality',
			'90', 
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
		'c288x206y' => array(
			'-thumbnail ' . escapeshellarg('288x206^').' -crop ' . escapeshellarg('288x206+0+0!')
		),
		'c288x410y' => array(
			'-thumbnail ' . escapeshellarg('288x410^').' -crop ' . escapeshellarg('288x410+0+0!')
		),
		'c90x82y' => array(
			'-thumbnail ' . escapeshellarg('90x82^').' -crop ' . escapeshellarg('90x82+0+0!')
		),
		'c90x41y' => array(
			'-thumbnail ' . escapeshellarg('90x41^').' -crop ' . escapeshellarg('90x41+0+0!')
		),
		'c45x82y' => array(
			'-thumbnail ' . escapeshellarg('45x82^').' -crop ' . escapeshellarg('45x82+0+0!')
		),
		'c45x41y' => array(
			'-thumbnail ' . escapeshellarg('45x41^').' -crop ' . escapeshellarg('45x41+0+0!')
		),
		'c45x27y' => array(
			'-thumbnail ' . escapeshellarg('45x27^').' -crop ' . escapeshellarg('45x27+0+0!')
		),
	),
));
$i->Display($_GET['download'] == '1' ? 'attachment' : 'inline');

?>