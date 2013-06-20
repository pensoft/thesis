<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once("$docroot/lib/static.php");
$historypagetype = HISTORY_ACTIVE;
HtmlStart();

$t = array(
	'id' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	'uname' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Потребител',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'name' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Име',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'upass1' => array(
		'CType' => 'text',
		'VType' => 'string',
		'AllowNulls' => true,
		'DisplayName' => 'Парола',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'email' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Имейл',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'phone' => array(
		'CType' => 'text',
		'VType' => 'string',
		'AllowNulls' => true,
		'DisplayName' => 'Телефон',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'state' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(0 => 'неактивен', 1 => 'активен'),
		'DisplayName' => 'Статус',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'utype' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(0 => 'Вътрешен', 1 => 'Външен'),
		'DisplayName' => 'Тип',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'show' => array(
		'CType' => 'action',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM spUsr(0, {id}, null, null, null, null, null, null, null)',
		'Hidden' => true,
	),
	
	'save' => array(
		'CType' => 'action',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spUsr(1, {id}, {uname}, {name}, {upass1}, {email}, {phone}, {state}, {utype})',
		'DisplayName' => 'Запази',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => 'Назад',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
);

$f = new kfor($t);

if ((int)$f->lFieldArr['id']['CurValue']) {
	$f->lFieldArr['uname']['AllowNulls'] = true;
}

$f->ExecAction();

$h = '{id}
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
		<th colspan="2">' . ((int)$f->lFieldArr['id']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на потребител</th>
	</tr>
	' . (
		(int)$f->lFieldArr['id']['CurValue'] ? '' : 
		'
	<tr>
		<td><b>{*uname}:</b></td>
		<td>{uname}</td>
	</tr>
		'
	) . '
	<tr>
		<td><b>{*name}:</b></td>
		<td>{name}</td>
	</tr>
	<tr>
		<td><b>{*upass1}:</b></td>
		<td>{upass1}</td>
	</tr>
	<tr>
		<td><b>{*email}:</b></td>
		<td>{email}</td>
	</tr>
	<tr>
		<td><b>{*phone}:</b></td>
		<td>{phone}</td>
	</tr>
	<tr>
		<td><b>{*state}:</b></td>
		<td>{state}</td>
	</tr>
	<tr>
		<td><b>{*utype}:</b></td>
		<td>{utype}</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td nowrap="true" align="right">{save} {cancel}</td>
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

$f->lFormHtml = $h;

echo $f->Display();

HtmlEnd();
?>