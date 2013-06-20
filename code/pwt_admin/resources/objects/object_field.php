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
	
	'object_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => false,
	),
	
	'field_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'field_id',
			'onchange' => 'LoadFieldDefaultValues()',
		),
		'SrcValues' => 'SELECT null as id, \'----\' as name UNION
			SELECT id, name FROM fields
			ORDER BY id ASC
			',
		'DisplayName' => getstr('pwt_admin.objects.fields.colName'),
	),
	
	'label' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'label',
		),
		'DisplayName' => getstr('pwt_admin.objects.fields.colLabel'),
	),
	
	'control_type' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'control_type',
		),
		'SrcValues' => 'SELECT null as id, \'----\' as name UNION
		SELECT id, name FROM html_control_types 
		ORDER BY id ASC
		',
		'DisplayName' => getstr('pwt_admin.objects.fields.colHtmlControlType'),
	),
	
	'allow_nulls' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'allow_nulls',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),		
		'DisplayName' => getstr('pwt_admin.objects.fields.colAllowNulls'),
	),
	
	'is_read_only' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'allow_nulls',
		),
		'SrcValues' => array(
			0 => getstr('global.no'),
			1 => getstr('global.yes'),
		),		
		'DisplayName' => getstr('pwt_admin.objects.fields.colIsReadOnly'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spObjectFields(0, {id}, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spObjectFields(1, {id}, {object_id}, {field_id}, {label}, {control_type}, {allow_nulls}, {is_read_only}, ' . (int)$user->id . ')',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spObjectFields(3, {id}, null, null, null, null, null, null, null)',
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

$gKfor = new kfor($gKforFlds, null, 'GET');
$gKfor->debug = false;


$gKfor->ExecAction();

$gKforTpl = '
{id}{object_id}
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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('pwt_admin.objects.fields.editLabel') : getstr('pwt_admin.objects.fields.addLabel') ) . getstr('pwt_admin.objects.fields.nameLabel') . '</th>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*field_id}:</b><br/>{field_id}</td>
			<td colspan="2" valign="top"><b>{*label}:</b><br/>{label}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*control_type}:</b><br/>{control_type}</td>
			<td colspan="2" valign="top"><b>{*allow_nulls}:</b><br/>{allow_nulls}</td>
		</tr>		
		<tr>
			<td colspan="2" valign="top"><b>{*is_read_only}:</b><br/>{is_read_only}</td>			
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