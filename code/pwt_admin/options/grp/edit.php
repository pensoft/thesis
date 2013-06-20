<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

HtmlStart();

$t = array(
	'id' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	'name' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Име',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'show' => array(
		'CType' => 'action',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM sp_secgrp(0, {id}, null)',
		'Hidden' => true,
	),
	
	'save' => array(
		'CType' => 'action',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM sp_secgrp(1, {id}, {name})',
		'RedirUrl' => './index.php',
		'DisplayName' => 'Запази',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => 'Назад',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
);

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
			<th colspan="2">' . ((int)$_REQUEST['id'] ? 'Редактиране' : 'Добавяне' ) . ' на група</th>
		</tr>
		<tr>
			<td><b>{*name}:</b></td>
			<td>{name}</td>
		</tr>
		<tr>
			<td colspan="2" align="right">{show}{save} {cancel}</td>
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

$f = new kfor($t, $h);
$f->ExecAction();
echo $f->Display();

HtmlEnd();

?>