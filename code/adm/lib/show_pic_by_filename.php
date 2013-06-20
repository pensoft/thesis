<?php

require_once(getenv("DOCUMENT_ROOT") . "/lib/static.php");

$lFileName = basename(s($_GET['filename']));

$lCon = Con();
$lSql = 'SELECT guid FROM photos WHERE filenameupl ILIKE \'' . q($lFileName) . '\' LIMIT 1 ';
$lCon->Execute($lSql);
$lCon->MoveFirst();
$lGuid = (int) $lCon->mRs['guid'];
if( !(int) $lGuid){
	echo 'No such photo';
	exit;
}

$i = new cgetimage(array(
	'fname' => PHOTO_DISPLAY_PREFIX . $lGuid . '.jpg',
	'fpref_commands' => array(
		'oo' => '-quality 80',
		'big' => '-quality 80 -thumbnail ' . escapeshellarg('1024x1024>'), // Tazi se polzva za osnova
		'gb' => '-quality 60 -thumbnail ' . escapeshellarg('450x450>'),
		's' => array('-thumbnail 100', '-thumbnail 80'),
		'mx50' => '-thumbnail \'50\'',
		'm80' => '-thumbnail \'x160\' -thumbnail \'160x<\' -resize 50% -gravity center -crop 80x80+0+0',
		'd200x150' => '-thumbnail ' . escapeshellarg('200x150>'),
	),
	'imageextensions' => $gImageExtensions,
));

$i->Display();

?>