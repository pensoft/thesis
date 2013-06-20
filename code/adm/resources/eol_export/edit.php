<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype = HISTORY_ACTIVE;
global $gEcmsLibRequest;
HtmlStart();
$gKforFlds = array(	
	'id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'appended_to_xml_file' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => getstr('admin.eol_export.colAppendedToXmlFile'),
		'AllowNulls' => true,
	),
	
	'title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.eol_export.colTitle'),
	),
	
	
	'createuid' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT id, name FROM usr',
		'DisplayName' => getstr('admin.eol_export.colAuthor'),
	),
	
	'lastmod' => array(
		'VType' => 'date',
		'CType' => 'text',		
		'DisplayName' => getstr('admin.eol_export.colLastMod'),
	),
	
	'time_appended_to_xml_file' => array(
		'VType' => 'date',
		'CType' => 'text',		
		'DisplayName' => getstr('admin.eol_export.colTimeAppendedToXmlFile'),
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
	
	'issue_id' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		//~ 'SrcValues' => 'SELECT id, name FROM usr',
		'DisplayName' => getstr('admin.eol_export.colIssueId'),
	),
	
	'journal_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT id, name FROM journals',		
		'DisplayName' => getstr('admin.eol_export.colJournalId'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spEolExport(0, {id}, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spEolExport(1, {id}, {title}, {journal_id}, {issue_id}, {xml}, ' . (int) $user->id . ')',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spEolExport(3, {id}, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'class' => 'frmbutton',			
			'onclick' => 'javascript:if (!confirm(\'' . getstr('admin.eol_export.confirmDel') . '\')) {return false;}'
		), 
	),
	
	'generate_xml' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.eol_export.generateXmlButton'),
		'SQL' => 'SELECT * FROM spEolExport(1, {id}, {title}, {journal_id}, {issue_id}, {xml}, ' . (int) $user->id . ')',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW ,
		'RedirUrl' => './index.php',
		'AddTags' => array(
			//~ 'onclick' => 'alert(\'Generate XML\');return false;',
			'class' => 'frmbutton',						
		), 
	),
	
	'get_taxon_external_info' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.eol_export.externalTaxonInfoButton'),
		'SQL' => '{id}, {issue_id}, {journal_id}',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK ,
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'onclick' => 'alert(\'Get taxon external info\');return false;',
			'class' => 'frmbutton',						
		), 
	),
	
	'append_to_xml_file' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.eol_export.appendToXmlFileButton'),
		'SQL' => '{id}',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW,
		'RedirUrl' => './index.php',
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

if ($gKfor->lCurAction == 'save') {
	// Parsvane na special quotes:
	$gKfor->lFieldArr['title']['CurValue'] = parseSpecialQuotes($gKfor->lFieldArr['title']['CurValue']);	
}

$gKfor->DoChecks();


if ($gKfor->lCurAction == 'generate_xml' && !$gKfor->lErrorCount) {	
	GetPensoftJournalXmls($gKfor);	
}

if ($gKfor->lCurAction == 'append_to_xml_file' && !$gKfor->lErrorCount) {	
	AppendEolExportToPensoftXmlFile($gKfor);
	if( !$gKfor->lErrorCount ){
		header('Location: /resources/eol_export/edit.php?id=' . (int)$gKfor->lFieldArr['id']['CurValue'] . '&tAction=showedit');
		exit;
	}
}
//~ if ($gKfor->lCurAction == 'get_taxon_external_info' && !$gKfor->lErrorCount) {	
	//~ GetEolExportXmlTaxonExternalInfoByKfor($gKfor);	
//~ }

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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('admin.eol_export.editLabel') : getstr('admin.eol_export.addLabel') ) . getstr('admin.eol_export.nameLabel') . '</th>
		</tr>
		' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? 
			'
				<tr>
					<td colspan="2" valign="top">
						<b>{*lastmod}:</b> {@lastmod}<br/>
						<b>{*createuid}:</b> {@createuid}<br/>
						<b>{*appended_to_xml_file}:</b> {_yesNoAppendedToXml}<br/>
						<b>{*time_appended_to_xml_file}:</b> {#time_appended_to_xml_file}<br/>
					</td>
					<td colspan="2" valign="top" align="right">
						{save} {cancel} {delete} {generate_xml} 
						' . (!(int)$gKfor->lFieldArr['appended_to_xml_file']['CurValue'] ?
							'{append_to_xml_file}'
						:
							''
						) . '
					</td>
				</tr>
			' : '
				<tr>
					<td colspan="2" valign="top">&nbsp;</td>
					<td colspan="2" valign="top" align="right">{save} {cancel}</td>
				</tr>
			'
		) . '
		<tr>
			<td colspan="4" valign="top"><b>{*title}:</b><br/>{title}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*journal_id}:</b><br/>{journal_id}</td>
			<td colspan="2" valign="top"><b>{*issue_id}:</b><br/>{issue_id}</td>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*xml}:</b><br/>{xml}</td>
		</tr>		
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{save} {cancel} 
				' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? 
					'{delete}  {generate_xml} 
					' . 
						(!(int)$gKfor->lFieldArr['appended_to_xml_file']['CurValue'] ?
							'{append_to_xml_file}'
						:
							''
						) . '
					
					'
				:
					''
				) . '
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
	echo GetEolExportTaxons((int)$gKfor->lFieldArr['id']['CurValue'], $gKfor->lFieldArr['xml']['CurValue']);
}

HtmlEnd();
?>