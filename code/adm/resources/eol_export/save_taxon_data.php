<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_ACTIVE;
global $gEcmsLibRequest;
HtmlStart();

$gTaxonIdentifiers = $_POST['identifier'];
$gTaxonKingdoms = $_POST['kingdom'];
$gTaxonFamilies = $_POST['family'];

if(!is_array($gTaxonIdentifiers) )
	$gTaxonIdentifiers = array();

if(!is_array($gTaxonKingdoms) )
	$gTaxonKingdoms = array();

if(!is_array($gTaxonFamilies) )
	$gTaxonFamilies = array();
	
$gKforFlds = array(	
	'export_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',		
	),
	
	'xml' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'AddTags' => array(
			'class' => 'coolinp contentTextArea',
		),
		'DisplayName' => getstr('admin.eol_export.colXML'),
		'AllowNulls' => true
	),
		
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => '{export_id}',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_REDIRECT ,
		'RedirUrl' => '/resources/eol_export/edit.php?tAction=showedit&id={export_id}',
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
$gKfor->debug = 0;

$gKfor->DoChecks();

if ($gKfor->lCurAction == 'save') {	
	$gTaxonData = array();
	foreach($gTaxonIdentifiers as $gCurrentTaxonIdentifier){
		$gTaxonData[$gCurrentTaxonIdentifier] = array(
			'kingdom' => $gTaxonKingdoms[$gCurrentTaxonIdentifier],
			'family' => $gTaxonFamilies[$gCurrentTaxonIdentifier],
		);
	}
	SyncEolExportFields($gKfor, true, $gTaxonData);
}


$gKfor->ExecAction();

$gKforTpl = '
{export_id}
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
			<th colspan="4">' . getstr('admin.eol_export.editLabel') . '</th>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*xml}:</b><br/>{xml}</td>
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