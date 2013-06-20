<?php
error_reporting((int)ERROR_REPORTING);

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
	'article_id' => array(
		'VType' => 'int',
		'CType' => 'hidden',
		'DisplayName' => '',
		'AllowNulls' => true,
	),
	
	'version' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.article_versions.colVersion'),
	),
	
	'xml_content' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'AddTags' => array(
			'class' => 'coolinp contentTextArea',
		),
		'DisplayName' => getstr('admin.article_versions.colContent'),
		'AllowNulls' => true
	),
	
	'article_title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.article_versions.colArticleTitle'),
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT a.id as article_id, v.id, v.version, v.xml_content, a.title as article_title FROM article_versions v JOIN articles a ON a.id = v.article_id WHERE v.id = {id}',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	'back_to_article' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.article_versions.backToArticleButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '/resources/articles/edit.php?tAction=showedit&id={article_id}',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
		
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.backButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),	
);

$gKfor = new kfor($gKforFlds, null, 'POST');
$gKfor->debug = 0;

$gKfor->ExecAction();

if( !(int) $gKfor->lFieldArr['article_id']['CurValue'] ){
	header('Location: /resources/articles/');
	exit;
}

$gKforTpl = '
{id}{article_id}
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
				<th colspan="4">' . getstr('admin.article_versions.viewVersion') . '</th>
			</tr>	
			<tr>
				<td colspan="2" valign="top">&nbsp;</td>
				<td colspan="2" valign="top" align="right">{cancel} {back_to_article}</td>
			</tr>		
			<tr>
				<td colspan="4" valign="top"><b>{*article_title}:</b><br/>{@article_title}</td>				
			</tr>
			<tr>				
				<td colspan="4" valign="top"><b>{*version}:</b><br/>{@version}</td>
			</tr>
			<tr>
				<td colspan="4"><b>{*xml_content}:</b><br/>{@xml_content}</td>
			</tr>		
			<tr>
				<td colspan="2">&nbsp;</td>
				<td colspan="2" align="right">{cancel} 	{back_to_article}			
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
</div>';

$gKfor->lFormHtml = $gKforTpl;

echo $gKfor->Display();

HtmlEnd();

?>