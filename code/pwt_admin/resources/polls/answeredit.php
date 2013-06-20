<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

HtmlStart();
$t = array(
	'id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'pollid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'ans' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.polls.colAnswer'),
	),
	
	'ord' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.polls.colOrder'),
	),
	
	'votes' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.polls.colAnswerVotes'),
	),
	
	'flags' => array (
		'VType' => 'int',
		'CType' => 'select',
		'DisplayName' => getstr('admin.polls.colActiveAnswer'),
		'AllowNulls' => false,
		'SrcValues' => array(
			0 => getstr('admin.no'),
			1 => getstr('admin.yes'),
		),
		'DefValue' => 1,
		
	), 
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM sp_poll_answer(0,{id}, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'SQL' => 'SELECT * FROM sp_poll_answer(1,{id}, {pollid}, {ans}, {flags}, {ord})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
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


$html = '

	{id}{pollid}
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
				<col width="25%" />
				<col width="25%" />
				<col width="25%" />
			</colgroup>
			<tr>
				<th colspan="4">' . ((int)$kfor->lFieldArr['id']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на отговор</th>
			</tr>
			<tr>
				<td colspan="2" valign="top"><b>{*ans}:</b><br/>{ans}</td>
				<td colspan valign="top"><b>{*ord}:</b><br/>{ord}</td>
				<td colspan valign="top"><b>{*flags}:</b><br/>{flags}</td>
			</tr>
			' .( (int)$kfor->lFieldArr['id']['CurValue'] ? '
				<tr>
					<td colspan valign="top"><b>{*votes}:</b><br/>{#votes}</td>
				</tr>
			' : '' ) . '
			<tr>
				<td colspan="2">&nbsp;</td>
				<td colspan="2" align="right">{save} {cancel}</td>
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

HtmlEnd();
?>