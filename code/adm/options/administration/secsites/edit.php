<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/static.php');

$historypagetype = HISTORY_ACTIVE;

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
		'DisplayName' => getstr('admin.secsites.colSiteLabel'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'url' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'URL',
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	'ord' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => getstr('admin.secsites.colPositionLabel'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'type' => array(
		'CType' => 'select',
		'VType' => 'int',
		'DisplayName' => getstr('admin.secsites.colTypeLabel'),
		'SrcValues' => array(
			1 => getstr('admin.secsites.linkType'),
			2 => getstr('admin.secsites.delimiterType'),
		),
		'DefValue' => 1,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),

	'show' => array(
		'CType' => 'action',
		'Hidden' => true,
		'SQL' => 'SELECT * FROM spsecsites(0, {id}, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'AddTags' => array(
			'class' => 'frmbutton',
		),
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIR,
		'SQL' => 'SELECT * FROM spsecsites(1, {id}, {name}, {url}, {ord}, {type}, {_mycntslashes})',
		'RedirUrl' => '',
	),
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.backButton'),
		'AddTags' => array(
			'class' => 'frmbutton',
		),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '',
	)
);

$h = '
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
			<tr>
				<th>' . ((int)$_REQUEST['id'] ? getstr('admin.secsites.editLabel') : getstr('admin.secsites.addLabel') ) . getstr('admin.secsites.nameLabel') . '</th>				
			</tr>
			<tr>
				<td><b>{*name}:</b><br/>{name}</td>
			</tr>
			<tr>
				<td><b>{*url}:</b><br/>{url}</td>
			</tr>
			<tr>
				<td><b>{*ord}:</b><br/>{ord}</td>
			</tr>
			<tr>
				<td><b>{*type}:</b><br/>{type}</td>
			</tr>
			<tr>
				<td align="right">{show}{save} {cancel}</td>
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

function mycntslashes($p) {
	return substr_count($p['url'], '/');
}

HtmlEnd();

?>