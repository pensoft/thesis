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
	
	'subobject_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'field_id',			
		),
		'SrcValues' => 'SELECT null as id, \'----\' as name UNION
			SELECT id, name FROM objects ORDER BY id ASC',
		'DisplayName' => getstr('pwt_admin.objects.subobjects.colName'),
	),
	
	'min_occurrence' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',			
		),
		'DisplayName' => getstr('pwt_admin.objects.subobjects.colMinOccurrence'),
	),
	
	'initial_occurrence' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('pwt_admin.objects.subobjects.colInitialOccurrence'),
	),
	
	'max_occurrence' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('pwt_admin.objects.subobjects.colMaxOccurrence'),
	),
	
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spObjectSubobject(0, {id}, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spObjectSubobject(1, {id}, {object_id}, {subobject_id}, {min_occurrence}, {max_occurrence}, {initial_occurrence}, ' . (int)$user->id . ')',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spObjectSubobject(3, {id}, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW ,
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

if($gKfor->lCurAction == 'save'){
	if($gKfor->lFieldArr['min_occurrence']['CurValue'] < 0 ){
		$gKfor->SetError('min_occurrence', getstr('pwt_admin.objects.subobjects.fieldMustBeNonNegative'));
	}
	if($gKfor->lFieldArr['max_occurrence']['CurValue'] < 0 ){
		$gKfor->SetError('max_occurrence', getstr('pwt_admin.objects.subobjects.fieldMustBeNonNegative'));
	}
	
	if($gKfor->lFieldArr['min_occurrence']['CurValue'] > $gKfor->lFieldArr['max_occurrence']['CurValue'] ){
		$gKfor->SetError('max_occurrence', getstr('pwt_admin.objects.subobjects.minOccurrenceShouldBeLessThanMaxOccurrence'));
	}
}
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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('pwt_admin.objects.subobjects.editLabel') : getstr('pwt_admin.objects.subobjects.addLabel') ) . getstr('pwt_admin.objects.subobjects.nameLabel') . '</th>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*subobject_id}:</b><br/>{subobject_id}</td>			
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*min_occurrence}:</b><br/>{min_occurrence}</td>
			<td colspan="2" valign="top"><b>{*max_occurrence}:</b><br/>{max_occurrence}</td>
		</tr>		
		<tr>
			<td colspan="2" valign="top"><b>{*initial_occurrence}:</b><br/>{initial_occurrence}</td>			
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