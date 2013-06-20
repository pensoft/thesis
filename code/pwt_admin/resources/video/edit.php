<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$historypagetype=HISTORY_ACTIVE;

$lHide = 0;
if ($_REQUEST['mode'] == 'rel') {
	$lHide = 1;
}

HtmlStart($lHide);

$lStoryTypeArray = array(6 => 'Видео');
$lStoryTypeDefValue = 6;
$t = array(
	'guid' => array(
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
		'DisplayName' => getstr('admin.stories.colTitle'),
	),
	
	'author' => array(
		'VType' => 'string',
		'CType' => 'text',
		'Checks' => array(
			CKMAXSTRLEN('{author}', 128),
		),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colAuthor'),
		'AllowNulls' => true,
	),
	
	'storydesc' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'Checks' => array(
			CKMAXSTRLEN('{storydesc}', 4096),
		),
		'AddTags' => array(
			'class' => 'coolinp',
			'style' => 'height: 50px;',
		),
		'DisplayName' => getstr('admin.stories.colDescription'),
		'AllowNulls' => true,
	),
	
	'lastmod' => array(
		'VType' => 'date',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colLastMod'),
	),
	
	'createuid' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => 'Създал',
		'AddTags' => array(
			'style' => 'width: 120px',
		),
	),
	
	'primarysite' => array (
		'VType' => 'int',
		'CType' => 'hidden',
		'AllowNulls' => false,
		'DefValue' => 1,
	), 
	
	'state' => array (
		'VType' => 'int' ,
		'CType' => 'select' ,
		'SrcValues' =>	$gStoriesStates,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colState'),
	),
	
	'storytype' => array(
		'VType' => 'int',
		'CType' => 'select',
		'SrcValues' => $lStoryTypeArray,
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colStoryType'),
		'DefValue' => $lStoryTypeDefValue,
		'AllowNulls' => true,
	),
	
	'pubdate' => array(
		'VType' => 'date',
		'CType' => 'text',
		'AddTags' => array(
			'class' => 'coolinp',
			'id' => 'pubdate',
		),
		'DisplayName' => getstr('admin.stories.colPubDate'),
		'DefValue' => date('d/m/Y H:i'),
	),

	'language' => array(
		'VType' => 'string',
		'CType' => 'select',
		'SrcValues' => 'SELECT code as id, name FROM languages ORDER BY langid',
		'DefValue' => getlang(true),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'DisplayName' => getstr('admin.stories.colLang'),
	),
	
	'showforum' => array(
		'VType' => 'int',
		'CType' => 'checkbox',
		'SrcValues' => array(1 => getstr('admin.stories.colShowForum'), 0 => 0),
		'TransType' => MANY_TO_BIT_ONE_BOX,
		'AllowNulls' => true,
		'IsNull' => 0,
		'Separator' => '&nbsp;&nbsp;',
		'DefValue' => 1,
		'DisplayName' => getstr('admin.stories.colShowForum'),
	),
	
	"mediaguid" => array(
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
	
	"imgname" => array(
		"VType" => "string",
		"CType" => "text",
		"AllowNulls" => true,
	),

	"ftype" => array (
		"VType" => "int",
		"CType" => "select",
		"SrcValues" => array(
			4 => 'Нормало видео',
			5 => 'Вградено видео (от външен източник)',
		),
		"DisplayName" => "Тип на видеото",
		"AddTags" => array (
			'class' => 'coolinp',
			'style' => 'width: 300px;',
			'id' => 'ftype',
			'onchange' => 'displayVideoInput();',
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
	
	'embedsource' => array(
		'VType' => 'string',
		'CType' => 'textarea',
		'DisplayName' => 'Код',
		'AddTags' => array(
			'class' => 'coolinp',
			'style' => 'width: 50%;',
			'rows' => '10',
		),
		'AllowNulls' => true,
	),
	
	'showedit' => array(
		'CType' => 'action',
		'Hidden' => true,
		'DisplayName' => '',
		'SQL' => 'SELECT * FROM GetMediaBaseData({guid}, ' . (int) $lStoryTypeDefValue . ' , ' . (int) $user->id . ', \'' . getlang(1) . '\')',
		'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
	),
	
	'save' => array(
		'CType' => 'action',
		'DisplayName' => 'Запази',
		'SQL' => 'SELECT SaveStoriesBaseData({guid}, {primarysite}, {language}, {title}, null, {storydesc}, {pubdate}, {author}, 
			\'' . $user->id . '\', null, {state}, null, null, {storytype}, null, null, 
			1, 1, {showforum}, ' . (int)STORIES_DSCID . ', null) as guid /*{embedsource}*/',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | /*ACTION_EXEC | ACTION_FETCH |*/ ACTION_REDIRECT,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
		), 
	),
	
	'delete' => array(
		'CType' => 'action',
		'DisplayName' => 'Изтрий',
		'SQL' => 'SELECT * FROM deleteVideo({guid})',
		'ActionMask' => ACTION_CHECK | ACTION_CCHECK | /*ACTION_EXEC | ACTION_FETCH |*/ ACTION_REDIRECT,
		'Hidden' => false,
		'RedirUrl' => '',
		'AddTags' => array(
			'class' => 'frmbutton',
			'onclick' => 'javascript: if (confirm(\'Сигурни ли сте, че искате да изтриете това видео?\')) { return true; } else { return false;}',
		),
	),
	
	'cancel' => array(
		'CType' => 'action',
		'DisplayName' => 'Назад',
		'ActionMask' => ACTION_REDIRECT,
		'RedirUrl' => './',
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	)	
);

$gFilesRoot = PATH_DL;
$kfor = new kfor($t, null, 'POST" enctype="multipart/form-data' );
$kfor->debug = false;

if (!$kfor->lFieldArr['srctype']['CurValue']) {
	$kfor->lFieldArr['srctype']['CurValue'] = 1;
}

if ($kfor->lCurAction == 'save') {
	$kfor->lFieldArr['title']['CurValue'] = parseSpecialQuotes($kfor->lFieldArr['title']['CurValue']);
	$kfor->lFieldArr['author']['CurValue'] = parseSpecialQuotes(replaceBom($kfor->lFieldArr['author']['CurValue']));
}

if ($kfor->lCurAction == 'save' && $kfor->lFieldArr['tmplength']['CurValue']) {
	if (!preg_match('/(\d+)[\:\d]*/', $kfor->lFieldArr['tmplength']['CurValue'])) {
		$kfor->SetError('tmplength', 'Използвайте формат - чч:мм:сс');
	}
}

$fileinput = '
		<div id="normalVideoInput" class="visibleElm">
			<b>Файл:</b><br/><input type="file" name="mediafile" size="36">
		</div>
		<div id="embedVideoInput" class="hiddenElm">
			<b>{*embedsource}</b>:<br />{embedsource}
		</div>
';


$RelRows = '';
if ($kfor->lFieldArr['ftype']['CurValue'] == 5) {
	$kfor->lFieldArr['embedsource']['AllowNulls'] = false;
}
$kfor->ExecAction();



if ((int)$kfor->lFieldArr['mediaguid']['CurValue']) {
	$fileinput = '
		<table id="normalVideoInput" class="visibleElm">
			<tr>
				<td>
					<input type="radio" name="imgopts" value="0" ' . ((int)$kfor->lFieldArr["imgopts"]["CurValue"] == 0 ? 'checked' : '') . ' /> <b>Запази сегашния файл</b>
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="imgopts" value="1" ' . ($kfor->lFieldArr["imgopts"]["CurValue"] ? 'checked' : '') . ' /> <b>Добави нов файл</b>
					<br/><br/>
					<b>Файл</b>: <input type="file" name="mediafile" size="36">
				</td>
			</tr>
		</table>
		<div id="embedVideoInput" class="hiddenElm">
			<b>{*embedsource}</b>:<br />{embedsource}
		</div>
	';
}


if ($kfor->lCurAction == 'showedit' && (int)$kfor->lFieldArr['length']['CurValue']) {
	if ((int)$kfor->lFieldArr['length']['CurValue'] < 60)
		$kfor->lFieldArr['tmplength']['CurValue'] = (int)$kfor->lFieldArr['length']['CurValue'];
	if ((int)$kfor->lFieldArr['length']['CurValue'] < 3600)
		$kfor->lFieldArr['tmplength']['CurValue'] = date('i:s', (int)$kfor->lFieldArr['length']['CurValue']);
	if ((int)$kfor->lFieldArr['length']['CurValue'] >= 3600)
		$kfor->lFieldArr['tmplength']['CurValue'] = date('g:i:s', (int)$kfor->lFieldArr['length']['CurValue']);
}

$preview = '';


if (($kfor->lCurAction == 'showedit' || $kfor->lCurAction == 'save') && (int)$kfor->lFieldArr['mediaguid']['CurValue']) {
	$kfor->lFieldArr['ftype']['CType'] = 'hidden';
	$preview = showPlayerByType((int)$kfor->lFieldArr['ftype']['CurValue'], $kfor->lFieldArr['mediaguid']['CurValue'], $kfor->lFieldArr['embedsource']['CurValue']);
}

$cn = Con();

$gMaxSize = 200*1024*1024;

if ($kfor->lCurAction == 'save' && $kfor->lErrorCount == 0) {
	if (!$kfor->lFieldArr['guid']['CurValue']) {
		$lSqlStr = $kfor->ReplaceSqlFields('SELECT * FROM GetMediaBaseData(null, ' . (int) $lStoryTypeDefValue . ' , ' . (int) $user->id . ', \'' . q(getlang(1)) . '\')');
		$cn->Execute($lSqlStr);
		$cn->MoveFirst();
		$kfor->lFieldArr['guid']['CurValue'] = $cn->mRs['guid'];
	}
	
	$lSaveSql = $kfor->ReplaceSqlFields($kfor->lFieldArr[$kfor->lCurAction]['SQL']);
	$cn->Execute($lSaveSql);
	$cn->MoveFirst();
	
	if ($kfor->lFieldArr['ftype']['CurValue'] == 4) { // Normal video
		$lmediaguid = 0;
		if ((int)$kfor->lFieldArr['mediaguid']['CurValue'] && !(int)$kfor->lFieldArr['imgopts']['CurValue']) {
			$sql = $kfor->ReplaceSqlFields('SELECT * FROM spMultimedia(1, {ftype}, {mediaguid}, {language}, {title}, \'\', 
				{author}, ' . (int)$user->id . ', {access}, {accesscode}, {filenameupl}, {dim_x}, {dim_y}, {length}, {guid}, {place}, {mediatxt}, {mediasize}, {srctype}, {mimetype})');
			$cn->Execute($sql);
			$cn->MoveFirst();
			$lmediaguid = (int)$kfor->lFieldArr['mediaguid']['CurValue'];
		} elseif ($_FILES['mediafile']['name']) {
			$extarray = array('.flv','.avi','.mpg','.mpeg','.wmv');
			$gFileExt = substr($_FILES['mediafile']['name'], strrpos($_FILES['mediafile']['name'], '.'));
			$isExtension = in_array(strtolower($gFileExt), $extarray);
			
			$mimearr = array('video/x-flv', 'application/x-flash-video', 'application/octet-stream', 'video/x-msvideo', 'video/mpeg', 'video/x-ms-wmv');
			$isMimeType = in_array(strtolower($_FILES['mediafile']['type']), $mimearr);
			
			if ($_FILES['mediafile']['size'] > $gMaxSize) {
				$kfor->SetError('Файл', 'Прекалено голям файл. Максимален размер: ' . $gMaxSize/1024 . ' kB');
				delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
			}
			if (!$_FILES['mediafile']['size']) {
				$kfor->SetError('Файл', 'Невалиден файл!');
				delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
			}
			
			if ($isExtension && $isMimeType) {
				if ($_FILES['mediafile']['error'] == UPLOAD_ERR_OK) {
					
					$lDelSql = $kfor->ReplaceSqlFields('SELECT * FROM DeleteRelatedItemsFromStory({guid})');
					$cn->Execute($lDelSql);//Za da moje da ima samo po 1 prika4en audio/video fail kym statiq
					
					$sql = $kfor->ReplaceSqlFields('SELECT * FROM spMultimedia(1, {ftype}, {mediaguid}, {language}, {title}, \'\', 
						{author}, ' . (int)$user->id . ', {access}, {accesscode}, \'' . q($_FILES['mediafile']['name']) . '\', {dim_x}, {dim_y}, {length}, {guid}, {place}, {mediatxt}, ' . (int)$_FILES['mediafile']['size'] . ', {srctype}, \'' . q(strtolower($_FILES['mediafile']['type'])) . '\')');
					
					$cn->Execute($sql);
					$cn->MoveFirst();
					if (!$lmediaguid = (int)$cn->mRs['guid']) {
						$kfor->SetError('Файл', 'Грешка в базата данни! - spMultimedia');
						delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
					} else {
						$kfor->lFieldArr['mediaguid']['CurValue'] = $lmediaguid;
						
						$fName = 'oo_' . $lmediaguid . $gFileExt;
						if (!move_uploaded_file($_FILES['mediafile']['tmp_name'], $gFilesRoot . $fName)) {
							$kfor->SetError('Файл', 'Грешка при качването на файл! - move_uploaded_file');
							delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
						} else {
							$lErr = 0;
							if ((int)$kfor->lFieldArr['ftype']['CurValue'] == 4) {
								$fNameFlv = 'oo_' . $lmediaguid . '.flv';
								$fNameImg = 'big_' . $lmediaguid . '.jpg';
								chdir($gFilesRoot);
								removeVideoThumbs($lmediaguid, $gFilesRoot);
								system(BINARY_FFMPEG . " -i " . $fName . " -an -ss 00:00:03 -s 480x360 -f mjpeg -an -r 1 -vframes 1 -y " . $fNameImg, $cmdres1);
								if ($gFileExt != '.flv') {
									if (is_file($fNameFlv))
										unlink($fNameFlv);
									system(BINARY_FFMPEG . " -i " . $fName . " -ar 44100 -ab 64 -f flv -s 640x480 -qscale 10 -r 20 " . $fNameFlv, $cmdres2);
									unlink($fName);
									if ($cmdres1 || $cmdres2)
										$lErr = 1;
									else
										$fSizeFlv = filesize($fNameFlv);
								} else {
									if ($cmdres1)
										$lErr = 1;
									else
										$fSizeFlv = filesize($fName);
								}
								if ($lErr) {
									$kfor->SetError('Файл', 'Грешка при конвертиране на видео файла във формат .flv');
									delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
								} else {
									$sql = $kfor->ReplaceSqlFields('SELECT * FROM spMultimedia(1, {ftype}, {mediaguid}, {language}, {title}, \'\', 
										{author}, ' . (int)$user->id . ', {access}, {accesscode}, \'' . q($fNameFlv) . '\', {dim_x}, {dim_y}, 
										{length}, {guid}, {place}, {mediatxt}, ' . (int)$fSizeFlv . ', {srctype}, \'video/x-flv\')');
									$cn->Execute($sql);
								}
							}
						}
					}
				} else {
					$kfor->SetError('Файл', 'Грешка при качването на файл! - ' . $_FILES['mediafile']['error']);
					delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
				}
			} else {
				$kfor->SetError('Файл', 'Непознат тип на файла! - ' . $_FILES['mediafile']['type']);
				delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
			}
		} else {
			if (!(int)$kfor->lFieldArr['mediaguid']['CurValue'] || (int)$kfor->lFieldArr['imgopts']['CurValue']) {
				$kfor->SetError('Файл', 'Трябва да зададете файл!');
				delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
			}
		}


		//~ echo $lmediaguid;exit;
		$isExtension = false;
		$isMimeType = false;
		
		// thumbnaila bi trqbvalo da moje se uploadne vinagi
		if ($_FILES['imgfile']['name']) {
			
			$extarray = array(".jpg", ".jpeg", ".png", ".gif");
			$gFileExt = substr($_FILES['imgfile']['name'], strrpos($_FILES['imgfile']['name'], '.'));
			$isExtension = in_array(strtolower($gFileExt), $extarray);
			
			$mimearr = array("image/jpeg", "image/gif", "image/png");
			$isMimeType = in_array(strtolower($_FILES['imgfile']['type']), $mimearr);
			
			if ($_FILES['imgfile']['size'] > $gMaxSize) {
				$kfor->SetError('Thumbnail', 'Прекалено голям файл. Максимален размер: ' . $gMaxSize/1024 . ' kB');
				delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
			}
			if (!$_FILES['imgfile']['size']) {
				$kfor->SetError('Thumbnail', 'Невалиден файл!');
				delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
			}
			
			if ($isExtension && $isMimeType) {
				if ($_FILES['imgfile']['error'] == UPLOAD_ERR_OK) {
					exec(BINARY_CONVERT . " -colorspace rgb " . $_FILES['imgfile']['tmp_name'] . " " . $gFilesRoot . "oo_" . $lmediaguid . '.jpg');
					exec(BINARY_CONVERT . " -colorspace rgb -quality 80 -thumbnail " . escapeshellarg('1024x768>') . " " . $_FILES['imgfile']['tmp_name'] . " " . $gFilesRoot . "big_" . $lmediaguid . '.jpg');
					exec(BINARY_CONVERT . " -colorspace rgb -quality 80 -thumbnail " . escapeshellarg('450x450>') . " " . $_FILES['imgfile']['tmp_name'] . " " . $gFilesRoot . "gb_" . $lmediaguid . '.jpg');
					exec(BINARY_CONVERT . " -colorspace rgb -quality 80 -thumbnail " . escapeshellarg('100x>') . " " . $_FILES['imgfile']['tmp_name'] . " " . $gFilesRoot . "s_" . $lmediaguid . '.jpg');
				} else {
					$kfor->SetError('Thumbnail', 'Грешка при качването на файл! - ' . $_FILES['imgfile']['error']);
					delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
				}
			} else {
				$kfor->SetError('Thumbnail', 'Непознат тип на файла! - ' . $_FILES['imgfile']['type']);
				delStoryRecord($cn, $kfor->lFieldArr['guid']['CurValue']);
			}
		}
	} else if ($kfor->lFieldArr['ftype']['CurValue'] == 5) { // Embed video
		$sql = $kfor->ReplaceSqlFields('SELECT * FROM spMultimedia(1, {ftype}, {mediaguid}, {language}, {title}, \'\', 
			{author}, ' . (int)$user->id . ', {access}, {accesscode}, {embedsource}, {dim_x}, {dim_y}, {length}, {guid}, {place}, {mediatxt}, {mediasize}, {srctype}, {mimetype})');
		$cn->Execute($sql);
	}
}

$html = '
	' . ($_GET['mode'] == 'rel' ? '<input name="mode" type="hidden" value="rel" />' : '') . '
{primarysite}{guid}{mediaguid}{filenameupl}{srctype}{mediasize}{mimetype}
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
			<th colspan="4">' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на видео материал</th>
		</tr>
		' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 
			'
				<tr>
					<td colspan="2" valign="top">
						<b>{*lastmod}:</b> {@lastmod}<br/>
						<b>{*createuid}:</b> {@createuid}<br/>
					</td>
					<td colspan="2" valign="top" align="right">{save} {cancel} {delete}</td>
				</tr>
			' : '
				<tr>
					<td colspan="2" valign="top">&nbsp;</td>
					<td colspan="2" valign="top" align="right">{save} {cancel}</td>
				</tr>
			'
		) . '
		<tr>
			<td colspan="2"><b>{*title}:</b><br/>{title}</td>
			<td><b>{*language}:</b><br/>{language}</td>
			<td valign="top"><b>{*pubdate}:</b> <a href="javascript: void(0);" onclick="InsertCurTime(\'pubdate\');return false;"><img src="/img/clock.gif" alt="Въведи моментното време" align="absmiddle" title="Въведи моментното време" border="0" /></a><br/>{pubdate}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*author}:</b><br/>{author}</td>
			<td valign="top"><b>{*state}:</b><br/>{state}</td>
			<td valign="top"><b>{*showforum}:</b><br/>{showforum}</td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><b>{*storydesc}:</b><br/>{storydesc}</td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
			<td colspan="2" align="right">{save} {cancel} ' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? '{delete}' : '') . '</td>
		</tr>		
		<tr>
			<th colspan="4">Мултимедия</th>
		</tr>
		<tr>
			<td valign="middle" align="center" rowspan="3">&nbsp;' . $preview . '&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" valign="top">
				<table width="100%" border="0">
					<tr>
						<td valign="top"><b>{*ftype}</b>: ' . ($kfor->lCurAction == 'showedit' ? $kfor->lFieldArr['ftype']['DisplayName'] : '') . '{ftype}</td>
					</tr>
					<tr>
						<td valign="bottom">' . $fileinput . '</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		<script language="JavaScript">showAttAccessCode();</script>
		<script language="JavaScript">displayVideoInput();</script>
	</div>
	</div>
	</div>
	</div>
</div>
</div>
</div>
</div>
';

$lSiteRights = GetSiteRights();

if ($lSiteRights[$kfor->lFieldArr['primarysite']['CurValue']] != 'edit' && $kfor->lFieldArr['guid']['CurValue']) {
	$html = '
		' . ($_GET['mode'] == 'rel' ? '<input name="mode" type="hidden" value="rel" />' : '') . '
	{primarysite}{guid}
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
				<th colspan="4">' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 'Редактиране' : 'Добавяне' ) . ' на статия</th>
			</tr>
			' . ((int)$kfor->lFieldArr['guid']['CurValue'] ? 
				'
					<tr>
						<td colspan="2" valign="top">
							<b>{*lastmod}:</b> {@lastmod}<br/>
							<b>{*createuid}:</b> {@createuid}<br/>
						</td>
						<td colspan="2" valign="top" align="right">{cancel}</td>
					</tr>
				' : '
					<tr>
						<td colspan="2" valign="top">&nbsp;</td>
						<td colspan="2" valign="top" align="right">{cancel}</td>
					</tr>
				'
			) . '
			<tr>
				<td><b>{*language}:</b><br/>{@language}</td>
			</tr>
			<tr>
				<td valign="top"><b>{*pubdate}:</b><br/>{@pubdate}</td>
			</tr>
			<tr>
				<td colspan="2" valign="top"><b>{*state}:</b><br/>{@state}</td>
			</tr>
			<tr>
				<td colspan="4" valign="top"><b>{*title}:</b><br/>{@title}</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td colspan="2" align="right">{cancel}</td>
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

if ($kfor->lFieldArr['guid']['CurValue']) {
	$chngLogArr = getStoryChangeLog($kfor->lFieldArr['guid']['CurValue']);

	$html .= '
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<fieldset class="changelog">
				<legend>' . getstr('admin.stories.changeLog') . ':</legend>
	';

	foreach($chngLogArr as $log) {
		$log['inittxt'] = ($log['init'] == 1 ? 'създава' : 'променя');
		$log['statustxt'] = $gStoriesStates[$log['status']];
		$level = ($log['status'] != $lState ? getstr('admin.stories.changeLogLevel', $log) : '');
		$html .= '<p>'. getstr('admin.stories.changeLogRow', $log) . $level . '</p>';
		$lState = $log['status'];
	}
	
	$html .= '
			</fieldset>
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>
	</div>
	</div>
	<br/>
	';
}

$kfor->lFormHtml = $html;

if ($kfor->lCurAction == 'save' && $kfor->lErrorCount == 0) {
	if ($kfor->lErrorCount == 0) {
		clearcacheditems2('stories', $kfor->lFieldArr['primarysite']['CurValue']);
	}
}

if ($kfor->lCurAction == 'delete' && (int)$kfor->lFieldArr['guid']['CurValue'] && $kfor->lErrorCount == 0) {
	delVideoFilesByStory((int)$kfor->lFieldArr['guid']['CurValue']);
	$lDelSql = $kfor->ReplaceSqlFields($kfor->lFieldArr[$kfor->lCurAction]['SQL']);
	$cn->Execute($lDelSql);
}

echo $kfor->Display();

HtmlEnd($lHide);
?>