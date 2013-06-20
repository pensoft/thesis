<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
UserRedir($user);
ini_set('display_errors', 'Off');
$gKforFlds = array(	
	'article_id' => array(
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
		'DisplayName' => getstr('admin.finalize_form.colTitle'),
	),
	
	'authors' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.finalize_form.colAuthors'),
	),
	
	'journal_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'SrcValues' => 'SELECT null as id, \'---\' as name UNION SELECT id, name FROM journals',
		'DisplayName' => getstr('admin.finalize_form.colJournalId'),
		'AllowNulls' => false
	),
	
	'issue' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.finalize_form.colIssue'),
	),
	
	'fpage' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.finalize_form.colFPage'),
	),
	
	'lpage' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.finalize_form.colLPage'),
	),
	
	'doi' => array(
		'VType' => 'string',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.finalize_form.colDoi'),
	),
	
	'pensoft_id' => array(
		'VType' => 'int',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.finalize_form.colPensoftId'),
	),
	
	'new' => array(
		'CType' => 'action',
		'Hidden' => true,
		'ActionMask' => ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spFinalizeArticle({article_id}, ' . (int) $user->id . ', {title}, {authors}, {journal_id}, {issue}, {fpage}, {lpage}, {doi}, {pensoft_id})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW ,
		'RedirUrl' => '',		
		'AddTags' => array(
			'class' => 'frmbutton',			
			'onclick' => 'submitFinalizeForm(' . (int)$_REQUEST['article_id'] . ');return false',
		), 
	),
	
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.backButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './',
		'AddTags' => array(
			'class' => 'frmbutton',
			'onclick' => 'destroyFinalizeForm();return false',
		),
	),	
);

$gKforTpl = '
{article_id}
<div class="t" style="margin-bottom:0px;">
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
				<th colspan="4">' .  getstr('admin.finalize_form.Label') . '</th>
			</tr>		
			<tr>
				<td colspan="4" valign="top"><b>{*title}:</b><br/>{title}</td>
			</tr>
			<tr>
				<td colspan="4" valign="top"><b>{*authors}:</b><br/>{authors}</td>			
			</tr>
			<tr>
				<td colspan="2" valign="top"><b>{*journal_id}:</b><br/>{journal_id}</td>
				<td colspan="2" valign="top"><b>{*issue}:</b><br/>{issue}</td>			
			</tr>
			<tr>
				<td colspan="2" valign="top"><b>{*fpage}:</b><br/>{fpage}</td>
				<td colspan="2" valign="top"><b>{*lpage}:</b><br/>{lpage}</td>			
			</tr>
			<tr>
				<td colspan="2" valign="top"><b>{*doi}:</b><br/>{doi}</td>
				<td colspan="2" valign="top"><b>{*pensoft_id}:</b><br/>{pensoft_id}</td>			
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td colspan="2" align="right">
					{save} {cancel} 				
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


$gKfor = new kfor($gKforFlds, null, 'POST', null, 1, 'finalize_form');
$gKfor->lFormHtml = $gKforTpl;


$gKfor->ExecAction();
if($gKfor->lCurAction == 'new'){
	FetchDataForArticleFinalization($gKfor);
}

$lResult = array(
	'form' => $gKfor->Display(), 
	'err_cnt' => $gKfor->lErrorCount,
	'action' => $gKfor->lCurAction,
);
returnAjaxResult($lResult);

?>