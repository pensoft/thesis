<?
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$gPg = $_GET['pg'];
$page = (int)$_GET['p'];



//~ if (!(int)$_REQUEST['storyid'] && !$gPg) {
	//~ echo 'Невалидна статия!';
	//~ exit;
//~ }

HtmlStart(1);

echo '
<script>
	function addfile(guid){
		document.def2.photoid.value = guid;
		window.location.hash = "#add";
	}
</script>
';

$t = array(
	'guid' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	
	'picid' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'DisplayName' => getstr('admin.attachments.fileCol'),
		'AllowNulls' => true,
	),
	
	'imgopts' => array(
		'CType' => 'hidden',
		'VType' => 'int',
	),
	
	'imgname' => array(
		'CType' => 'hidden',
		'VType' => 'string',
	),
	
	'title' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => getstr('admin.attachments.titleCol'),
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'underpic' => array(
		'CType' => 'textarea',
		'VType' => 'string',
		'DisplayName' => getstr('admin.attachments.underPicFld'),
		'AllowNulls' => true,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'storyid' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	
	'source' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'SrcValues' => array(
			0 => 'За статия',
			1 => 'За продукт',
			2 => 'За справочник',
		),
		'DisplayName' => getstr('admin.attachments.categoryFld'),
		'AllowNulls' => true,
		'DefValue' => 0,
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'place' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => array(
			0 => 'Не се показва в статията',
			1 => 'Горе Дясно',
			2 => 'Горе Ляво',
			3 => 'Долу',
			4 => 'Голяма снимка',
		),
		'DisplayName' => getstr('admin.attachments.placeFld'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
	),
	
	'show' => array(
		'CType' => 'action',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM spAttachemnts(0, {guid}, {storyid}, null, null, null)',
	),
	
	//~ 'delete' => array(
		//~ 'CType' => 'action',
		//~ 'Hidden' => true,
		//~ 'RedirUrl' => '',
		//~ 'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		//~ 'SQL' => 'SELECT * FROM spAttachemnts(3, {guid}, {storyid}, null, null, null)',
	//~ ),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.saveButton'),
		'AddTags' => array(
			'class' => 'frmbutton',
		), 
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW,
		'SQL' => 'SELECT * FROM spAttachemnts(1, {guid}, {storyid}, {picid}, {title}, {underpic})',
		'RedirUrl' => '',
	),
	
	//~ 'cancel' => array(
		//~ 'CType' => 'action',
		//~ 'AddTags' => array(
			//~ 'class' => 'frmbutton',
		//~ ), 			
		//~ 'DisplayName' => getstr('admin.backButton'),
		//~ 'ActionMask' => ACTION_REDIRECT,
		//~ 'RedirUrl' => '',
	//~ ),		
);
$k = new kfor($t, null, 'POST');

$k->ExecAction();
$editLabel = getstr('admin.attachments.editLabel');

$h = '{show}{guid}{storyid}{picid}{imgname}
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="5" border="0" class="formtable">
	<tr>
		<th colspan="3">'. $editLabel .' на файл към елемент</th>
	</tr>
	<tr>
		<td width="120" align="right"><b>{*title}</b></td>
		<td>{title}</td>
	</tr>
	<tr>
		<td width="120" align="right"><b>{*underpic}</b></td>
		<td>{underpic}</td>
	</tr>
	<tr>
		<td colspan="2" align="right">{save}</td>
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

$k->SetFormHtml($h);
	
if ($k->lCurAction == 'save' && $k->lErrorCount == 0) {

	if (!(int)$k->lFieldArr['guid']['CurValue'] || (int)$k->lFieldArr['imgopts']['CurValue']) {
		$k->lFieldArr['picid']['CurValue'] = (int)UploadAttachment('imgfile', PATH_DL, $k);
	}
	
	if ($k->lErrorCount == 0) {
		
		if (!$k->lFieldArr['title']['CurValue']) 
			$k->lFieldArr['title']['CurValue'] = $_FILES['imgfile']['name'];
		
		$cn = Con();
		$sql = $k->ReplaceSqlFields($k->lFieldArr['save']['SQL']);
		echo $sql;
		//~ exit;
		$cn->Execute($sql);
		if (!$cn->GetLastError()) {
			
			if (!(int)$k->lFieldArr['storyid']['CurValue']) {
				header('Location: /resources/attachments/');
			} 
			else {
				echo '<script>
						window.opener.location.reload();
						window.close();
					</script>';
					//~ header('Location: /resources/stories/extra.php?guid=' . (int)$k->lFieldArr['storyid']['CurValue']);
			}
			exit;
		}
		$k->SetError('save', $cn->GetLastError());
	}
}

echo $k->Display();

HtmlEnd(1);
?>