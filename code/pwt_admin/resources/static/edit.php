<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_ACTIVE;

HtmlStart();

$t = array(
	'staticid' => array (
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	
	'artid' => array (
		'CType' => 'mlfield',
		'VType' => 'mlint',
		'DisplayName' => getstr('admin.staticpages.colStoryTitle'),
		'TransType' => MANY_TO_SQL_ARRAY,
		'DisplayFormat' => MLSTR_D_NOTABLE,
		'AllowNulls' => true,
		'mlfield'=>array(
			'CType' => 'select',
			'VType' => 'int',
			'SrcValues' => '
				SELECT null as id, \'--\' as name, 1 as ord 
				UNION 
				SELECT guid as id, title as name, 2 as ord FROM stories WHERE coalesce(storytype, 0) <> 1 AND state IN (3,4) and lang=\'{ml_code}\'
				ORDER BY ord, name
			',
			'AllowNulls' => true,
			'AddTags' => array (
				'class' => 'coolinp',
			),
		),
	),
	
	'artname' => array (
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => getstr('admin.staticpages.colSPName'),
		'AllowNulls' => false,
		'AddTags' => array (
			'class' => 'coolinp',
		),
	),
	
	'show' => array (
		'CType' => 'action',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM spStatic(0, {staticid}, null, null, null)',
		'Hidden' => true,
	),
	
	'saveb' => array (
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spStatic(1, {staticid}, {artid}::int[], {artname}, 1)',
		'AddTags' => array (
			'class' => 'frmbutton',
		),
		'RedirUrl' => '',
	),
	
	'delb' => array (
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spStatic(3, {staticid}, null,null,null)',
		'AddTags' => array (
			'class' => 'frmbutton',
			'onclick' => 'return confirm(\'Сигурни ли сте че желаете да изтриете този запис?\')'
		),
		'RedirUrl' => '',
	),
	
	'backb' => array (
		'CType' => 'action',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '',
		'DisplayName' => getstr('admin.backButton'),
		'AddTags' => array (
			'class' => 'frmbutton',
		),
	)
);

$addLabel = getstr('admin.staticpages.addLabel');
$editLabel = getstr('admin.staticpages.editLabel');

$h1 = '{staticid}
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
			<tr><th colspan="2">' . getstr('admin.staticpages.addPage', array('addoredit' => ((int)$_REQUEST['staticid'] ? $editLabel : $addLabel))) . '</th></tr>
			{artid}
			<tr><td><b>{*artname}</b></td><td>{artname}</td></tr>
			<tr><td colspan="2" align="right">{delb} {saveb} {backb}</td></tr>
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

$k = new kfor($t, $h1);
$k->ExecAction();
echo $k->Display();

HtmlEnd();
?>