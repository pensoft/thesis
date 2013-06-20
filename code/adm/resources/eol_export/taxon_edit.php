<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_ACTIVE;
global $gEcmsLibRequest;
HtmlStart();
$gKforFlds = array(	
	'export_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',		
	),
	
	'doi' => array(
		'VType' => 'string',
		'CType' => 'hidden',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.eol_export.colDoi'),
	),
	
	
	'kingdom' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.eol_export.colKingdom'),
	),
	
	'phylum' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.eol_export.colPhylum'),
	),
	
	'class' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.eol_export.colClass'),
	),
	
	'order' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.eol_export.colOrder'),
	),
	
	'family' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.eol_export.colFamily'),
	),
	
	'scientific_name' => array(
		'VType' => 'string',
		'CType' => 'hidden',
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.eol_export.colScientificName'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => '{export_id} {doi}',
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => '{export_id} {doi}',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
		
		
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.backButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './edit.php?tAction=showedit&id={export_id}',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),	
);
$gKfor = new kfor($gKforFlds, null, 'GET');
$gKfor->debug = 0;

$gKfor->ExecAction();

if ($gKfor->lCurAction == 'showedit' && !(int)$gKfor->lErrorCount) {//Popylvame poletata ot xml-a
	SyncEolExportFields($gKfor);
	
}

if ($gKfor->lCurAction == 'save' && !(int)$gKfor->lErrorCount) {//Popylvame poletata ot xml-a
	SyncEolExportFields($gKfor, true);
	
}



$gKforTpl = '
{export_id}{doi}{scientific_name}
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
			<th colspan="4">' . getstr('admin.eol_export.taxonEditLabel') . '</th>
		</tr>
		
		<tr>
			<td colspan="2" valign="top"><b>{*doi}:</b><br/>{#doi}</td>
			<td colspan="2" valign="top"><b>{*scientific_name}:</b><br/>{#scientific_name}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*kingdom}:</b><br/>{kingdom}</td>
			<td colspan="2" valign="top"><b>{*family}:</b><br/>{family}</td>
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

HtmlEnd();
?>