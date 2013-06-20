<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

HtmlStart();


$t = array(
	'guid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'primarysite'	=>	array (
		'VType' => 'int',
		'CType' => 'hidden',
		'AllowNulls' => true,
		'DefValue' => 1,
	), 
	
	'title' => array(
		'VType' => 'string',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'new' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM GetStoriesBaseData({guid}, ' . getlang() . ')',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'svelements' => array(
		'CType' => 'action',
		'DisplayName' => 'Редактиране на статията',
		'SQL' => '',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './edit.php?guid={guid}&tAction=showedit',
		'AddTags' => array(
			'class' => 'frmbutton',
			'style' => 'width: 160px;',
		), 
	),
	
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => 'Назад',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	)	
);

$h = '
<h3>Елементи свързани със "{#title}"</h3>
<p>{title}{guid}{primarysite}{svelements} {cancel}</p>
';

$kfor = new kfor($t, $h, 'POST');
$kfor->debug = false;
echo $kfor->Display();

$guid = (int)$kfor->lFieldArr['guid']['CurValue'];
$lSiteRights = GetSiteRights();
if ($lSiteRights[(int)$kfor->lFieldArr['primarysite']['CurValue']] == 'edit' && $guid) 
	showRelatedItems($guid, (int)$kfor->lFieldArr['primarysite']['CurValue']);

HtmlEnd();
?>