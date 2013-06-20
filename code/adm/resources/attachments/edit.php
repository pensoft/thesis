<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_ACTIVE;

function UploadAttachment($name, $dir, $kfor) {
	$gMaxSize = 5*1024*1024; // 1 MB
	//~ $gMaxSize = 500 // Za testove;
	$extarray = array(".doc", ".rtf", ".pdf", ".txt", ".zip", ".rar", ".xls", ".csv", ".tar");
	$typearr = array("text/plain", "text/richtext", "text/tab-separeted-values", "application/pdf", "application/rtf", "application/word", "application/zip", "application/x-tar", "application/x-rar-compressed", "application/x-rar", "application/msword", "application/vnd.ms-excel");
	$imgUploadErr = 1;
	
	if ( $_FILES[$name]['name'] ) {
		
		$pFnUpl = $_FILES[$name]['name'];
		$pTitle = $kfor->lFieldArr['title']['CurValue'];
		if (!$pTitle) $pTitle = $pFnUpl;
		$PicText = $kfor->lFieldArr['underpic']['CurValue'];
		$PicSrc = (int)$kfor->lFieldArr['source']['CurValue'];
		
		$gFileExt = substr($_FILES[$name]['name'], strrpos($_FILES[$name]['name'], '.'));
		$isImageExtension = in_array(strtolower($gFileExt), $extarray);
		$isImageMime = in_array(strtolower($_FILES[$name]['type']), $typearr);
		//~ var_dump($_FILES[$name]['type']);
		if ($isImageExtension && $isImageMime) {
			if ($_FILES[$name]['size'] > $gMaxSize) {
				$kfor->SetError($_FILES[$name]['name'], 'Файлът е твърде голям! Максимален размер: 5MB');
			} elseif (!$_FILES[$name]["size"]) {
				$kfor->SetError($_FILES[$name]['name'], 'Невалиден файл!');
			} elseif ($_FILES[$name]['error'] == UPLOAD_ERR_OK) {
				$lCn = Con() ;
				$lCn->Execute('SELECT AttUpload(1, null, ' . (int)$PicSrc . ',\'' . q($pTitle) . '\', \'' . q($pFnUpl) . '\', \'' . q($PicText) . '\', \'' . q($gFileExt) . '\', \'' . q(strtolower($_FILES[$name]['type'])) . '\') as picid');
				$lCn->MoveFirst();
				$picid = (int)$lCn->mRs['picid'];
				
				if ($picid) {
					if (!move_uploaded_file($_FILES[$name]['tmp_name'], $dir . 'oo_' . $picid . $gFileExt)) {
						$kfor->SetError($_FILES[$name]['name'], 'Грешка: ' . $_FILES[$name]['error']);
					} else { 
						// Vsichko e ok...
						$imgUploadErr = 0;
					}
				} else {
					$kfor->SetError($_FILES[$name]['name'], 'Неочаквана грешка в базата данни!');
				}
			} else {
				$kfor->SetError($_FILES[$name]['name'], 'Грешка при запазването на файла!');
			}
		} else {
			$kfor->SetError($_FILES[$name]['name'], 'Невалиден тип файл! Позволените типове са: .doc, .rtf, .pdf, .txt, .zip, .rar, .xls, .csv, .tar');
		}
	} else {
		$kfor->SetError('Файл', 'Не сте добавили файл');
	}
	
	if (!$imgUploadErr) return $picid;
	
	if ($picid) $lCn->Execute('SELECT AttUpload(3, ' . (int)$picid . ', null, null, null, null, null);');
	return false;
}

$lHide = 0;
if ((int)$_GET['storyid']) {
	$lHide = 1;
}

HtmlStart($lHide);

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
	
	'delete' => array(
		'CType' => 'action',
		'Hidden' => true,
		'RedirUrl' => '',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spAttachemnts(3, {guid}, {storyid}, null, null, null)',
	),
	
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
	
	'cancel' => array(
		'CType' => 'action',
		'AddTags' => array(
			'class' => 'frmbutton',
		), 			
		'DisplayName' => getstr('admin.backButton'),
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '',
	),		
);

$k = new kfor($t, null, 'POST');

if (!(int)$k->lFieldArr['guid']['CurValue'] || (int)$k->lFieldArr['imgopts']['CurValue']) {
	if (!$_FILES['imgfile']['name'] && $k->lCurAction == 'save') $k->SetError('Файл', 'Не сте добавили файл');
}

$k->ExecAction();

$addLabel = getstr('admin.attachments.addLabel');
$editLabel = getstr('admin.attachments.editLabel');

if (!(int)$k->lFieldArr['storyid']['CurValue']) {
	
	$h = '{guid}{picid}{storyid}{imgname}
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
		<th colspan="3">' . getstr('admin.attachments.addPic', array('addoredit' => ((int)$k->lFieldArr['guid']['CurValue'] ? $editLabel : $addLabel))) . '</th>
	</tr>' . ((int)$k->lFieldArr['guid']['CurValue'] ? '
	<tr>
		<td rowspan="4"><a href="/getatt.php?filename=oo_' . $k->lFieldArr['imgname']['CurValue'] . '">[Свали]</a></td>
		<td width="120" align="right"><b>{*picid}:</b></td>
		<td>
			<label><input type="radio" name="imgopts" value="0" checked /> ' . getstr('admin.attachments.dontReplacePic') .'</label>
			<label><input type="radio" name="imgopts" value="1" /> ' . getstr('admin.attachments.replacePic') .'</label>
		</td>
	</tr>
	<tr>
		<td width="120" align="right">&nbsp;</td>
		<td><input style="width: 200px;" type="file" name="imgfile"></td>
	</tr>

	' : '
	<tr>
		<td rowspan="4">&nbsp;</td>
		<td width="120" align="right"><b>{*picid}:</b></td>
		<td><input style="width: 200px;" type="file" name="imgfile"></td>
	</tr>
	') . '
	<tr>
		<td width="120" align="right"><b>{*title}:</b></td>
		<td>{title}</td>
	</tr>
	<tr>
		<td colspan="3" align="right">{delete}{show}{save} {cancel}</td>
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
} else {
	$h = '{guid}{storyid}{picid}{imgname}
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
		<th colspan="3">' . getstr('admin.attachments.addPic', array('addoredit' => ((int)$k->lFieldArr['guid']['CurValue'] ? $editLabel : $addLabel))) . '</th>
	</tr>' . ((int)$k->lFieldArr['guid']['CurValue'] ? '
	<tr>
		<td rowspan="7"><a href="/getatt.php?filename=oo_' . $k->lFieldArr['imgname']['CurValue'] . '">[file]</a></td>
		<td width="120" align="right"><b>{*picid}</b></td>
		<td>
			<label><input type="radio" name="imgopts" value="0" checked /> ' . getstr('admin.attachments.dontReplacePic') .'</label>
			<label><input type="radio" name="imgopts" value="1" /> ' . getstr('admin.attachments.replacePic') .'</label>
		</td>
	</tr>
	<tr>
		<td width="120" align="right">&nbsp;</td>
		<td><input style="width: 200px;" type="file" name="imgfile"></td>
	</tr>

	' : '
	<tr>
		<td rowspan="7">&nbsp;</td>
		<td width="120" align="right"><b>{*picid}</b></td>
		<td><input style="width: 200px;" type="file" name="imgfile"></td>
	</tr>
	') . '
	<tr>
		<td width="120" align="right"><b>{*title}</b></td>
		<td>{title}</td>
	</tr>
	<tr>
		<td width="120" align="right"><b>{*underpic}</b></td>
		<td>{underpic}</td>
	</tr>
	<tr>
		<td colspan="2" align="right">{delete}{show}{save} {cancel}</td>
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
}
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
			} else {
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


HtmlEnd($lHide);

?>