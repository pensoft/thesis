<?php

$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_ACTIVE;

HtmlStart();

$t = array(
	'id' => array (
		'CType' => 'hidden',
		'VType' => 'int',
		"DisplayName" => "",
		'AllowNulls' => true,
	),
	
	'word_bg' => array(
		'CType' => 'text',
		'VType' => 'string',
		'AddTags' => array(
			'style' => 'width: 99%',
		),
		"DisplayName" => "Дума (bg)",
		'AllowNulls' => false,
	),
	
	'word_en' => array(
		'CType' => 'text',
		'VType' => 'string',
		'AddTags' => array(
			'style' => 'width: 99%',
		),
		"DisplayName" => "Дума (en)",
		'AllowNulls' => false,
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => '',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM transliteration_words WHERE id = {id}',
	),
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM SaveTransLiterationWords(1, {id}, {word_bg}, {word_en})',
		'RedirUrl' => '',
		"AddTags" => array(
			"class" => "frmbutton",
		),
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM SaveTransLiterationWords(2, {id}, null, null)',
		'RedirUrl' => '',
		"AddTags" => array(
			"class" => "frmbutton",
			"onclick" => "javascript: if (confirm('Сигурни ли сте, че искате да изтриете тази дума?')) { return true; } else { return false;}",
		),		
	),
	
	"cancel" => array(
		'CType' => 'action',
		'DisplayName' => 'Назад',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '',
		"AddTags" => array(
			"class" => "frmbutton",
		),		
	),
);

$f = new kfor($t);
$f->ExecAction();
$html = '{id}
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
				<col width="25%"></col>
				<col width="75%"></col>
			</colgroup>
			<tr>
				<th colspan="2">' . ( $f->lFieldArr["id"]["CurValue"] ? 'Редактиране' : 'Добавяне' ) . ' на дума</th>
			</tr>
			<tr>
				<td><b>{*word_bg}:</b></td>
				<td>{word_bg}</td>
			</tr>
			<tr>
				<td><b>{*word_en}:</b></td>
				<td>{word_en}</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2" align="right">{show} {save} ' . ((int)$f->lFieldArr['id']['CurValue'] ? '{delete}' : '') . ' {cancel}</td></tr>
		</table>
	</div>
	</div>
	</div>
	</div>
</div>
</div>
</div>
</div>';
$f->lFormHtml = $html;

echo $f->Display();
HtmlEnd();

?>