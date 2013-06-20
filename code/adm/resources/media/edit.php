<?php

$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

$historypagetype = HISTORY_ACTIVE;
HtmlStart();

echo '
<script language="JavaScript">
	function mmTimeToSec(t) {
		var slen = document.getElementById(\'length\');
		var tarr = t.value.toString().split(\':\');
		var secs = 0;
		if (!parseInt(tarr[0]) && tarr.length == 1) {slen.value = \'\';return;}
		if (tarr.length == 1) {
			// ima samo sekundi
			secs = parseInt(tarr[0]);
		} else if (tarr.length == 2) {
			//ima minuti i secundi
			secs = parseInt(tarr[0]) * 60;
			secs += parseInt(tarr[1]);
		} else if (tarr.length == 3) {
			//ima chasove, minuti i secundi
			secs = parseInt(tarr[0]) * 3600;
			secs += parseInt(tarr[1]) * 60;
			secs += parseInt(tarr[2]);
		}
		slen.value = secs;
	}
</script>
';

$k = array(
	
	"guid" => array(
		"VType" => "int",
		"CType" => "hidden",
		"AllowNulls" => true
	),
	
	'mimetype' => array (
		'CType' => 'hidden',
		'VType' => 'string',
		'AllowNulls' => true,
	),
	
	'srctype' => array (
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),
	
	'srcid' => array (
		'CType' => 'hidden',
		'VType' => 'int',
		'AllowNulls' => true,
	),

	'language' => array(
		"VType" => "string",
		"CType" => "select",
		"DisplayName" => "Език",
		'SrcValues' => 'SELECT code as id, name FROM languages ORDER BY langid',
		"DefValue" => getlang(true),
		"Checks" => array(
			CKMAXSTRLEN('{language}', 3),
		),
		"AddTags" => array (
			"style" => "width: 70px;",
			"class" => "frmtext"
		),
	),

	"title" => array (
		"VType" => "string",
		"CType" => "text" ,
		"DisplayName" => "Заглавие",
		"AddTags" => array (
			"style" => "width: 99%",
			"class" => "frmtext"
		),
		"AllowNulls" => true,
	) ,
	
	"description" => array (
		"VType" => "string",
		"CType" => "textarea" ,
		"DisplayName" => "Описание",
		"AddTags" => array (
			"style" => "width: 99%",
			"rows" => "3",
			"class" => "frmtext",
		),
		"AllowNulls" => true
	),
	
	"author" => array (
		"VType" => "string",
		"CType" => "text" ,
		"DisplayName" => "Автор",
		"AddTags" => array (
			"style" => "width: 99%",
			"class" => "frmtext",
			"id" => "authtxt",
			"autocomplete" => "off",
		),
		"DisplayName" => "Автор",
		"AllowNulls" => true		
	) ,
	
	"imgname" => array(
		"VType" => "string",
		"CType" => "text",
		"AllowNulls" => true,
	),

	"ftype" => array (
		"VType" => "int",
		"CType" => "select",
		"SrcValues" => array(3 => 'audio', 4 => 'video'),
		"DisplayName" => "Тип",
		"AddTags" => array (
			"style" => "width: 100px;",
			"class" => "frmtext"
		),
		"AllowNulls" => false,
	),
	
	"access" => array (
		"VType" => "int",
		"CType" => "select",
		"SrcValues" => array(
			"0" => "Свободен",
			"1" => "С код",
			"2" => "Само за абонати",
		),
		"DisplayName" => "Достъп",
		"AllowNulls" => true,
		"DefValue" => 0,
		'AddTags' => array (
			'onchange' => 'javascript:showAttAccessCode();',
			"class" => "frmtext",
		),
	),

	'accesscode' => array (
		'CType' => 'text',
		'VType' => 'string',
		'AllowNulls' => true,
		'DisplayName' => 'Код за достъп',
		'DefValue' => substr(md5(time()), 0, 20),
		'AddTags' => array (
			'style' => 'width: 175px; color: #000;',
			"class" => "frmtext",
		),
	),
	
	'dim_x' => array (
		'CType' => 'text',
		'VType' => 'int',
		'AllowNulls' => true,
		'DisplayName' => 'Ширина',
		'AddTags' => array (
			'style' => 'width: 50px; color: #000;',
			"class" => "frmtext",
		),
	),
	
	'dim_y' => array (
		'CType' => 'text',
		'VType' => 'int',
		'AllowNulls' => true,
		'DisplayName' => 'Височина',
		'AddTags' => array (
			'style' => 'width: 50px; color: #000;',
			"class" => "frmtext",
		),
	),
	
	'length' => array (
		'CType' => 'text',
		'VType' => 'int',
		'AllowNulls' => true,
		'DisplayName' => 'Продължителност (в сек.)',
		'AddTags' => array (
			'style' => 'background: silver;width: 60px; color: #000;',
			"class" => "frmtext",
			"readonly" => "readonly",
			"id" => "length",
		),
	),
	
	'tmplength' => array (
		'CType' => 'text',
		'VType' => 'string',
		'AllowNulls' => true,
		'DisplayName' => 'Продължителност (чч:мм:сс)',
		'AddTags' => array (
			'style' => 'width: 100px; color: #000;',
			"class" => "frmtext",
			"onchange" => "mmTimeToSec(this);",
			"autocomplete" => "off",
		),
	),
	
	"filenameupl" => array (
		"VType" => "string",
		"CType" => "hidden",
		"AllowNulls" => true,
	),
	
	"mediasize" => array (
		"VType" => "string",
		"CType" => "hidden",
		"AllowNulls" => true,
	),
	
	"imgopts" => array(
		"VType" => "int",
		"CType" => "hidden",
		"AllowNulls" => true,
	),
	
	"storyid" => array(
		"VType" => "int",
		"CType" => "hidden",
		"AllowNulls" => true,
	),
	
	"sid" => array(
		"VType" => "int",
		"CType" => "hidden",
		"AllowNulls" => true,
	),
	
	"place" => array (
		"VType" => "int",
		"CType" => "select",
		"SrcValues" => array(
			"0" => "Не се показва в статията",
			"1" => "Горе Дясно",
			"2" => "Горе Ляво",
			'3' => "Долу",
		),
		"DisplayName" => "Място",
		"AllowNulls" => true,
		"DefValue" => 1,
	),
	
	"mediatxt" => array (
		"VType" => "string",
		"CType" => "textarea" ,
		"DisplayName" => "Текст под медия",
		"AddTags" => array (
			"style" => "width: 99%",
			"rows" => "3",
			"class" => "frmtext",
		),
		"AllowNulls" => true
	),
	"edit" => array (
		"CType" => "action",
		"SQL" => "SELECT * FROM spMultimedia(0, null, {guid}, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, {srctype}, null)",
		"ActionMask" => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		"Hidden" => true
	) ,
	"save" => array (
		"CType" => "action",
		"SQL" => "SELECT {ftype}, {guid}, {language}, {title}, {description}, {author}, {access}, {accesscode}, {dim_x}, {dim_y}, {length}, {srcid}, {place}, {mediatxt}, {mediasize}, {tmplength}, {srctype}, {mimetype}",
		"ActionMask" => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW ,
		"DisplayName" => "Запази",
		"AddTags" => array (
			"class" => "frmbutton"
		)
	),
	"delete" => array (
		"CType" => "action",
		"SQL" => "SELECT * FROM spMultimedia(3, null, {guid}, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null)",
		"ActionMask" => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
		"DisplayName" => "Изтрий",
		"RedirUrl" => '/media/index.php',
		"AddTags" => array (
			"onclick" => 'javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете тази медия ?\')) { return true; } else { return false;}',
			"class" => "frmbutton"
		)
	),
	"cancel" => array(
		"CType"	=> "action",
		"DisplayName" => "cancel",
		"ActionMask" =>	ACTION_REDIRECT,
		"RedirUrl" => '',
		"AddTags" => array (
			"class" => "frmbutton"
		),
		"DisplayName" => "Назад"
	)
);

$gFilesRoot = PATH_DL;
$kfor = new kfor($k, null, 'POST" enctype="multipart/form-data' );
$kfor->debug = false;

if (!$kfor->lFieldArr['srctype']['CurValue']) {
	$kfor->lFieldArr['srctype']['CurValue'] = 1;
}
if ($kfor->lFieldArr['storyid']['CurValue']) {
	$kfor->lFieldArr['srcid']['CurValue'] = $kfor->lFieldArr['storyid']['CurValue'];
}

if ($kfor->lCurAction == 'save' && $kfor->lFieldArr['tmplength']['CurValue']) {
	if (!preg_match('/(\d+)[\:\d]*/', $kfor->lFieldArr['tmplength']['CurValue'])) {
		$kfor->SetError('tmplength', 'Използвайте формат - чч:мм:сс');
	}
}

$fileinput = '<b>Файл:</b><br/><input style="width: 300px;" type="file" name="mediafile">';

if ($kfor->lCurAction == 'edit' || ($kfor->lCurAction == 'save' && (int)$kfor->lFieldArr['guid']['CurValue'])) {
	$fileinput = '
				<table>
					<tr><td><input type="radio" name="imgopts" value="0" ' . ((int)$kfor->lFieldArr["imgopts"]["CurValue"] == 0 ? 'checked' : '') . ' /> <b>Запази сегашния файл</b></td></tr>
					<tr><td><input type="radio" name="imgopts" value="1" ' . ($kfor->lFieldArr["imgopts"]["CurValue"] ? 'checked' : '') . ' /> <b>Добави нов файл</b><br/><input style="width: 300px;" type="file" name="mediafile"></td></tr>
				</table>
	';
}

$RelRows = '';
if ((int)$kfor->lFieldArr['srcid']['CurValue']) {
	$RelRows = '
		<tr><td colspan="5"><hr/></td></tr>
		<tr>
			<td colspan="2" valign="top">&nbsp;</td>
			<td valign="top"><b>{*place}</b><br/>{place}</td>
			<td valign="top" colspan="2"><b>{*mediatxt}</b><br/>{mediatxt}</td>
		</tr>
	';
	
	if ((int)$kfor->lFieldArr['sid']['CurValue']) {	// story
		$kfor->lFieldArr['cancel']['RedirUrl'] = '/resources/stories/'. $gSiteArr['dir'][$kfor->lFieldArr['sid']['CurValue']] .'/index.php?tAction=showedit&guid=' . $kfor->lFieldArr["srcid"]["CurValue"];
	} else {	
		$kfor->lFieldArr['cancel']['RedirUrl'] = '/resources/stories/edit.php?tAction=showedit&guid=' . $kfor->lFieldArr["srcid"]["CurValue"];
	}
}

$kfor->ExecAction();
if ($kfor->lCurAction == 'edit' && (int)$kfor->lFieldArr['length']['CurValue']) {
	if ((int)$kfor->lFieldArr['length']['CurValue'] < 60)
		$kfor->lFieldArr['tmplength']['CurValue'] = (int)$kfor->lFieldArr['length']['CurValue'];
	if ((int)$kfor->lFieldArr['length']['CurValue'] < 3600)
		$kfor->lFieldArr['tmplength']['CurValue'] = date('i:s', (int)$kfor->lFieldArr['length']['CurValue']);
	if ((int)$kfor->lFieldArr['length']['CurValue'] >= 3600)
		$kfor->lFieldArr['tmplength']['CurValue'] = date('g:i:s', (int)$kfor->lFieldArr['length']['CurValue']);
}

$preview = '';
//~ var_dump($kfor->lFieldArr['ftype']['CurValue']);
if ($kfor->lCurAction == 'edit' || ($kfor->lCurAction == 'save' && (int)$kfor->lFieldArr['guid']['CurValue'])) {
	if ((int)$kfor->lFieldArr['ftype']['CurValue'] == 3) {
		$preview = '
			<object style="vertical-align: middle;" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="150" height="20"
				codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab">
				<param name="movie" value="./singlemp3player.swf?file=./getmm.php?filename=o_' . (int)$kfor->lFieldArr['guid']['CurValue'] . '.mp3&songVolume=50&showDownload=false" />
				<param name="wmode" value="transparent" />
				<embed style="vertical-align: middle;" wmode="transparent" width="150" height="20" src="./singlemp3player.swf?file=./getmm.php?filename=o_' . (int)$kfor->lFieldArr['guid']['CurValue'] . '.mp3&songVolume=50&showDownload=false"
				type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
			</object>
			<br/><br/><a href="./getmm.php?filename=o_' . (int)$kfor->lFieldArr['guid']['CurValue'] . '.mp3">Вземи файл</a>
		';
	}
}

$cn = Con();


if ($kfor->lCurAction == 'save' && $kfor->lErrorCount == 0) {
	
	$lGuid = 0;
	if ((int)$kfor->lFieldArr['guid']['CurValue'] && !(int)$kfor->lFieldArr['imgopts']['CurValue']) {
		$sql = $kfor->ReplaceSqlFields('SELECT * FROM spMultimedia(1, {ftype}, {guid}, {language}, {title}, {description}, 
			{author}, ' . (int)$user->id . ', {access}, {accesscode}, {filenameupl}, {dim_x}, {dim_y}, {length}, {srcid}, {place}, {mediatxt}, {mediasize}, {srctype}, {mimetype})');
		//~ echo $sql;
		//~ exit;
		$cn->Execute($sql);
	} elseif ($_FILES['mediafile']['name']) {
		$gMaxSize = 200*1024*1024;
		$extarray = array(".mp3", ".flv");
		$gFileExt = substr($_FILES['mediafile']['name'], strrpos($_FILES['mediafile']['name'], '.'));
		$isExtension = in_array(strtolower($gFileExt), $extarray);
		
		$mimearr = array("audio/mpeg", "video/x-flv", "application/x-flash-video", 'application/octet-stream');
		$isMimeType = in_array(strtolower($_FILES['mediafile']['type']), $mimearr);
		
		if ($_FILES['mediafile']['size'] > $gMaxSize) {
			$kfor->SetError('Файл', 'Прекалено голям файл. Максимален размер: ' . $gMaxSize/1024 . ' kB');
		}
		if (!$_FILES['mediafile']['size']) {
			$kfor->SetError('Файл', 'Невалиден файл!');
		}
		if ($isExtension && $isMimeType) {
			if ($_FILES['mediafile']['error'] == UPLOAD_ERR_OK) {
				$sql = $kfor->ReplaceSqlFields('SELECT * FROM spMultimedia(1, {ftype}, {guid}, {language}, {title}, {description}, 
					{author}, ' . (int)$user->id . ', {access}, {accesscode}, \'' . q($_FILES['mediafile']['name']) . '\', {dim_x}, {dim_y}, {length}, {srcid}, {place}, {mediatxt}, ' . (int)$_FILES['mediafile']['size'] . ', {srctype}, \'' . q(strtolower($_FILES['mediafile']['type'])) . '\')');
				
				//~ echo $sql;exit;
				$cn->Execute($sql);
				$cn->MoveFirst();
				if (!$lGuid = (int)$cn->mRs['guid']) {
					$kfor->SetError('Файл', 'Грешка в базата данни! - spMultimedia');
				} else {
					$kfor->lFieldArr['guid']['CurValue'] = $lGuid;
					
					$fName = 'oo_' . $lGuid . $gFileExt;
					if (!move_uploaded_file($_FILES['mediafile']['tmp_name'], $gFilesRoot . $fName)) {
						$kfor->SetError('Файл', 'Грешка при качването на файл! - move_uploaded_file');
					} else {
						
						$isExtension = false;
						$isMimeType = false;
						
						if ($_FILES['imgfile']['name']) {
							$extarray = array(".jpg", ".jpeg", ".png", ".gif");
							$gFileExt = substr($_FILES['imgfile']['name'], strrpos($_FILES['imgfile']['name'], '.'));
							$isExtension = in_array(strtolower($gFileExt), $extarray);
							
							$mimearr = array("image/jpeg", "image/gif", "image/png");
							$isMimeType = in_array(strtolower($_FILES['imgfile']['type']), $mimearr);
							
							if ($_FILES['imgfile']['size'] > $gMaxSize) {
								$kfor->SetError('Thumbnail', 'Прекалено голям файл. Максимален размер: ' . $gMaxSize/1024 . ' kB');
							}
							if (!$_FILES['imgfile']['size']) {
								$kfor->SetError('Thumbnail', 'Невалиден файл!');
							}
							
							if ($isExtension && $isMimeType) {
								if ($_FILES['imgfile']['error'] == UPLOAD_ERR_OK) {
									exec("convert -colorspace rgb " . $_FILES['imgfile']['tmp_name'] . " " . $gFilesRoot . "oo_" . $lGuid . '.jpg');
									exec("convert -colorspace rgb -quality 80 -thumbnail " . escapeshellarg('1024x768>') . " " . $_FILES['imgfile']['tmp_name'] . " " . $gFilesRoot . "big_" . $lGuid . '.jpg');
									exec("convert -colorspace rgb -quality 80 -thumbnail " . escapeshellarg('450x450>') . " " . $_FILES['imgfile']['tmp_name'] . " " . $gFilesRoot . "gb_" . $lGuid . '.jpg');
								} else {
									$kfor->SetError('Thumbnail', 'Грешка при качването на файл! - ' . $_FILES['imgfile']['error']);
								}
							} else {
								$kfor->SetError('Thumbnail', 'Непознат тип на файла! - ' . $_FILES['imgfile']['type']);
							}
						}
					}
				}
			} else {
				$kfor->SetError('Файл', 'Грешка при качването на файл! - ' . $_FILES['mediafile']['error']);
			}
		} else {
			$kfor->SetError('Файл', 'Непознат тип на файла! - ' . $_FILES['mediafile']['type']);
		}
	} else {
		//~ echo 'dasd';
		if (!(int)$kfor->lFieldArr['guid']['CurValue'] || (int)$kfor->lFieldArr['imgopts']['CurValue']) {
			$kfor->SetError('Файл', 'Трябва да зададете файл!');
		}
	}
	
	if ($kfor->lErrorCount == 0) {
		header("Location: " . $kfor->lFieldArr['cancel']['RedirUrl']);
		exit;
	}
}

if ($kfor->lCurAction == 'delete' && $kfor->lErrorCount == 0) {
	removeUplFiles((int)$kfor->lFieldArr['guid']['CurValue'], $gFilesRoot);
}

$h = '{guid}{filenameupl}{srcid}{srctype}{sid}{mediasize}{mimetype}
	<table width="100%" cellspacing="0" cellpadding="2" border="0" class="formtable">
		<colgroup>
			<col width="20%" />
			<col width="20%" />
			<col width="20%" />
			<col width="20%" />
			<col width="20%" />
		</colgroup>
		<tr>
			<th colspan="5">Мултимедия</th>
		</tr>
		<tr>
			<td colspan="2" valign="middle" align="center" rowspan="3">&nbsp;' . $preview . '&nbsp;</td>
			<td><b>{*ftype}</b><br/>{ftype}</td>
			<td><b>{*tmplength}</b><br/>{tmplength}<br/> = {length}сек.</td>
			<td align="right"><b>Размери (в px)</b><br/>{dim_x}&nbsp;x&nbsp;{dim_y}</td>
		</tr>
		<tr>
			<td colspan="3"><b>{*title}</b><br/>{title}</td>
		</tr>
		<tr>
			<td colspan="3"><b>{*author}</b><br/>{author}</td>
		</tr>
		<tr>
			<td colspan="2">' . $fileinput . '

			</td>
			<td colspan="3"><b>{*description}</b><br/>{description}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>Thumbnail:</b><br/><input style="width: 300px;" type="file" name="imgfile"></td>
			<td><b>{*language}</b><br/>{language}</td>
			<td><b>{*access}</b><br/>{access}</td>
			<td><b>{*accesscode}</b><br/>{accesscode}</td>
		</tr>
		' . $RelRows . '
		<tr>
			<td colspan="5" align="right"><hr />{delete} {save} {cancel}</td>
		</tr>
	</table>
	<script language="JavaScript">showAttAccessCode();</script>
';

$kfor->SetFormHtml($h);

echo $kfor->Display();

HtmlEnd();
?>