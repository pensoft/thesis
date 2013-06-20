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
		'DisplayName' => getstr('admin.journals.colName'),
	),
	
	'pensoft_title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.journals.colPensoftTitle'),
	),
	
	'title_abrev' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.journals.colTitleAbrev'),
	),
	
	'issn_print' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.journals.colIssnPrint'),
	),
	
	'issn_online' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.journals.colIssnOnline'),
	),
	
	'publisher' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.journals.colPublisher'),
	),
	
	'keys_apikey' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
		'DisplayName' => getstr('admin.journals.colKeysApiKey'),
	),
	
	'xml_file_name' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.journals.colXmlFileName'),
	),
	
	'wiki_username_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT id, username as name FROM wiki_login',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.journals.colWikiUsername'),
		'AllowNulls' => false,
	),
	
	'export_types' => array(
		'VType' => 'int',
		'CType' => 'mselect',
		'TransType' => MANY_TO_SQL_ARRAY,
		'SrcValues' => 'SELECT id, name FROM export_types',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.journals.colExportTypes'),
		'AllowNulls' => true,
	),
	
	'pensoft_id' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.journals.colPensoftId'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM spJournals(0, {id}, null, null, null, null, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spJournals(1, {id}, {name}, {pensoft_id}, {pensoft_title}, {xml_file_name}, {title_abrev}, {issn_print}, {issn_online}, {publisher}, {keys_apikey}, {export_types}, {wiki_username_id})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => 'SELECT * FROM spJournals(3, {id}, null, null, null, null, null, null, null, null, null, null, null)',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'copy' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.copyButton'),
		'SQL' => 'SELECT * FROM spJournals(4, {id}, null, null, null, null, null, null, null, null, null, null, null)',
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
			<th colspan="4">' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? getstr('admin.journals.editLabel') : getstr('admin.journals.addLabel') ) . getstr('admin.journals.nameLabel') . '</th>
		</tr>
		<tr>
			<td colspan="4" valign="top"><b>{*name}:</b><br/>{name}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*pensoft_id}:</b><br/>{pensoft_id}</td>
			<td colspan="2" valign="top"><b>{*pensoft_title}:</b><br/>{pensoft_title}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*title_abrev}:</b><br/>{title_abrev}</td>
			<td colspan="2" valign="top"><b>{*publisher}:</b><br/>{publisher}</td>
		</tr>	
		<tr>
			<td colspan="2" valign="top"><b>{*issn_print}:</b><br/>{issn_print}</td>
			<td colspan="2" valign="top"><b>{*issn_online}:</b><br/>{issn_online}</td>
		</tr>		
		<tr>
			<td colspan="2" valign="top"><b>{*xml_file_name}:</b><br/>{xml_file_name}</td>
			<td colspan="2" valign="top"><b>{*keys_apikey}:</b><br/>{keys_apikey}</td>			
		</tr>	
		<tr>
			<td colspan="2" valign="top"><b>{*export_types}:</b><br/>{export_types}</td>
			<td colspan="2" valign="top"><b>{*wiki_username_id}:</b><br/>{wiki_username_id}</td>			
		</tr>			
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{save} {cancel} ' . ((int)$gKfor->lFieldArr['id']['CurValue'] ? '{delete} ' : '') . '				
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