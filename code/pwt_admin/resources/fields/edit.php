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
		'DisplayName' => getstr('pwt_admin.fields.colName'),
	),
	
	'type' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT id, name FROM field_types',
		'DisplayName' => getstr('pwt_admin.fields.colType'),
	),
	
	'default_label' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('pwt_admin.fields.colDefaultLabel'),
	),
	
	'default_control_type' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT id, name FROM html_control_types',
		'DisplayName' => getstr('pwt_admin.fields.colHtmlControlType'),
	),
	
	'default_allow_nulls' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('pwt_admin.fields.colDefaultAllowNulls'),
	),
	
	'default_is_read_only' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),
		'DisplayName' => getstr('pwt_admin.fields.colDefaultIsReadOnly'),
	),
	
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spFields(0, {id}, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spFields(1, {id}, {name}, {type}, {default_label}, {default_control_type}, {default_allow_nulls}, {default_is_read_only}, ' . (int)$user->id . ')',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => '',
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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('pwt_admin.fields.editLabel') : getstr('pwt_admin.fields.addLabel') ) . getstr('pwt_admin.fields.nameLabel') . '</th>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*name}:</b><br/>{name}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*type}:</b><br/>{type}</td>
			<td colspan="2" valign="top"><b>{*default_label}:</b><br/>{default_label}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*default_control_type}:</b><br/>{default_control_type}</td>
			<td colspan="2" valign="top"><b>{*default_allow_nulls}:</b><br/>{default_allow_nulls}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*default_is_read_only}:</b><br/>{default_is_read_only}</td>			
		</tr>		
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{save} {cancel} 
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

if((int)$gKfor->lFieldArr['id']['CurValue']){
	echo ShowFieldRelatedObjects($gKfor->lFieldArr['id']['CurValue']);
}

HtmlEnd();


?>