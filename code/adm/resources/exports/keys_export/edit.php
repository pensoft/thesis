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
	
	'title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.keys_export.colTitle'),
	),
	
	'is_uploaded' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => getstr('admin.keys_export.colIsUploaded'),
		'AllowNulls' => true,
	),
	
	'upload_has_errors' => array(
		'VType' => 'int',
		'CType' => 'text',
		'DisplayName' => getstr('admin.keys_export.colUploadHasErrors'),
		'AllowNulls' => true,
	),
	
	'upload_msg' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => getstr('admin.keys_export.colUploadMsg'),
		'AllowNulls' => true,
	),
	
	'upload_time' => array(
		'VType' => 'date',
		'CType' => 'text',		
		'DisplayName' => getstr('admin.keys_export.colUploadTime'),
	),
	
	
	'createuid' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT id, name FROM usr',
		'DisplayName' => getstr('admin.keys_export.colAuthor'),
	),
	
	'lastmod' => array(
		'VType' => 'date',
		'CType' => 'text',		
		'DisplayName' => getstr('admin.keys_export.colLastMod'),
	),
	
	'xml' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'AddTags' => array(
			'class' => 'coolinp contentTextArea',			
		),
		'DisplayName' => getstr('admin.keys_export.colXML'),
		'AllowNulls' => true
	),
	
	'article_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT null as id, \'--\' as name UNION SELECT id, id || \' -- \' || title as name FROM articles ORDER BY id DESC',
		'DisplayName' => getstr('admin.keys_export.colArticleId'),
	),	
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spKeysExport(0, {id}, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spKeysExport(1, {id}, {title}, {article_id}, {xml}, ' . (int) $user->id . ')',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spKeysExport(3, {id}, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => './index.php',
		'AddTags' => array(
			'class' => 'frmbutton',			
			'onclick' => 'javascript:if (!confirm(\'' . getstr('admin.keys_export.confirmDel') . '\')) {return false;}'
		), 
	),
	
	'generate_xml' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.keys_export.generateXmlButton'),
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW ,
		'RedirUrl' => './index.php',
		'AddTags' => array(
			//~ 'onclick' => 'alert(\'Generate XML\');return false;',
			'class' => 'frmbutton',						
		), 
	),
	
	'upload_xml' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.keys_export.uploadXmlButton'),
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW ,
		'RedirUrl' => './index.php',
		'AddTags' => array(
			//~ 'onclick' => 'alert(\'Generate XML\');return false;',
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


$gKfor->ExecAction();

if ($gKfor->lCurAction == 'generate_xml' && !$gKfor->lErrorCount) {	
	try{
		GenerateExportXml($gKfor->lFieldArr['id']['CurValue'], true, true);
		header('Location: ./edit.php?id=' . (int) $gKfor->lFieldArr['id']['CurValue'] . '&tAction=showedit');
		exit;
	}catch(Exception $pException){
		$gKfor->SetError('generate_xml', $pException->GetMessage());	
	}
}
if ($gKfor->lCurAction == 'upload_xml' && !$gKfor->lErrorCount) {	
	try{
		UploadExportXml($gKfor->lFieldArr['id']['CurValue']);
		header('Location: ./edit.php?id=' . (int) $gKfor->lFieldArr['id']['CurValue'] . '&tAction=showedit');
		exit;
	}catch(Exception $pException){
		$gKfor->SetError('upload_xml', $pException->GetMessage());	
	}
}

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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('admin.keys_export.editLabel') : getstr('admin.keys_export.addLabel') ) . getstr('admin.keys_export.nameLabel') . '</th>
		</tr>
		' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? 
			'
				<tr>
					<td colspan="2" valign="top">
						<b>{*lastmod}:</b> {@lastmod}<br/>
						<b>{*createuid}:</b> {@createuid}<br/>	
						<b>{*is_uploaded}:</b> {_yesNoIsUploaded}<br/>
						<b>{*upload_time}:</b> {#upload_time}<br/>
						<b>{*upload_has_errors}:</b> {_yesNoUploadHasErrors}<br/>
						<b>{*upload_msg}:</b> {#upload_msg}<br/>	
					</td>
					<td colspan="2" valign="top" align="right">
						{save} {cancel} {delete} {generate_xml} 
						' . (!(int)$gKfor->lFieldArr['is_uploaded']['CurValue'] ?
							'{upload_xml}'
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
			<td colspan="2" valign="top"><b>{*article_id}:</b><br/>{article_id}</td>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*xml}:</b><br/>{xml}</td>
		</tr>		
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{save} {cancel} 
				' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? 
					'{delete}  {generate_xml}
					' . (!(int)$gKfor->lFieldArr['is_uploaded']['CurValue'] ?
							'{upload_xml}'
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

HtmlEnd();
?>