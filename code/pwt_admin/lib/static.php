<?php
$x = session_start(); //na servera session_autostart e 0
//~ var_dump($x);
include_once(getenv("DOCUMENT_ROOT") . "/lib/conf.php");
require_once(PATH_ECMSFRCLASSES . 'static.php');
include_once(getenv("DOCUMENT_ROOT") . "/lib/struct.php");
include_once(getenv("DOCUMENT_ROOT") . "/lib/cuser.php");
require_once(PATH_CLASSES . 'static.php');


$gSiteStruct = GetSitesStruct();
$gSiteAccess = GetAccess();


$gUrl = substr(getenv('SCRIPT_NAME'), 0, strrpos(getenv('SCRIPT_NAME'), '/') + 1);
$gSiteAccessType = $gSiteAccess[$gUrl];
if (!$gSiteAccessType) $gSiteAccessType = 0;



$gStoriesStates = array (
	0 => 'Пишеща се',
	1 => 'За коректор',
	2 => 'За редактор',
	3 => 'Публикувана',
	//~ 4 => 'Публикувана (За корекция и преглед)',
);

$gImageExtensions = array (
	1=> '.gif',
	2=> '.png',
);

$gFightDscids = array();

function HtmlStart($Hide = 0) {
	global $user, $gUrl;
	
	ProccessHistory();

	echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . getlang(true) . '" lang="' . getlang(true) . '">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Etaligent CMS Administration</title>
		<meta name="description" content="Etaligent CMS Administration" />
		<meta name="keywords" content="Etaligent CMS Administration" />
		<meta name="author" content="Etaligent.net"/>
		<meta name="robots" content="index,follow" />
		<meta name="language" content="' . getlang(true) . '" />
		<meta name="generator" content="Etaligent.Net CMS" />
		<link rel="SHORTCUT ICON" href="/favicon.ico" type="image/x-icon"/>
		<link rel="ICON" href="/favicon.ico" type="image/x-icon"/>			
		<link type="text/css" rel="stylesheet" href="/lib/def.css" media="all" title="default" />
		<link type="text/css" rel="stylesheet" href="/lib/orange.css" media="all" title="orange" />
		<link type="text/css" rel="stylesheet" href="/lib/green.css" media="all" title="green" />
		<link type="text/css" rel="stylesheet" href="/lib/black.css" media="all" title="black" />
		<link type="text/css" rel="stylesheet" href="/lib/def.css" media="all" title="blue" />			
		<script type="text/javascript" language="JavaScript">
			var gSiteUrl = \'' . ADM_URL .  '\';
		</script>
		<script type="text/javascript" language="JavaScript" src="/lib/def.js"></script>						
		<script type="text/javascript" language="JavaScript" src="/lib/jquery.js"></script>
		<script type="text/javascript" language="JavaScript" src="/lib/jquery_form.js"></script>
		<script type="text/javascript" language="JavaScript" src="/lib/jquery_table_dnd.js"></script>
		
		
		<script type="text/javascript" language="JavaScript" src="/lib/HtmlText/Screen.js"></script>
		<script type="text/javascript" language="JavaScript" src="/lib/HtmlText/common.js"></script>
		<script type="text/javascript" language="JavaScript" src="/lib/HtmlText/HtmlText.js"></script>
		<script type="text/javascript" language="JavaScript" src="/lib/HtmlText/HtmlTextInitializer.js"></script>
	</head>
	<body>
	<div id="wrapper">
	';
	if (!$Hide) {
		echo '
		<div id="header">
			<a href="/" id="logo"></a>
			' . ($user ? '
				' . getstr('admin.hello') . ' ' . $user->fullname . '
				[ <a href="/profile/passwd/">' . getstr('admin.changePass') . '</a> | 
				<a href="/login/index.php?l=1">' . getstr('admin.logOut') . '</a> ]
			' : '
				' . getstr('admin.pleaseLogin') . '
			') . '
		</div>
		' . DisplayMenu('/') . '
		<script language="JavaScript">ActivateMenu(\'menu\');</script>
		
		<div class="unfloat"></div>
		<div id="main"' . ($Hide ? ' class="noMarginMain"' : '') . '>
		';
	}	
	UserRedir($user);	}


function HtmlEnd($Hide = 0) {
	global $gEcmsLibRequest;
	if( !$gEcmsLibRequest ){		
		UpdateHistory();
		
		
		if (!$Hide) {
			echo '
				<div class="unfloat"></div>
				<br/>
			</div>
			<div class="unfloat"></div>
			<div id="footer">
				' . getstr('admin.copywright') . '
			</div>
			</div>
			<div id="bg_shadow"></div>
			<div id="loading_shadow"></div>
			<div id="finalize_form"></div>
		' . returncalendarlayers() . '
		</body>
		</html>';
		}
	}else{
		DisplayJSONOrganizer();
	}
}

function nullstr($pParam) {
	if ($pParam) {
		return '\'' . q($pParam) . '\'';
	} else {
		return 'NULL';
	}
}

function IsInt($pVal) {
	if (is_numeric($pVal))
		if (doubleval($pVal) - intval($pVal) == 0)
			return 1;
	return 0;
}

function ErrOut($pStr) {
	echo '<p style="color: Red; font-size: 12pt;"><b>ERROR: </b> ' . $pStr;
}

function UserRedir($pUser) {
	global $gUrl, $gSiteAccess;
	if (!$gSiteAccess[$gUrl]) {
		if (!$pUser) {
			$url = getenv("REQUEST_URI");
			header('Location: /login/index.php?url=' . urlencode($url));			
			exit;
		} else {
			header('Location: /error.php');			
			exit;
		}
	}
}

function CountOnBits($int){
	$r=0;
	while($int>0){
		if($int % 2) $r++;
		$int>>=1;
	}
	return $r;
}

function ckdt1($pStr, $pCheck = 1) {
	if (!preg_match('/[\/\\\.\-]/', $pStr, $lMatches)) {
		return false;
	}
	
	$lSeparator = $lMatches[0]; //Kato nqma skobi v reg expa v 0-q element e kakvoto e machnalo
	
	if (!preg_match('/^(\d{1,2})\\' . $lSeparator .  '(\d{1,2})\\' . $lSeparator . '(\d{2,4})$/i', $pStr, $lMatches)) {
		return false;
	}
	
	if (!checkdate($lMatches[2], $lMatches[1], $lMatches[3])) {
		return false;
	}
	
	if (!$pCheck) {return $lMatches;}
	else {return true;}
}

function getSiteName($pRs) {
	global $gSiteArr;
	return $gSiteArr['name'][$pRs['primarysite']];
}
function getSiteDir($pRs) {
	global $gSiteArr;
	return $gSiteArr['dir'][$pRs['primarysite']];
}

function getsubnav($navarr, $p) {
	global $gSiteTabsHide;
	
	$r = '<table class="tabtable"><tr>';
	foreach ($navarr as $k => $v) {
		if (!is_array($gSiteTabsHide) || !in_array($k, $gSiteTabsHide)) {
			$r .= '<td><div' . ($k == $p ? ' class="seltab"' : '') . '><a href="' . $v[0] . '">' . $v[1] . '</a></div></td>';
		}	
	}
	$r .= '</tr></table>';
	return $r;
}

function showRelatedItems($guid, $sid = 0) {
	echo '
	<div id="framesholder">
		<iframe class="rels" border="0" id="relsframe" name="relsframe" src="/resources/stories/relstories.php?guid='.$guid.'&sid='.$sid.'"></iframe>	
		<iframe class="rels" border="0" id="linkframe" name="linkframe" src="/resources/stories/relinks.php?guid='.$guid.'"></iframe>	
		<iframe class="rels" border="0" id="picsframe" name="picsframe" src="/resources/stories/relpics.php?guid='.$guid.'&sid='.$sid.'"></iframe>
		<iframe class="rels" border="0" id="attframe" name="attframe" src="/resources/stories/relattachments.php?guid='.$guid.'&sid='.$sid.'"></iframe>
		<iframe class="rels" border="0" id="medframe" name="medframe" src="/resources/stories/relmedia.php?guid='.$guid.'"></iframe>
	</div>
	';
}

function checkplace($pRs) {
	$lArr = array (
		0 => "Не се показва в статията",
		1 => "Горе Дясно",
		2 => "Горе Ляво",
		3 => "Долу",
		4 => "Голяма снимка",
	);
	if ($pRs['frst'])
		return $lArr[$pRs['place']] . ' (<span style="color:red">При заглавието</span>)';
	 else 
		return $lArr[$pRs['place']];
}

function retcalico($pfield, $pform='def1') {
	return returncalendarico($pfield, $pform);
}
function returncalendarico($pfield, $pform='def1') {
	return '<a href="#" onclick="jscalshow(this, \'' . $pform . '\', \'' . $pfield . '\'); return false;"><img src="/img/calico.gif" border="0"/></a>';
}
function returncalendarlayers() {
	return '<div id="calid" style="z-index: 2; display: none; position: absolute; width: 150px; height: auto; background-color: White;"></div>
		<iframe id="calidfrm" style="z-index: 1; display: none; position: absolute; left: 0px; top: 0px;" src="javascript:false;" frameborder="0" scrolling="no"></iframe>';
}

function isAdmin() {
	global $user;
	if (in_array('Admin', $user->grpnames)) return 1;
	return 0;
}

function GetSiteRights() {
	global $user;
	static $lSiteRights;
	
	if (!is_array($lSiteRights) || count($lSiteRights) == 0) {
		foreach ($user->arrPerm as $url => $actipe) {
			if (preg_match('/\*sid(\d+)/', $url, $mm)) {
				if ($actipe > 1) {
					$lSiteRights[$mm[1]] = 'edit';
				} else {
					$lSiteRights[$mm[1]] = 'view';
				}
			}
		}
	}
	
	return $lSiteRights;
}

function SqlProvIn($fld = "provider_id") {
	$lProvRights = ProviderRights();
	if (!isAdmin()) {
		if (!is_array($lProvRights)) $lProvRights = array();
		foreach ($lProvRights as $k => $v) $instr[] = $k;
		
		if (count($instr) > 0) {
			$instr = implode(', ', $instr);
			return $fld . ' IN (' . $instr . ')';
		}
	}
	return ' true ';
}

// FUNKCII ZA escape na adresi (preobrazuvane url-ta i email adresi v linkove); preobrazuvane na specialnite kavichki v normalni
function parseUrls($txt) {
	$parsed = '';
	$lines = preg_split('/<br \/>/', $txt);
	foreach ($lines as $line) {
		$parsedLine = '';
		if ($line) {
			$words = preg_split('/[\ ]/', $line);
			foreach ($words as $word) {
				$w = parseWordUrls($word);
				$parsedLine .= $w.' ';
			}
		}
		$parsed .= trim($parsedLine) . '<br />';
	}
	return $parsed;
}

function parseWordUrls($str) {
	$delim = '[\(\)\[\]\{\}\<\>\"\'\/\\\\.,;?!]*';
	$left_delim = $delim; 	// = '[\(\[\{\<\"\']*';
	$right_delim = $delim; 	// = '[\)\]\}\>\"\']*';
	$url_pattern = '/^'.$left_delim.'((http|https|ftp|gopher|news):\/\/|www\.)/';
	$email_pattern = '/^'.$left_delim.'[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.(?:[A-Za-z]{2}|com|org|net|biz|info|name|aero|jobs|museum)'.$right_delim.'$/';
	
	if (preg_match($url_pattern, $str)) {
		$address = preg_replace('/^'.$left_delim.'(http:\/\/)?(www\.)?(.+?)'.$right_delim.'$/', 'http://${2}${3}', $str);
		$addr_orig = preg_replace('/^'.$left_delim.'(.+?)'.$right_delim.'$/', '${1}${2}', $str);
		$link = '<a href="' . trim($address) . '" target="_blank" >'. $addr_orig .'</a>';
		$res = preg_replace('/^('.$left_delim.')(.+?)('.$right_delim.')$/', '${1}'. $link .'${3}', $str);

	} elseif (preg_match($email_pattern, $str)) {
		$address  = preg_replace('/^'.$left_delim.'(.+?)'.$right_delim.'$/', '${1}', $str);
		$link = '<a href="mailto:' . trim($address) .'">'. $address .'</a>';
		$res = preg_replace('/^('.$left_delim.')(.+?)('.$right_delim.')$/', '${1}'.$link.'${3}', $str);		
	} else {
		$res = $str;		
	}
	return $res;
}

function parseSpecialQuotes($str) {
	return str_replace(array('&bdquo;', '„', '“', '”', '&laquo;', '&raquo;', '&ldquo;', '&rdquo;'), array('"', '"', '"', '"', '"', '"', '"', '"'), $str);
}

function getStoryChangeLog($pGuid) {
	if (!$pGuid) {
		return array();
	}
	$sql = 'SELECT s.modtime, s.status, usr.uname, s.init
				FROM storychangelog s JOIN usr ON (usr.id = s.userid) 
				WHERE guid = '. $pGuid .'
				ORDER BY s.modtime';
	$cn = Con();
	$cn->Execute($sql);
	$cn->MoveFirst();
	$res = array();
	while (!$cn->Eof()) {
		$modtime = preg_replace('/(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2})\.\d+/', 
							 '$3-$2-$1 / $4:$5', $cn->mRs['modtime']);
		$uname = $cn->mRs['uname'];
		$status = $cn->mRs['status'];
		$init = $cn->mRs['init'];
		$cn->MoveNext();
		$res[] = array(
			'modtime' => $modtime, 
			'uname' => $uname, 
			'status' => $status,
			'init' => $init
		);
	}
	return $res;
}

function matchTextUrls($text) {
	$str = preg_replace('/\r*\n/', " \n ", $text);
	$words = preg_split('/[\ ]+/', $str);
	foreach ($words as $word) {
		$w = matchWordUrls($word);
		$replaced .= $w.' ';
	}
	return nl2br($replaced);
}

function matchWordUrls($str) {
	$delim = '[\(\)\[\]\{\}\<\>\"\'\/\\\\.,;!?]*';
	$left_delim = $delim; 	// = '[\(\[\{\<\"\']*';
	$right_delim = $delim; 	// = '[\)\]\}\>\"\']*';
	$url_pattern = '/^'.$left_delim.'((http|https|ftp|gopher|news):\/\/|www\.)/i';
	$email_pattern = '/^'.$left_delim.'[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.(?:[A-Za-z]{2}|com|org|net|biz|info|name|aero|jobs|museum)'.$right_delim.'$/i';
	
	if (preg_match($url_pattern, $str)) {
		$address = preg_replace('/^'.$left_delim.'(http:\/\/)?(www\.)?(.+?)'.$right_delim.'$/i', 'http://${2}${3}', $str);
		$addr_orig = preg_replace('/^'.$left_delim.'(.+?)'.$right_delim.'$/i', '${1}${2}', $str);
		$link = '<a href="' . h(trim($address)) . '" target="_blank" >'. $addr_orig .'</a>';
		$res = preg_replace('/^('.$left_delim.')(.+?)('.$right_delim.')$/i', '${1}'. $link .'${3}', $str);		

	} elseif (preg_match($email_pattern, $str)) {
		$address  = preg_replace('/^'.$left_delim.'(.+?)'.$right_delim.'$/i', '${1}', $str);
		$link = '<a href="mailto:' . h(trim($address)) .'">'. $address .'</a>';
		$res = preg_replace('/^('.$left_delim.')(.+?)('.$right_delim.')$/i', '${1}'.$link.'${3}', $str);			
	
	} else {
		$res = $str;
	}
	return $res;
}

function convertKwds($pKwds) {
	if ($pKwds) {
		$KWArr = explode(',', $pKwds);
		$kwds1 = array();
		$kwds2 = array();
		foreach ($KWArr as $kwd) {
			$kwd = trim(UnicodeToWin($kwd));
			$kwdFirstCh = substr($kwd, 0, 1);
			if ((ord($kwdFirstCh) >= 65 && ord($kwdFirstCh) <= 90) || (ord($kwdFirstCh) >= 176 && ord($kwdFirstCh) <= 223)) {
				$kwds2[] = $kwd;
			} else {
				$kwds1[] = $kwd;
			}
		}
		$resKwdsStr = WinToUnicode(implode(', ', array_merge($kwds1, $kwds2)));
	}
	return $resKwdsStr;
}

function StoryUsage($pGuid) {
	global $user;
	
	$con = Con();
	$con->Execute('SELECT * 
		FROM spStoryUsage(' . (int)$pGuid . ', ' . (int)$user->id . ', \'' . q($user->uname) . '\') 
		WHERE uid <> ' . (int)$user->id
	);
	
	$con->MoveFirst();
	
	if (!$con->RecordCount()) return;
	$ret = '<span>В последните 10 мин тази статия е била отваряна от:</span> ';
	$i = 1;
	while (!$con->Eof()) {
		$ret .= ' ' . $con->mRs['uname'];
		$ret .= ($i != $con->RecordCount() ? ',' : '');
		$i ++;
		$con->MoveNext();
	}
	return '<div id="underconstruction">' . $ret . '</div>';
}

function br2nl($str) {
	return preg_replace('/\<br[\s\/]*\>/', "\n", $str);
}

function GetState($state) {
	if ((int)$state) return 'Активен';
	return 'Неактивнен';
}

function jsGetState($state) {
	return escapeForJS(GetState($state));
}

function escapeForJS($str) {
	if (!$str) return '&nbsp;';
	return nl2br(addslashes(htmlspecialchars($str)));
}

function TrueOrFalse($bool) {
	if ($bool == 'false') return 'НЕ';
	return 'ДА';
}

function jsTrueOrFalse($bool) {
	return escapeForJS(TrueOrFalse($bool));
}

function GetUserType($utype) {
	if ((int)$utype == 1) return 'Power user';
	return 'Обикновен';
}

function jsGetUserType($utype) {
	return escapeForJS(GetUserType($utype));
}

function GetPriceType($pType) {
	if ((int)$pType == 1) return 'Процент';
	return 'Цена';
}

function FormatPgArray($arr) {
	$arr = preg_replace('/{(.+)}/', "\\1", $arr);
	$arr = array_map('trim', explode(',', $arr));
	return implode('<br/>', $arr);
}

function jsFormatPgArray($arr) {
	$arr = preg_replace('/{(.+)}/', "\\1", $arr);
	$arr = array_map('trim', explode(',', $arr));
	$arr = array_map('escapeForJS', $arr);
	return implode('<br/>', $arr);
}

function getMMThumb($pRs) {
	$gFilesRoot = PATH_DL;
	if ((int)$pRs['guid'] && is_file($gFilesRoot . 'o_' . $pRs['guid'] . '.jpg')) 
		return '<br/><a href="./multimedia.php?guid=' . $pRs['guid'] . '&tAction=edit"><img src="/showimg.php?f=m80_' . $pRs['guid'] . '.jpg" alt="" /></a>';
	return '';
}

function mmFtype($pRs) {
	if ($pRs['ftype'] == 3) return 'audio';
	if ($pRs['ftype'] == 4) return 'video';
	return '';
}

function mmModDate($pRs) {
	if (!preg_match('/(\d+)\/(\d+)\/(\d+) (\d+):(\d+)/', $pRs['lastmod'], $a)) {
		return $pRs['lastmod'];
	}
	$tstamp = mktime($a[4], $a[5], $a[6], $a[2], $a[1], $a[3]);
	return date('d/m/Y H:i', $tstamp);
}

function GetStoryStaus($state) {
	global $gStoriesStates;
	return $gStoriesStates[$state];
}

function clearcacheditems2($pType, $pSiteId = null) {
	if (!$pSiteId) {
		$a = glob(PATH_CACHE . '/*');
		foreach($a as $f) {
			$dir = PATH_CACHE . basename($f);
			touchDirFiles($dir, $pType);
		}
	} else {
		$dir = PATH_CACHE . '/' . $pSiteId . '/';
		touchDirFiles($dir, $pType);
	}
}


function touchDirFiles($pDir, $pType) {
	if (is_dir($pDir)) {
		if ($dh = opendir($pDir)) {
			while (($file = readdir($dh)) !== false) {
				if (strpos($file, $pType.'_') == 0) {
					touch($pDir . $file, strtotime('1/1/2000'));
				}
			}
			closedir($dh);
		}
	}
	if (is_file($pDir)) {
		if (strpos(basename($pDir), $pType.'_') == 0) {
			touch($pDir, strtotime('1/1/2000'));
		}
	}
}


function showRelatedPhotos($guid, $sid = 0) {
	echo '
	<div id="framesholder">
		<iframe class="rels" border="0" id="picsframe" style="height:500px;" name="picsframe" src="/resources/gallery/relpics.php?guid='.$guid.'&sid='.$sid.'"></iframe>
	</div>
	';
}

// s tazi funkciq se mahat bom markerite (utf-8 cookie-tata) ot string
function replaceBom($pStr) {
	return str_replace(chr(239) || chr(187) || chr(191), '', $pStr);
}

function removeUplFiles($pId, $pFilesRoot) {
	$lFiles = glob($pFilesRoot . '*_' . $pId . '.*');
	foreach ($lFiles as $lFile) {
		if (is_file($lFile))
			unlink($lFile);
	}
}

function removeVideoThumbs($pId, $pFilesRoot) {
	$lFiles = glob($pFilesRoot . '*_' . $pId . '.jpg');
	foreach ($lFiles as $lFile) {
		if (is_file($lFile))
			unlink($lFile);
	}
}

function delStoryFile($pStoryId) {
	if ((int)$pStoryId) {
		$lStoryFile = PATH_STORIES . (int)$pStoryId . '.html';
		if (is_file($lStoryFile))
			return unlink($lStoryFile);
	}
	return false;
}

function delStoryRecord($pCn, $pStoryId) {
	if ((int)$pStoryId && is_object($pCn) && $pCn instanceof DBCn) {
		$pCn->Execute('SELECT * FROM deleteVideo(' . (int)$pStoryId . ')');
	}
}

function delVideoFilesByStory($pStoryId) {
	$lCn = Con();
	$lCn->Execute('SELECT valint AS videoid FROM storyproperties WHERE propid = 13 AND guid = ' . (int)$pStoryId);
	$lCn->MoveFirst();
	$lVideoId = (int)$lCn->mRs['videoid'];
	if ($lVideoId) {
		removeUplFiles($lVideoId ,PATH_DL);
	}
}

function showPlayerByType($pFtype, $pId, $pHtml = false) {
	$lFtype = (int)$pFtype;
	$lId = (int)$pId;
	
	if ($lFtype && $lId) {
		switch ($lFtype) {
			case 3: // Audio
				return '
					<object style="vertical-align: middle;" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="150" height="20">
						<param name="movie" value="/singlemp3player.swf?file=' . ADM_URL . GETATT_URL . 'o_' . $lId . '.mp3&songVolume=80&showDownload=false" />
						<param name="quality" value="high">
						<param name="wmode" value="transparent" />
						<embed
							style="vertical-align: middle;"
							width="150"
							height="20"
							src="/singlemp3player.swf?file=' . ADM_URL . GETATT_URL . 'o_' . $lId . '.mp3&songVolume=80&showDownload=false"
							quality="high"
							wmode="transparent"
							type="application/x-shockwave-flash"
							pluginspage="http://www.macromedia.com/go/getflashplayer"
						/>
					</object>
				';
				break;
			case 4: // Video
				return '
					<embed
						width="360"
						height="270"
						src="' . SITE_URL . '/mediaplayer.swf"
						quality="high"
						wmode="transparent"
						flashvars="width=360&amp;height=270&amp;autostart=false&amp;file=' . ADM_URL . GETATT_URL . 'oo_' . $lId . '.flv&amp;type=flv&amp;repeat=false&amp;image=' . ADM_URL . SHOWIMG_URL . 'big_' . $lId . '.jpg&amp;showdownload=false&amp;link=' . ADM_URL . GETATT_URL . 'oo_' . $lId . '.flv&amp;allowfullscreen=true&amp;showdigits=true&amp;shownavigation=true&amp;logo=&amp;largecontrols=false&amp;backcolor=0xffffff&amp;frontcolor=0x000000&amp;lightcolor=0x000000&amp;screencolor=0x000000"
						menu="false"
						allowfullscreen="true"
						loop="false"
						play="false"
						type="application/x-shockwave-flash"
						pluginspage="http://www.macromedia.com/go/getflashplayer"
					/>
				';
				break;
			case 5: // Embed video
				return $pHtml;
				break;
			default:
				break;
		
		}
	}
}

function showProductRelatedItems($guid, $sid = 0) {
	echo '
		<div id="framesholder">
			<iframe class="rels" width="100%" height="250px" border="0" id="picsframe" name="picsframe" src="/store/products/relpics.php?guid='.$guid.'&sid='.$sid.'"></iframe>
			<iframe class="rels" width="100%" height="120px" border="0" id="medframe" name="medframe" src="/store/products/relproducts.php?id='.$guid.'"></iframe>
		</div>
	';
}

function positive ($pArg){
	if($pArg > 0) return 1;
	return 0;
}

function checkplace2($pRs) {
	$lArr = array (
		3 => "Галерия",
	);
	if ($pRs['frst'])
		return $lArr[$pRs['place']] . ' (<span style="color:red">При заглавието</span>)';
	 else 
		return $lArr[$pRs['place']];
}

function getJSONOrganizer() {
	global $gJSONOrganizer;
	if (get_class($gJSONOrganizer) != 'JSONOrganizer') {
		$gJSONOrganizer = new JSONOrganizer();		
	}
	return $gJSONOrganizer;
}

function DisplayJSONOrganizer(){
	$lJSONOrganizer = getJSONOrganizer();
	$lJSONOrganizer->Display();
}

function putUnfloat($pRownum, $pItemsonrow){
	if( ($pRownum % $pItemsonrow) == 0 )
		return '<div class="unfloat"></div>';
}

function CutText($d, $len = 80) {
	$d = strip_tags($d);
	if (mb_strlen($d, 'UTF-8') < $len) return $d;
	$cut = mb_substr($d, 0, $len, 'UTF-8');
	return mb_substr($cut, 0, mb_strrpos($cut, ' ', 'UTF-8'), 'UTF-8') . '...';
}


function showYesNo($pParam){
	if((int)$pParam )
		return getstr('global.yes');
	return getstr('global.no');
}

function parseDate($pDate){
	if(!preg_match('/(?P<day>\d{1,2})\s*(?P<month>[a-z]+)\s*(?P<year>\d{2,4})/uims', $pDate, $pMatch))
		return false;
	return $pMatch;
}

function getDateDay($pDate){
	$lResult = parseDate($pDate);
	if( $lResult !== false )
		return $lResult['day'];
	return '';
}

function getDateMonth($pDate){
	$lResult = parseDate($pDate);
	if( $lResult !== false )
		return $lResult['month'];
	return '';
}

function getDateYear($pDate){
	$lResult = parseDate($pDate);
	if( $lResult !== false )
		return $lResult['year'];
	return '';
}

function UploadPic($pName, $pDir, $pPicId, &$pError) {
	$gMaxSize = 100*1024*1024; // 100 MB	
	//~ $gMaxSize = 500 // Za testove;
	$extarray = array(".jpeg", ".jpg", ".gif", ".tif", ".tiff", ".bmp", ".png");
	$typearr = array("image/pjpeg", "image/jpeg", "image/gif", "image/tiff", "image/png", "image/bmp");
	$imgUploadErr = 1;
	$lPicId = 0;
	
	$lCn = Con() ;
	if ( $_FILES[$pName]['name'] ) {
		
		$pFnUpl = $_FILES[$pName]['name'];
		$pTitle = $pFnUpl;
		
		$gFileExt = substr($_FILES[$pName]['name'], strrpos($_FILES[$pName]['name'], '.'));
		$isImageExtension = in_array(strtolower($gFileExt), $extarray);
		$isImageMime = in_array(strtolower($_FILES[$pName]['type']), $typearr);
		
		if ($isImageExtension && $isImageMime) {
			if ($_FILES[$pName]['size'] > $gMaxSize) {
				$pError = getstr('admin.articles.error_picTooBigMaxSize')  . ($gMaxSize / (1024 * 1024)). ' MB';
			} elseif (!$_FILES[$pName]["size"]) {
				$pError = getstr('admin.articles.error_wrongFile');
			} elseif ($_FILES[$pName]['error'] == UPLOAD_ERR_OK) {				
				$lResult = $lCn->Execute('SELECT PicsUpload(1, ' . (int) $pPicId. ', 0,\'' . q($pTitle) . '\', \'' . q($pFnUpl) . '\', \'' . q($PicText) . '\') as picid');
				
				if( $lResult ){
					$lCn->MoveFirst();
					$lPicId = (int)$lCn->mRs['picid'];
					
					if ($lPicId) {
						if (!move_uploaded_file($_FILES[$pName]['tmp_name'], $pDir . $lPicId . $gFileExt)) {
							$pError = getstr('admin.articles.error_error') . $_FILES[$pName]['error'];
						} else { 
							// Vsichko e ok... pravim jpg i mahame originala
							DeletePicFiles( $lPicId );
							exec(escapeshellcmd("convert -colorspace rgb -quality 80 " . $pDir . $lPicId . $gFileExt . " " . $pDir . 'oo_' . $lPicId . '.jpg' ));
							exec("convert -colorspace rgb -quality 80 -thumbnail " . escapeshellarg('1024x1024>') . " " . $pDir . $lPicId . $gFileExt . " " . $pDir . 'big_' . $lPicId . '.jpg' );
							unlink($pDir . $lPicId . $gFileExt);
							$imgUploadErr = 0;
						}
					} else {
						$pError = getstr('admin.articles.error_dbError');
					}
				}else{
					//~ var_dump($lCn->mErrMsg);
					$pError = $lCn->mErrMsg ? $lCn->mErrMsg : getstr('admin.articles.error_dbError');
				}
			} else {
				$pError = getstr('admin.articles.error_errorWhileSavingFile');
			}
		} else {
			$pError = getstr('admin.articles.error_wrongFileFormatAllowedFormatsAre') . implode(' ', $extarray);
		}
	} else {
		$pError = getstr('admin.articles.error_noFileUploaded');
	}
	
	if (!$imgUploadErr) 
		return $lPicId;
	
	if ($lPicId) //Mahame snimkata pri greshka
		$lCn->Execute('SELECT PicsUpload(3, ' . (int)$lPicId . ', null, null, null, null);');
	return false;
}

function DeletePic($pPicId){
	$lCn = Con();
	$lCn->Execute('SELECT PicsUpload(3, ' . (int)$pPicId . ', null, null, null, null);');
	DeletePicFiles($pPicId);
}

function DeletePicFiles($pPicId){
	if( !$pPicId )
		return;
	$lFormats = array('gif', 'jpg');
	foreach($lFormats as $lFormat){
		$lPrefixes = array('oo', 'big', 'gb', 's', 'mx50', 'm80', 'd200x150');
		foreach( $lPrefixes as $lPrefix){
			$lFile = PATH_DL . $lPrefix . '_' . $pPicId . '.' . $lFormat;
			if( file_exists($lFile) )
				unlink($lFile);
		}
	}
}


//Poneje ne e na nash syrvyr ne sa pozvoleni exec i system i polzvame perl script koito execva komandite
function executeConsoleCommand($pCommand, $pArgs, $pUsePassThru = 1){	
	if( !is_array($pArgs))
		$pArgs = array();
	/**
		Масив с кодовете на операциите
	*/
	if( (int) USE_PERL_EXECS ){
		$lCommandsArr = array(
			1 => 'convert',
		);
		$lPerlPhpAddress = PERL_EXEC_ADDRESS;
		
		$lCommandFound = false;
		$gCommandCode = -1;
		foreach( $lCommandsArr as $lCommandCode => $lCommandName ){
			if( $lCommandName == $pCommand ){
				$lCommandFound = true;
				$gCommandCode = $lCommandCode;
			}
		}
		if( !$lCommandFound )
			return;
		
		$lUrl = $lPerlPhpAddress . '?command=' . $gCommandCode . '&argsCount=' . (int) count($pArgs);
		
		$lArgIter = 1;
		foreach( $pArgs as $lCommandArgument ){
			$lUrl .= '&arg' . $lArgIter . '=' . rawurlencode($lCommandArgument);
			$lArgIter++;
		}
		//~ var_dump(file_get_contents($lUrl));
		return file_get_contents($lUrl);
	}else{
		$lCommandToExecute = $pCommand;
		foreach( $pArgs as $lCommandArgument ){
			$lCommandToExecute .= ' ' .$lCommandArgument;
		}		
		if( $pUsePassThru ){
			ob_start();			
			passthru($lCommandToExecute);
			$lContent = ob_get_contents();
			ob_clean();
			return $lContent;
		}else{
			exec($lCommandToExecute);
		}
	}
}

/*
	Сваля картинката в указания файл
*/	
function downloadImage($pFileName, $pImageUrl){
	$lPicContent = file_get_contents($pImageUrl);
	return file_put_contents($pFileName, $lPicContent);
}

//Изкарва списък с всички field-ове на този обект
function GetObjectFields($pObjectId){
	if(!$pObjectId)
		return;
	$lTempls = array(
		G_ROWTEMPL => 'objects.fieldListRow',
		G_HEADER => 'objects.fieldListHead',
		G_STARTRS => 'objects.fieldListStart',
		G_ENDRS => 'objects.fieldListEnd',
		G_FOOTER => 'objects.fieldListFoot',
		G_NODATA => 'objects.fieldListNoData',
	);
		
	$lObject = new crs(array(
		'object_id' => $pObjectId,
		'sqlstr' => 'SELECT of.id, f.name as field_name,of.field_id, h.name as control_type, of.label, of.allow_nulls::int 
			FROM object_fields of
			JOIN fields f ON f.id = of.field_id
			JOIN html_control_types h ON h.id = of.control_type
			WHERE of.object_id = ' . (int)$pObjectId . '
		',
		'templs' => $lTempls,		
	));	
	return $lObject->Display();
}

//Изкарва списък с всички подобекти на този обект
function GetObjectSubobjects($pObjectId){
	if(!$pObjectId)
		return;
	$lTempls = array(
		G_ROWTEMPL => 'objects.subobjectListRow',
		G_HEADER => 'objects.subobjectListHead',
		G_STARTRS => 'objects.subobjectListStart',
		G_ENDRS => 'objects.subobjectListEnd',
		G_FOOTER => 'objects.subobjectListFoot',
		G_NODATA => 'objects.subobjectListNoData',
	);
		
	$lObject = new crs(array(
		'object_id' => $pObjectId,
		'sqlstr' => 'SELECT of.id, f.name as object_name, of.subobject_id, of.min_occurrence, of.initial_occurrence, of.max_occurrence
			FROM object_subobjects of
			JOIN objects f ON f.id = of.subobject_id			
			WHERE of.object_id = ' . (int)$pObjectId . '
		',
		'templs' => $lTempls,		
	));	
	return $lObject->Display();
}

//Изкарва списък с всички обекти на този темплейт
function GetTemplateObjects($pTemplateId){
	if(!$pTemplateId)
		return;
	$lTempls = array(
		G_ROWTEMPL => 'templates.objectListRow',
		G_HEADER => 'templates.objectListHead',
		G_STARTRS => 'templates.objectListStart',
		G_ENDRS => 'templates.objectListEnd',
		G_FOOTER => 'templates.objectListFoot',
		G_NODATA => 'templates.objectListNoData',
	);
		
	$lObject = new crs(array(
		'template_id' => $pTemplateId,
		'sqlstr' => 'SELECT of.id, f.name as object_name, of.object_id, of.pos, of.display_in_tree::int as display_in_tree, pos, char_length(pos) / 2 as level,
			(SELECT coalesce(of1.id) FROM pwt.template_objects of1 WHERE of1.template_id = of.template_id AND of1.pos = substring(of.pos, 1, char_length(of.pos) -2)) as parent_id,
			(SELECT count(*) as children_count FROM pwt.template_objects of2 WHERE of2.template_id = of.template_id AND substring(of2.pos, 1, char_length(of.pos)) = of.pos AND char_length(of2.pos) = char_length(of.pos) + 2) as children_count,
			of.allow_movement::int as allow_movement, of.allow_add::int as allow_add, of.allow_remove::int as allow_remove, of.display_title_and_top_actions::int as display_title_and_top_actions,
			of.display_default_actions::int as display_default_actions, ts.name as title_style 
			FROM template_objects of			
			JOIN object_title_display_style ts ON ts.id = of.title_display_style
			JOIN objects f ON f.id = of.object_id			
			WHERE of.template_id = ' . (int)$pTemplateId . ' AND char_length(pos) = 2
			ORDER BY of.pos
		',
		'subobject_external_link' => 1,
		'templs' => $lTempls,		
	));	
	return $lObject->Display();
}

//Изкарва списък с всички подобекти за този обект на темплейт
//Важно е да се отбележи че pTemplateObjectId отговаря на полето id в таблицата template_objects
function GetTemplateObjectSubobjects($pTemplateObjectId){
	if(!$pTemplateObjectId)
		return;
	$lTempls = array(
		G_ROWTEMPL => 'templates.objectListRow',
		G_HEADER => 'templates.objectSubobjectListHead',
		G_STARTRS => 'templates.objectSubobjectListStart',
		G_ENDRS => 'templates.objectSubobjectListEnd',
		G_FOOTER => 'templates.objectListFoot',
		G_NODATA => 'templates.objectSubobjectListNoData',
	);
		
		
	$lObject = new crs(array(		
		'sqlstr' => 'SELECT of.id, f.name as object_name, of.object_id, of.pos, of.display_in_tree::int as display_in_tree, char_length(of.pos) / 2 as level,
			(SELECT coalesce(of1.id) FROM pwt.template_objects of1 WHERE of1.template_id = of.template_id AND of1.pos = substring(of.pos, 1, char_length(of.pos) -2)) as parent_id,
			(SELECT count(*) as children_count FROM pwt.template_objects of2 WHERE of2.template_id = of.template_id AND substring(of2.pos, 1, char_length(of.pos)) = of.pos AND char_length(of2.pos) = char_length(of.pos) + 2) as children_count,
			of.allow_movement::int as allow_movement, of.allow_add::int as allow_add, of.allow_remove::int as allow_remove, of.display_title_and_top_actions::int as display_title_and_top_actions,
			of.display_default_actions::int as display_default_actions, ts.name as title_style
			FROM template_objects of	
			JOIN object_title_display_style ts ON ts.id = of.title_display_style		
			JOIN template_objects r ON  r.pos = substring(of.pos, 1, 2) AND r.template_id = of.template_id
			JOIN objects f ON f.id = of.object_id			
			WHERE r.id = ' . (int)$pTemplateObjectId . '
			ORDER BY of.pos
		',
		'subobject_external_link' => 0,
		'templs' => $lTempls,		
	));	
	return $lObject->Display();
}

//Изкарва линк за триене, ако обекта е от 1во ниво
function displayTemplateObjectDeleteLink($pId, $pLevel){
	if($pLevel != 1)
		return;
	return '<a href="javascript:if (confirm(\'' . getstr('pwt_admin.templates.objects.confirmDel') . '\')) { window.location = \'/resources/templates/template_object.php?id=' . $pId . '&tAction=delete\';} else {}">
				<img src="/img/trash2.gif" alt="' . getstr('admin.deleteButton') . '" title="' . getstr('admin.deleteButton') . '" border="0" />';
}

/**
	Връща цсс клас за криене, за тези обекти, които не са главни
*/
function getTemplateObjectRowClass($pParentId){
	if((int)$pParentId){
		return ' hiddenElement ';
	}
}

//Izkarvame padding za tekushtiq lvl
function displayTemplateObjectLevelCssStyle($pLevel){
	return 'padding-left:' . (($pLevel - 1) * 40 + 5) . 'px';
}

/**
	Ако обекта има деца - слагаме линк за показване на
	дървото с децата.
	Ако линка е външен слагаме линк-а към пхп-то за показване на поддървото
*/
function displayTemplateObjectTreeLink($pId, $pHasChildren, $pExternalLink){
	if(!$pHasChildren){
		return;
	}
	if((int)$pExternalLink){
		$lResult = '<a href="/resources/templates/template_object_subobjects.php?template_object_id=' . (int)$pId . '" target="_blank"><img src="/img/add.gif" alt="' . getstr('admin.showButton') . '" title="' . getstr('admin.showButton') . '" border="0" /></a>';
	}else{
		$lResult = '<img onclick="displayTemplateObjectSubtree(' . (int)$pId . ')" id="template_object_show_' . (int)$pId . '" src="/img/add.gif" alt="' . getstr('admin.showButton') . '" title="' . getstr('admin.showButton') . '" border="0" />';
		$lResult .= '<img class="hiddenElement" onclick="hideTemplateObjectSubtree(' . (int)$pId . ')" id="template_object_hide_' . (int)$pId . '" src="/img/remove.gif" alt="' . getstr('admin.hideButton') . '" title="' . getstr('admin.hideButton') . '" border="0" />';
	}
	return $lResult;
}

function parseToInt($pArg){
	return (int)$pArg;
}


//Изкарва списък с всички обекти, които имат това поле
function ShowFieldRelatedObjects($pFieldId){
	if(!$pFieldId)
		return;
	$lTempls = array(
		G_ROWTEMPL => 'fields.relatedObjectsListRow',
		G_HEADER => 'fields.relatedObjectsListHead',
		G_STARTRS => 'fields.relatedObjectsListStart',
		G_ENDRS => 'fields.relatedObjectsListEnd',
		G_FOOTER => 'fields.relatedObjectsListFoot',
		G_NODATA => 'fields.relatedObjectsListNoData',
	);
		
	$lObject = new crs(array(
		'field_id' => $pFieldId,
		'sqlstr' => 'SELECT of.id, f.id as object_id, f.name as object_name, h.name as control_type_name, of.label, of.allow_nulls::int as allow_nulls
			FROM object_fields of			
			JOIN objects f ON f.id = of.object_id
			JOIN html_control_types h ON h.id = of.control_type
			WHERE of.field_id = ' . (int)$pFieldId . '
			ORDER BY f.id
		',
		'templs' => $lTempls,		
	));	
	return $lObject->Display();
}
?>