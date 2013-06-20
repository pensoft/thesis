<?php

$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_ACTIVE;

HtmlStart();

function UploadPic($name, $dir, $kfor) {
	$gMaxSize = 1*1024*1024; // 1 MB
	//~ $gMaxSize = 500 // Za testove;
	$extarray = array(".jpeg", ".jpg", ".gif", ".tif", ".tiff", ".bmp", ".png");
	$typearr = array("image/pjpeg", "image/jpeg", "image/gif", "image/tiff", "image/png", "image/bmp");
	$imgUploadErr = 1;
	$lguid=(int)$kfor->lFieldArr['picid']['CurValue'];
	{
		
		if ( $_FILES[$name]['name'] ) {
			
			$pFnUpl = $_FILES[$name]['name'];
			$pTitle = $kfor->lFieldArr['name']['CurValue'][1];
			if (!$pTitle) $pTitle = $pFnUpl;
			$PicSrc = 2;
			
			$gFileExt = substr($_FILES[$name]['name'], strrpos($_FILES[$name]['name'], '.'));
			$isImageExtension = in_array(strtolower($gFileExt), $extarray);
			$isImageMime = in_array(strtolower($_FILES[$name]['type']), $typearr);
			
			if ($isImageExtension && $isImageMime) {
				if ($_FILES[$name]['size'] > $gMaxSize) {
					$kfor->SetError($_FILES[$name]['name'], 'Снимката е твърде голяма! Максимален размер: 1MB');
				} elseif (!$_FILES[$name]["size"]) {
					$kfor->SetError($_FILES[$name]['name'], 'Невалиден файл!');
				} elseif ($_FILES[$name]['error'] == UPLOAD_ERR_OK) {
					$lCn = Con() ;
					$lCn->Execute('SELECT PicsUpload(1, null, ' . (int)$PicSrc . ',\'' . q($pTitle) . '\', \'' . q($pFnUpl) . '\', null) as picid');
					$lCn->MoveFirst();
					$picid = (int)$lCn->mRs['picid'];
					
					if ($picid) {
						if (!move_uploaded_file($_FILES[$name]['tmp_name'], $dir . $picid . $gFileExt)) {
							$kfor->SetError($_FILES[$name]['name'], 'Грешка: ' . $_FILES[$name]['error']);
						} else { 
							// Vsichko e ok... pravim jpg i mahame originala
							exec(escapeshellcmd("convert -colorspace rgb -quality 80 " . $dir . $picid . $gFileExt . " " . $dir . 'oo_' . $picid . '.jpg' ));
							exec("convert -colorspace rgb -quality 80 -thumbnail " . escapeshellarg('1024x1024>') . " " . $dir . $picid . $gFileExt . " " . $dir . 'big_' . $picid . '.jpg' );
							unlink($dir . $picid . $gFileExt);
							$imgUploadErr = 0;
						}
					} else {
						$kfor->SetError($_FILES[$name]['name'], 'Неочаквана грешка в базата данни!');
					}
				} else {
					$kfor->SetError($_FILES[$name]['name'], 'Грешка при запазването на файла!');
				}
			} else {
				$kfor->SetError($_FILES[$name]['name'], 'Невалиден тип файл! Позволените типове са: .jpeg, .jpg, .gif, .png, .bmp');
			}
		} else {
			$kfor->SetError('Снимка', 'Не сте добавили снимка');
		}
	}
	if (!$imgUploadErr) return $picid;
	
	if ($picid) $lCn->Execute('SELECT PicsUpload(3, ' . (int)$picid . ', null, null, null, null);');
	return false;
}

$t = array(
	'id' => array (
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	
	'picid' => array(
		'CType' => 'hidden',
		'VType' => 'int',
		'AddTags' => array(
			'id' => 'picid',
		),
		'AllowNulls' => true,
	),
	
	'imgopts' => array(
		'CType' => 'hidden',
		'VType' => 'int',
	),
	

	'name' => array(
		'CType' => 'text',
		'VType' => 'string',
		'DisplayName' => 'Име',
		'AllowNulls' => false,
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => '',
		'Hidden' => true,
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		'SQL' => 'SELECT * FROM spProductColor(0, {id}, null, null)',
	),
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW,
		'SQL' => 'SELECT * FROM spProductColor(1, {id}, {name}, {picid})',
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		'SQL' => 'SELECT * FROM spProductColor(3, {id}, null, null)',
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),		
	),
	"cancel" => array(
		'CType' => 'action',
		'DisplayName' => 'Назад',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		),	
	),

);

$f = new kfor($t, null, 'POST');

if ((int)$f->lFieldArr['imgopts']['CurValue']) {
	if (!$_FILES['imgfile']['name'] && $f->lCurAction == 'save') $f->SetError('Цвят', 'Не сте добавили снимка');
}

$f->ExecAction();

$h = '{id}{picid}{imgopts}
<table width="100%" cellspacing="0" cellpadding="2" border="0" class="formtable">
	<colgroup>
		<col width="100" />
		<col width="800" />
	</colgroup>
	<tr>
		<th colspan="2">' . ((int)$f->lFieldArr['id']['CurValue'] ? 'Редактиране' : 'Добавяне') . ' на цвят</th>
	</tr>
	<tr>
		<td><b>{*name}:</b></td>
		<td>{name}</td>
	</tr>
	<tr>
		<td colspan="2">
			<table>
				<colgroup>
					<col width="100" />
					<col width="800" />
				</colgroup>
				<tr>
					<td valign="top" rowspan="3"><img id="provlogo" src="./showimg.php?filename=d115x35_{#picid}.jpg" /></td>
					<td valign="top"><b>Снимка</b></td>
				</tr>
				<tr>
					<td valign="top">
						<label><input type="radio" name="imgopts" value="0" checked /> Запази</label>
						<label><input type="radio" name="imgopts" value="1" /> Качи нов</label>
						<br/><a href="javascript:openw(\'./sel.php\', \'aa\', \'location=no,menubar=yes,width=950,height=600,scrollbars=yes,resizable=yes,top=0,left=0\')"">Добави съществуващa снимка</a>
					</td>
				</tr>
				<tr>
					<td valign="top"><input style="width: 200px;" type="file" name="imgfile"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2" align="right">{show} {save} {delete} {cancel}</td></tr>
</table>';

$f->SetFormHtml($h);

if ($f->lCurAction == 'save' && $f->lErrorCount == 0) {
	if ((int)$f->lFieldArr['imgopts']['CurValue']==1) {
		$f->lFieldArr['picid']['CurValue'] = (int)UploadPic('imgfile', PATH_DL, $f);
		if ((int)$f->lFieldArr['imgopts']['CurValue'] && $f->lFieldArr['picid']['CurValue']) {
			$filesToDel = glob(PATH_DL . '*_' . $f->lFieldArr['picid']['CurValue']. '.jpg');
			foreach ($filesToDel as $file) {
				if (preg_match('/^big_.*$/', basename($file)) || preg_match('/^oo_.*$/', basename($file))) continue;
				exec('rm ' . $file);
			}
		}
	}
	
	
	if ($f->lErrorCount == 0) {
		$cn = Con();
		$sql = $f->ReplaceSqlFields($f->lFieldArr['save']['SQL']);

		$cn->Execute($sql);
		if (!$cn->GetLastError()) {
			//~ header('Location: /providers/edit.php?provider_id=' . (int)$f->lFieldArr['provider_id']['CurValue'] . '&tAction=show');
			header('Location: /store/products/color/index.php');
			exit;
		}
		$f->SetError('save', $cn->GetLastError());
	}
}

echo $f->Display();
HtmlEnd();

?>