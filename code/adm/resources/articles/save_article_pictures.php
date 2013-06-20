<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'Off');
session_write_close();


HtmlStart();

$gKforFlds = array(	
	'article_id' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => 'SELECT id, id || \' ---- \' || title as name FROM articles ORDER BY id DESC',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => false,
		'DisplayName' => getstr('admin.articles.colArticleId'),
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => 'SELECT * FROM spRemoveArticleImages({article_id}, '. (int) ARTICLE_PHOTOS_PROPID . ')',
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
		'RedirUrl' => './',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),	
);

$gKforTpl = '
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
			<th colspan="4">' . getstr('articles.removePhotosLabel') . '</th>
		</tr>
		<tr>
			<td colspan="4"><b>{*article_id}:</b><br/>{article_id}</td>
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
$gKfor = new kfor($gKforFlds, null, 'POST', null, 1, 'def1');
$gKfor->lFormHtml = $gKforTpl;
//~ var_dump($gKfor->lCurAction);
//~ exit;
if( $gKfor->lCurAction == 'save') {
	$lArticleId = (int) $gKfor->lFieldArr['article_id']['CurValue'];
	DeleteArticlePics($lArticleId);
}


echo $gKfor->Display();

HtmlEnd();
?>