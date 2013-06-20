<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

$lHide = 0;

HtmlStart($lHide);

$t = array(
	'id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),

	'title' => array(
		'CType' => 'text',
		'VType' => 'string',
		'AddTags' => array(
			'class' => 'coolinp',
			'style' => 'width:100%',
		),
		'DisplayName' => 'Заглавие',
	),
	
	'description' => array(
		'CType' => 'text',
		'VType' => 'string',
		'AddTags' => array(
			'class' => 'coolinp',
			'style' => 'width:100%',
		),
		'DisplayName' => 'Описание',
	),
	
	'keywords' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'AddTags' => array(
			'class' => 'coolinp',
			'rows' => '3',
		),
		'DisplayName' => 'Ключови думи',
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spMetadata(0,{id}, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'SQL' => 'SELECT * FROM spMetadata(1,{id}, {title}, {description}, {keywords})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './',
		'AddTags' => array(
			'class' => 'frmbutton',
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'SQL' => 'SELECT * FROM spMetadata(3,{id}, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => './',
		'AddTags' => array(
			'class' => 'frmbutton',
			'onclick' => 'javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тези метаданни?\')) { return true; } else { return false;}',
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

$kfor = new kfor($t, $h, 'POST');
$kfor->debug = false;

$kfor->ExecAction();

$html = '
{id}
<div class="t">
<div class="b">
<div class="l">
<div class="r">
	<div class="bl">
	<div class="br">
	<div class="tl">
	<div class="tr">
		<table cellspacing="0" cellpadding="5" border="0" class="formtable">
		<colgroup>
			<col width="25%" />
			<col width="75%" />
		</colgroup>
		<tr>
			<th colspan="2">' . ((int)$kfor->lFieldArr['id']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на метаданни</th>
		</tr>
		<tr>
			<td><b>{*title}:</b></td>
			<td>{title}</td>
		</tr>
		<tr>
			<td><b>{*description}:</b></td>
			<td>{description}</td>
		</tr>
		<tr>
			<td><b>{*keywords}:</b></td>
			<td><b>{keywords}</td>
		</tr>
		<tr>
			<td colspan="2" align="right">{save}' . ((int)$kfor->lFieldArr['id']['CurValue'] ? ' {delete}' : '' ) . ' {cancel}</td>
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

$kfor->lFormHtml = $html;
echo $kfor->Display();

HtmlEnd($lHide);
?>