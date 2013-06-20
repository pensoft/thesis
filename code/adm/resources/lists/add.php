<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

HtmlStart();

$t = array(
	'listnameid' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	
	'name' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => getstr('admin.lists.nameFld'),
		'Checks' => array(
			CKMAXSTRLEN("{name}", 255),
		),
		'AddTags' => array(
			'class' => 'coolinp',
		),	
	),
	
	'objtype' => array(
		'VType' => 'int' ,
		'CType' => 'select' ,
		'SrcValues' => array(1 => 'Статии', 2 => 'Рубрики'),
		'DisplayName' => getstr('admin.lists.objtypeFld'),
		'AddTags' => array(
			'class' => 'coolinp',
		),	
		'AllowNulls' => true,
	),
	
	'sid' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DefValue' => 1,
	),	
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => '',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM addList(0, {listnameid}, null, null, null)',
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM addList(1, {listnameid}, {name}, {objtype}, {sid})',
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'class' => 'frmbutton',
		),		
	),
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.backButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'class' => 'frmbutton',
		),		
	),
);

$h = '
	{listnameid}{sid}
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
					<th>' . getstr('admin.lists.addList') . '</th>
				</tr>
				<tr>
					<td><b>{*name}:</b><br/>{name}</td>
				</tr>
				<tr>
					<td><b>{*objtype}:</b><br/>{objtype}</td>
				</tr>
				<tr>
					<td align="right">{save} {cancel}</td>
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
$f->debug = false;
echo $f->Display();

HtmlEnd();

?>