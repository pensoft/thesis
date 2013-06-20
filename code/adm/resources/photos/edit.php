<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
//~ ini_set('display_errors', 'Off');
session_write_close();

HtmlStart();
$historypagetype=HISTORY_ACTIVE;
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
	
	'guid' => array(
		'VType' => 'int',
		'CType' => 'hidden',		
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => false,
		'DisplayName' => getstr('admin.photos.colID'),
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.showButton'),
		'SQL' => '
			SELECT p.guid, s.id as article_id 
			FROM photos p
			JOIN storyproperties sp ON sp.valint = p.guid AND sp.propid = ' . (int) ARTICLE_PHOTOS_PROPID . '
			JOIN articles s ON s.id = sp.guid
			WHERE p.guid = {guid}
			',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW ,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.deleteButton'),
		'SQL' => '{guid}',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_REDIRECT ,
		'RedirUrl' => './',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'SQL' => '{article_id}{guid}',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_REDIRECT ,
		'RedirUrl' => './edit.php?guid={guid}&tAction=show',
		'AddTags' => array(
			'class' => 'frmbutton',			
		), 
	),
	
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.backButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '{#backurl}',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),	
);

$gKforTpl = '{guid}
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
			<th colspan="4">' . getstr('admin.articles.uploadArticlePicLabel') . '</th>
		</tr>
		<tr>
			<td colspan="4"><img src="/showimg.php?filename=mx50_{#guid}.gif" border="0" alt="" /></td>
		</tr>
		<tr>
			<td colspan="4"><b>{*article_id}:</b><br/>{article_id}</td>
		</tr>
		<tr>
			<td colspan="4"><b>' . getstr('admin.articles.colImage') . ':</b><br/><input style="width: 200px;" type="file" name="imgfile"></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{save} {cancel} {delete}
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
$gKfor = new kfor($gKforFlds, null, 'POST');
//~ $gKfor->debug = 1;
$gKfor->lFormHtml = $gKforTpl;

$lPicId = (int) $gKfor->lFieldArr['guid']['CurValue'];
if ($gKfor->lCurAction == 'save') {
	
	$lArticleId = (int) $gKfor->lFieldArr['article_id']['CurValue'];
	
	if((int) $lArticleId && $lPicId){
		$lFileName = 'imgfile';
		$lImageName = $_FILES[$lFileName]['name'];		
		
		$lError = '';
		$lPicId = UploadPic($lFileName, PATH_DL, $lPicId, $lError);
		if( $lError == '' && (int) $lPicId){
			AddPhotoToArticle( $lArticleId, $lPicId);
		}else{
			$gKfor->SetError('save', $lError);
		}		
	}	
}

if( $gKfor->lCurAction == 'delete') {
	if((int) $lPicId ){
		DeletePic($lPicId);
	}
}

echo $gKfor->Display();






HtmlEnd();

?>