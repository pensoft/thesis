<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_ACTIVE;

HtmlStart();

$t = array(
	'langid' => array(
		'CType' => 'text',
		'VType' => 'int',
		'DisplayName' => 'ID',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => false,
	),
	
	'code' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Код',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => false,
	),
	
	'name' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Име',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => false,
	),
	
	'show' => array(
		'CType' => 'action',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM langs(0, null, {code}, null)',
		'Hidden' => true,
	),
	
	'save' => array(
		'CType' => 'action',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM langs(1, {langid}, {code}, {name})',
		'DisplayName' => 'Запази',
		'AddTags' => array (
		'class' => 'frmbutton'
		)
	),
	
	'delete' => array (
		'CType' => 'action',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM langs(3, null, {code}, null)',
		'DisplayName' => 'Изтрий',
		'AddTags' => array (
			'onclick' => 'javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете този език?\')) { return true; } else { return false;}',
			'class' => 'frmbutton'
		)
	),
	
	'back' => array (
		'CType' => 'action',
		'ActionMask' => ACTION_REDIRECT,
		'DisplayName' => 'Назад',
		'AddTags' => array (
			'class' => 'frmbutton'
		)
	),
);

$kfor = new kfor($t);
$kfor->ExecAction();

$h = '
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="5" border="0" class="formtable">
			<tr>
				<th colspan="2">' . ($kfor->lFieldArr['code']['CurValue'] ? 'Редактиране' : 'Добавяне') . ' на език</th>
			</tr>
			<tr>
			<tr>
				<td><b>{*langid}:</b></td>
				<td>{langid}</td>
			</tr>
			<tr>
				<td><b>{*code}:</b></td>
				<td>{code}</td>
			</tr>
			<tr>
				<td><b>{*name}:</b></td>
				<td>{name}</td>
			</tr>
			<tr>
				<td colspan="2" align="right">{show}{save} {delete} {back}</td>
			</tr>
			</table>
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>
	</div>
	</div>
';

$kfor->SetFormHtml($h);
echo $kfor->Display();

HtmlEnd();

?>