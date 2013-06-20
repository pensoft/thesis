<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

HtmlStart();

$gKforFlds = array(	
	'id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'name' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.auto_numerate_rules.colName'),
	),
	
	'attribute_name' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.auto_numerate_rules.colAttributeName'),
	),
	
	'xpath' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.auto_numerate_rules.colXPath'),
	),
	
	'starting_value' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.auto_numerate_rules.colStartingValue'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spAutoNumerateRules(0, {id}, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spAutoNumerateRules(1, {id}, {name}, {xpath}, {attribute_name}, {starting_value})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spAutoNumerateRules(3, {id}, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'copy' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.copyButton'),
		'SQL' => 'SELECT * FROM spAutoNumerateRules(4, {id}, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => './edit.php?tAction=showedit&id={id}',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	
	
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.backButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),	
);

$gKfor = new kfor($gKforFlds, null, 'POST');
$gKfor->debug = false;

$gKfor->ExecAction();

$gKforTpl = '
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
			<col width="25%" />
			<col width="25%" />
			<col width="25%" />
		</colgroup>
		<tr>
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('admin.auto_numerate_rules.editLabel') : getstr('admin.auto_numerate_rules.addLabel') ) . getstr('admin.auto_numerate_rules.nameLabel') . '</th>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*name}:</b><br/>{name}</td>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*xpath}:</b><br/>{xpath}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*attribute_name}:</b><br/>{attribute_name}</td>
			<td colspan="2" valign="top"><b>{*starting_value}:</b><br/>{starting_value}</td>
		</tr>			
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{save} {cancel} ' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? '{delete}' : '') . '				
			</td>
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

$gKfor->lFormHtml = $gKforTpl;

echo $gKfor->Display();

HtmlEnd();


?>