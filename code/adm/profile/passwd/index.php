<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once("$docroot/lib/static.php");
HtmlStart();

$t = array(
	'oldpass' => array(
		'CType' => 'password',
		'VType' => 'string',
		'DisplayName' => getstr('admin.passwd.oldPassword'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'newpass' => array(
		'CType' => 'password',
		'VType' => 'string',
		'DisplayName' => getstr('admin.passwd.newPassword'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'confirmpass' => array(
		'CType' => 'password',
		'VType' => 'string',
		'DisplayName' => getstr('admin.passwd.confirmPassword'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'confirm' => array(
		'CType' => 'action',
		'AddTags' => array(
			'class' => 'frmbutton',
		), 
		'DisplayName' => getstr('admin.changeButton'),
		'RedirUrl' => './',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spPasswd(' . $user->id . ', {oldpass}, {newpass}, {confirmpass})',
	),
	
	'cancel' => array(
		'CType' => 'action',
		'AddTags' => array(
			'class' => 'frmbutton',
		), 			
		'DisplayName' => getstr('admin.backButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '/',
	),	
	
);

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
				<colgroup>
					<col width="200"></col>
					<col width="*"></col>					
				</colgroup>
			</tr>
			<tr>
				<th colspan="2">' . getstr('admin.passwd.passwordChangeLabel') . '</th>
			</tr>
			<tr>
				<td width="120" align="right"><b>{*oldpass}:</b></td>
				<td>{oldpass}</td>
			</tr>
			<tr>
				<td width="120" align="right"><b>{*newpass}:</b></td>
				<td>{newpass}</td>
			</tr>
			<tr>
				<td width="120" align="right"><b>{*confirmpass}:</b></td>
				<td>{confirmpass}</td>
			</tr>
			<tr>
				<td colspan="2" align="right">{confirm} {cancel}</td>
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
echo $f->Display();

HtmlEnd();
?>