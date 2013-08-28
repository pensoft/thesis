<?php
$x = session_start(); //na servera session_autostart e 0
//~ var_dump($x);
include_once(getenv("DOCUMENT_ROOT") . "/lib/conf.php");
require_once(PATH_ECMSFRCLASSES . 'static.php');
include_once(getenv("DOCUMENT_ROOT") . "/lib/struct.php");
include_once(getenv("DOCUMENT_ROOT") . "/lib/cuser.php");
require_once(PATH_CLASSES . 'static.php');

$gWikiExportIgnorePageTitles = 0;
$gSiteStruct = GetSitesStruct();
$gSiteAccess = GetAccess();
$gTimeLogger = new ctime_logger(array());
//ид-то на статията за която в момента правим xsl трансформация - ползва се понякога в уики експорта
$gXslProcessedArticleId = false;
$gEcmsLibRequest =
	(
		(
			($_SERVER["HTTP_USER_AGENT"] =="ecmsconnect")
			|| ($_SERVER["HTTP_USER_AGENT"] =="wsb")
		)
		|| (int) $_REQUEST['force_json_output']
	)
	? 1 : 0;

	//~ $gEcmsLibRequest = 1;
if((int) $gEcmsLibRequest ){
	//~ error_reporting(0);//Spirame vsi4ki greshki
	//~ define('ERROR_REPORTING', 0);//Za da moje ako nqkyde sled statica se vika error_reporting da ne se precakame
	ini_set('display_errors', 'Off');
}
$gUrl = substr(getenv('SCRIPT_NAME'), 0, strrpos(getenv('SCRIPT_NAME'), '/') + 1);
$gSiteAccessType = $gSiteAccess[$gUrl];
if (!$gSiteAccessType) $gSiteAccessType = 0;

$gBHLData = ''; //tazi promenliva se polzva pri chetene na xml-a ot BHL v callback funkciata

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
	global $user, $gUrl,$gEcmsLibRequest;

	ProccessHistory();
	if( !$gEcmsLibRequest ){
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
			<link type="text/css" rel="stylesheet" href="/resources/icotest/HtmlText/HtmlText.css" media="all" title="default" />
			<link type="text/css" rel="stylesheet" href="/lib/def.css" media="all" title="default" />
			<link type="text/css" rel="stylesheet" href="/lib/orange.css" media="all" title="orange" />
			<link type="text/css" rel="stylesheet" href="/lib/green.css" media="all" title="green" />
			<link type="text/css" rel="stylesheet" href="/lib/black.css" media="all" title="black" />
			<link type="text/css" rel="stylesheet" href="/lib/def.css" media="all" title="blue" />
			<script type="text/javascript" language="JavaScript">
				var gSiteUrl = \'' . ADM_URL .  '\';
			</script>
			<script type="text/javascript" language="JavaScript" src="/lib/ajaxObjectsDescriptor.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/def.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/EWorkaround.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/EDispecher.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/markuptool.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/TextHolder.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/Selection.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/jquery.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/jquery_form.js"></script>


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
	}
	UserRedir($user);}

function HtmlStartXml($Hide = 0) {
	global $user, $gUrl, $gEcmsLibRequest;

	ProccessHistory();

	if( !$gEcmsLibRequest ){
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
			<script type="text/javascript" language="JavaScript" src="/lib/ajaxObjectsDescriptor.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/def.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/EWorkaround.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/EDispecher.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/markuptool.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/TextHolder.js"></script>
			<script type="text/javascript" language="JavaScript" src="/lib/Selection.js"></script>

		</head>
		<body>
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
			';
		}
	}

	UserRedir($user);
}

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

function HtmlEndXml($Hide = 0) {
	global $gEcmsLibRequest;
	if( !$gEcmsLibRequest ){
		UpdateHistory();


		if (!$Hide) {
			echo '
			<div id="footer">
				' . getstr('admin.copywright') . '
			</div>';
		}

		echo '
		</body>
		</html>';
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
	global $gUrl, $gSiteAccess, $gEcmsLibRequest;
	if (!$gSiteAccess[$gUrl]) {
		if (!$pUser) {
			$url = getenv("REQUEST_URI");
			if (!$gEcmsLibRequest) header('Location: /login/index.php?url=' . urlencode($url));
			else echo json_encode(array("err"=> -778, "rescnt" => 0, "errdesc"=> "You must login first!"));
			exit;
		} else {
			if (!$gEcmsLibRequest) header('Location: /error.php');
			else echo json_encode(array("err"=> -779, "rescnt" => 0, "errdesc"=> "You don't have permission to access this data!"));
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

function showRelatedAttributes($pNodeId) {
	echo '
	<div id="framesholder">
		<iframe class="rels" border="0" id="relsframe" name="relsframe" src="/resources/xml_nodes/relattributes.php?node_id='. (int)$pNodeId .'"></iframe>
	</div>
	';
}

function showRelatedProperties($pRuleId) {
	global $gEcmsLibRequest;
	if( !$gEcmsLibRequest ) {
		echo '
		<div id="framesholder" class="rule_properties">
			<iframe class="rels" border="0" id="relsframe" name="relsframe" src="/resources/autotag_rules/relproperties.php?rule_id='. (int)$pRuleId .'"></iframe>
		</div>
		';
	}
}

function showRelatedSyncDetails($pTemplateId) {
	global $gEcmsLibRequest;
	if( !$gEcmsLibRequest ) {
	echo '
	<div id="framesholder" class="rule_properties">
		<iframe class="rels" border="0" id="relsframe" name="relsframe" src="/resources/xml_sync_templates/reldetails.php?template_id='. (int)$pTemplateId .'"></iframe>
	</div>
	';
	}
}

function showIndesignTemplateDetails($pTemplateId) {
	global $gEcmsLibRequest;
	if( !$gEcmsLibRequest ) {
	echo '
	<div id="framesholder" class="rule_properties">
		<iframe class="rels" border="0" id="relsframe" name="relsframe" src="/resources/indesign_templates/reldetails.php?template_id='. (int)$pTemplateId .'"></iframe>
	</div>
	';
	}
}

function showSyncedXmlDetails($pArticleId) {
	global $gEcmsLibRequest;
	if( !$gEcmsLibRequest ) {
	echo '
	<div id="framesholder" class="rule_properties">
		<iframe class="rels" border="0" id="relsframe" name="relsframe" src="/resources/articles/reldetails.php?article_id='. (int)$pArticleId .'"></iframe>
	</div>
	';
	}
}

function showRelatedVariables($pSourceId) {
	global $gEcmsLibRequest;
	if( !$gEcmsLibRequest ) {
	echo '
	<div id="framesholder" class="rule_properties">
		<iframe class="rels" border="0" id="relsframe" name="relsframe" src="/resources/autotag_rules/autotag_re_sources/relvariables.php?source_id='. (int)$pSourceId .'"></iframe>
	</div>
	';
	}
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

function SyncXmlData($pArticleId, $pXml){
	if(!(int) $pArticleId )
		return;

	$lDOM = new DOMDocument("1.0");
	$lDOM->resolveExternals = true;
	if( !$lDOM->loadXML($pXml))
		return;

	$lXPath = new DOMXPath($lDOM);

	$lCon = new DBCn();
	$lCon->Open();
	$lSql = '
		SELECT sd.id, sd.xpath, sd.sync_type, sd.sync_column_name, sd.sync_column_default_value
		FROM xml_sync_details sd
		JOIN articles a ON a.xml_sync_template_id = sd.xml_sync_templates_id
		WHERE a.id = ' . (int) $pArticleId;
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	$lSql = 'DELETE FROM xml_sync WHERE article_id = ' . (int) $pArticleId . ';';//Triem starite zapisi
	//~ $lSql = '';
	while(!$lCon->Eof()){
		$lXpathExp = $lCon->mRs['xpath'];
		$lSyncType = (int)$lCon->mRs['sync_type'];
		$lSyncDetailId = (int)$lCon->mRs['id'];
		$lSyncColumnName = $lCon->mRs['sync_column_name'];
		$lDefaultValue = $lCon->mRs['sync_column_default_value'];

		$lXPathResult = $lXPath->query($lXpathExp);
		for($i = 0; $i < $lXPathResult->length; ++$i){
			$lNode = $lXPathResult->item($i);
			$lContent = trim($lNode->textContent);
			if( $lSyncType == (int) XML_SYNC_COLUMN_TYPE ){//Updatevame tekstova kolona ot articles i breakvame - taq kolona moje da ima samo 1 stoinost
				if( !$lContent ){
					$lContent = $lDefaultValue;
				}
				$lSql .= 'UPDATE articles SET ' . $lSyncColumnName . ' = \'' . q($lContent) . '\' WHERE id = ' . (int) $pArticleId . ';';
				break;
			}else{//Slagame zapis v xml_sync
				$lSql .= 'INSERT INTO xml_sync (xml_sync_details_id, article_id, data) VALUES (' . (int) $lSyncDetailId . ', ' . (int) $pArticleId . ', \'' . q($lContent) . '\');';
			}
		}
		if( $lSyncType == (int) XML_SYNC_COLUMN_TYPE && !$lXPathResult->length){//Slagame default stoinosta
			$lSql .= 'UPDATE articles SET ' . $lSyncColumnName . ' = \'' . q($lDefaultValue) . '\' WHERE id = ' . (int) $pArticleId . ';';
		}
		$lCon->MoveNext();
	}
	$lCon->Close();
	$lCon->Open();
	$lCon->Execute($lSql);
}


function getTaxonMap($pTaxonName){
	$lUrl = TAXON_MAP_SRV . $pTaxonName;
	$lQueryResult = executeExternalQuery($lUrl);
	$lMapFound = false;
	$lResult = '<h2>' . getstr('admin.external_details_about_taxon.distributionMapTitle') . '</h2>';
	if( $lQueryResult ){
		$lDom = new DOMDocument();
		if($lDom->loadXML($lQueryResult)){
			$lXpath = new DOMXPath($lDom);
			$lXpathQuery = '/taxa/taxon/mapHTML';
			$lXPathResult = $lXpath->query($lXpathQuery);
			if( $lXPathResult->length ){
				$lMapIframe = $lXPathResult->item(0);
				if( $lMapIframe ){
					$lMapFound = true;
					$lResult .= $lMapIframe->textContent;
				}
			}
		}
	}
	if( !$lMapFound ){
		$lResult .= getstr('admin.external_details_about_taxon.noMap');
	}
	return $lResult;

}


function getTaxonLinks($pTaxonName, $pLinksPerSource = 10){//Vryshta linkove kym statii za vyprosniq taxon
	//Za info http://eutils.ncbi.nlm.nih.gov/corehtml/query/static/esearch_help.html

	$lUrl = EUTILS_ESEARCH_SRV . 'term=' . str_replace(' ', '+', $pTaxonName) . '&retmode=xml&retmax=' . (int) $pLinksPerSource . '&tool=' . EUTILS_TOOL_NAME;
	$lDataBases = array(EUTILS_PUBMED_DB, EUTILS_NUCLEOTIDE_DB);
	//~ $lDataBases = array(EUTILS_PUBMED_DB);//Bazite ot danni v koito tyrsim statii
	$lLinksExist = false;
	foreach($lDataBases as $lDataBaseName){
		$lCurrentDataseResults = '';
		$lSingleUrl = $lUrl . '&db=' . $lDataBaseName;
		$lQueryResult = executeExternalQuery($lSingleUrl);
		if( $lQueryResult ){
			$lDom = new DOMDocument();
			$lDatabaseIds = array();
			if($lDom->loadXML($lQueryResult)){
				$lXpath = new DOMXPath($lDom);
				$lXpathQuery = '/eSearchResult/IdList/Id';
				$lXPathResult = $lXpath->query($lXpathQuery);

				foreach( $lXPathResult as $lSingleId){//Vzimame id-tata i stroim linkove
					$lResourceId = $lSingleId->textContent;
					if( $lResourceId ){
						$lResourceLink = getTaxonResourceLink($lDataBaseName, $lResourceId);
						$lDatabaseIds[$lResourceId] = array('title' => $lResourceId, 'link' => $lResourceLink);
					}
				}

				if( is_array( $lDatabaseIds ) && count($lDatabaseIds) ){
					$lCurrentDatabaseHasResults = false;
					$lIds = array_keys($lDatabaseIds);
					if( is_array($lIds) && count($lIds) ){//Stroim title-i za linkovete

						$lTitleUrl = EUTILS_ESUMMARY_SRV . '&db=' . $lDataBaseName . '&id=' . implode(',', $lIds) . '&retmode=xml';
						$lTitleQueryResult = executeExternalQuery($lTitleUrl);
						if( $lTitleQueryResult ){
							$lTitleDom = new DOMDocument();
							if($lTitleDom->loadXML($lTitleQueryResult)){
								$lTitleXpath = new DOMXPath($lTitleDom);
								$lTitleXpathQuery = '/eSummaryResult/DocSum';
								$lElements = $lTitleXpath->query($lTitleXpathQuery);
								foreach( $lElements as $lSingleElement){
									$lIdXpath = './Id';
									$lIdXpathResult = $lTitleXpath->query($lIdXpath, $lSingleElement);
									if( $lIdXpathResult->length ){
										$lCurrentId = $lIdXpathResult->item(0)->textContent;
										$lCurrentTitleXpath = "./Item[@Name='Title']";
										$lCurrentTitleResult = $lTitleXpath->query($lCurrentTitleXpath, $lSingleElement);
										if( $lCurrentTitleResult->length ){
											$lCurrentTitle = $lCurrentTitleResult->item(0)->textContent;
											$lDatabaseIds[$lCurrentId]['title'] = $lCurrentTitle;

										}
									}
								}
							}
						}
						foreach( $lDatabaseIds as $lResourceId => $lResourceData ){
							$lCurrentDatabaseHasResults = true;
							$lLinksExist = true;
							$lCurrentDataseResults .= '<a href="' . $lResourceData['link'] . '" target="_blank" title="' . $lResourceData['title'] . '">' . $lResourceData['title'] . '</a>';
						}
					}
				}
			}
		}
		if( $lCurrentDatabaseHasResults ){
			$lResult .= '<h3 class="linkDatabaseTitle">' . getstr('admin.external_details_about_taxon.linkDatabaseTitle' . $lDataBaseName) . '</h3>';
		}
		$lResult .= $lCurrentDataseResults;
	}
	$lResult = '<h2 class="taxonLinksTitle">' . getstr('admin.external_details_about_taxon.linksTitle') . '</h2>' . $lResult;
	if( !$lLinksExist ){
		$lResult .= getstr('admin.external_details_about_taxon.noLinks');
	}
	$lResult = '<div class="taxonLinks">' . $lResult . '</div>';
	return $lResult;
}

function getTaxonResourceLink($pDatabase, $pResourceId){
	switch($pDatabase){
		case EUTILS_PUBMED_DB:{
			return PUBMED_LINK_PREFIX . $pResourceId;
		}
		case EUTILS_NUCLEOTIDE_DB:{
			return NUCLEOTIDE_LINK_PREFIX . $pResourceId;
		}
	}
}

function BuildPropertyLink($pRs){
	$lName = $pRs['property_name'];
	$lTypeId = (int) $pRs['type_id'];
	$lPropertyId = (int) $pRs['property_id'];
	$lHref = '';
	switch( $lTypeId ){
		case (int) PLACE_RULE_PROPERTY_TYPE:{
			$lHref = '/resources/autotag_rules/place_rules/edit.php?tAction=showedit&id=' . (int) $lPropertyId;
			break;
		}
		case (int) REGEXP_RULE_PROPERTY_TYPE:{
			$lHref = '/resources/autotag_rules/regular_expressions/edit.php?tAction=showedit&id=' . (int) $lPropertyId;
			break;
		}
		case (int) SOURCE_RULE_PROPERTY_TYPE:{
			$lHref = '/resources/autotag_rules/autotag_re_sources/edit.php?tAction=showedit&id=' . (int) $lPropertyId;
			break;
		}
	}
	return '<a href="' . $lHref . '" target="_blank">' . $lName . '</a>';
}

function putUnfloat($pRownum, $pItemsonrow){
	if( ($pRownum % $pItemsonrow) == 0 )
		return '<div class="unfloat"></div>';
}

function ShowEntrezRecordsDbSubtreeLink($pTaxonName, $pTaxonId, $pDbName, $pCount){
	if(!(int) $pCount ){
		return '-';
	}
	return '<a href="' . ParseTaxonExternalLink($pTaxonName, NCBI_SUBTREE_LINK . '&term=txid' . $pTaxonId . '[Organism:exp]&db=' . $pDbName) . '">' . (int)$pCount . '</a>';

}

function CutText($d, $len = 80) {
	$d = strip_tags($d);
	if (mb_strlen($d, 'UTF-8') < $len) return $d;
	$cut = mb_substr($d, 0, $len, 'UTF-8');
	return mb_substr($cut, 0, mb_strrpos($cut, ' ', 'UTF-8'), 'UTF-8') . '...';
}

function getLinkIframe($pUrl, $pPostForm, $pPostFields = ''){
	if( !$pPostForm ){//Normalna get zaqvka
		$lResult = '<iframe id="ext_link_iframe" name="ext_link_iframe" src="' . $pUrl . '" class="externalIframe"></iframe>';
	}else{
		$lBaseUrl = $pUrl;
		$lFields = parseStringPostfields($pPostFields);
		$lResult = '
			<iframe id="ext_link_iframe" name="ext_link_iframe" class="externalIframe"></iframe>
			<form name="ext_link_form" id="ext_link_form" action="' . $lBaseUrl . '" target="ext_link_iframe" method="post">
		';
		foreach( $lFields as $lFieldName => $lValue){
			$lResult .= '<input type="hidden" name="' . $lFieldName . '" value="' . $lValue . '" />';
		}
		$lResult .= '</form>
			<script>document.ext_link_form.submit()</script>
		';
	}
	return $lResult;

}

function getBaseUrl($pUrl){//Връща базовото урл към което трябва да направим пост заявката напр от http://etaligent.net?query=a ще върне http://etaligent.net
	$lParsedUrl = parse_url($pUrl);
	return 'http://' . $lParsedUrl['host'] . $lParsedUrl['path'];
}

function getUrlPostFields($pUrl){//Връща масив с полетата които се съдържат в заявката от урл-а - напр. от http://etaligent.net?query=a ще върне array(query => a)
	$lParsedUrl = parse_url($pUrl);
	parse_str($lParsedUrl['query'], $lResult);
	return $lResult;
}

function parseStringPostfields($pString){//Връща масив с полетата които се съдържат в стринга - напр. query=a ще върне array(query => a)
	$lFakeUrl = 'http://www.etaligent.net?' . $pString;
	return getUrlPostFields($lFakeUrl);
}

function ParsePubmedTaxonName($pTaxonName){//Parsva taxona taka 4e v pubmed da go tyrsi s AND, a ne s OR
	return str_replace(' ', ' AND ', $pTaxonName);//Zamenq intervalite s AND
}

function showLinksMenuLastRowClass($pRecords, $pRownum, $pClassName){
	if((int) $pRecords == (int) $pRownum )
		return $pClassName;
}

function showExtLinksMenuFirstRowClass($pRownum, $pClassName){
	if(1 == (int) $pRownum )
		return $pClassName;
}

function showImageIfSrcExists($pSrc, $pClass = 'noBorder'){
	if( trim($pSrc)){
		return '<img class="' . $pClass . '" src="' . $pSrc . '"></img>';
	}
}

function putColumnExceptOnLastRow($pRownum, $pRecords){
	if((int) $pRownum != (int) $pRecords ){
		return ';';
	}
}

function parseIndesignXml($pXml, $pReplaceBrSymbol, $pJournalId = 0, $pOverwriteJournalInfo = false){
	global $G_INDESIGN_TAGS_SPLIT;
	global $gEcmsLibRequest;
	//Loadvame XML-a za da moje da go vzemem s hubav output - s neobhodimite intervali i dyrvovidna struktura
	//~ $gEcmsLibRequest = 1;

	$lDOM = new DOMDocument("1.0");
	$lDOM->preserveWhiteSpace = true;
	if (!$lDOM->loadXML($pXml))
		return $pXml;

	if((int) $pJournalId && $pOverwriteJournalInfo){
		fillArticleJournalInfo($lDOM, $pJournalId);
	}
	//~ var_dump($pJournalId, $pOverwriteJournalInfo);
	//~ var_dump(GetFormattedXml($lDOM->saveXML(), $pReplaceBrSymbol));
	//~ exit;
	if( !$gEcmsLibRequest )
		return GetFormattedXml($lDOM->saveXML(), $pReplaceBrSymbol);

	parseIndesignXmlIgnoreTags($lDOM);
	parseIndesignXmlBreakTags($lDOM);
	parseIndesignTableTags($lDOM);
	parseIndesignArticleMeta($lDOM);
	removeIndesignXmlFormattingTags($lDOM);
// 	var_dump(GetFormattedXml($lDOM->saveXML(), $pReplaceBrSymbol));
// 	exit;
	return GetFormattedXml($lDOM->saveXML(), $pReplaceBrSymbol);
}

/**
	Обработва мета информацията за статията
	За повече информация -
	https://projects.etaligent.net/issues/472
	https://projects.etaligent.net/attachments/247/4_-_journals-PMT-meta.xml
	https://projects.etaligent.net/issues/678
	https://projects.etaligent.net/attachments/347/article_meta_tags_order.xml
*/
function parseIndesignArticleMeta(&$pXmlDom){
	parseIndesignContribTags($pXmlDom);
	parseIndesignAuthorNotes($pXmlDom);
	parseIndesignHistory($pXmlDom);
	parseIndesignPermissions($pXmlDom);
	parseIndesignSelfUri($pXmlDom);
	parseIndesignKeywords($pXmlDom);

	//Сега оправяме поредността
	$lXpath = new DOMXPath($pXmlDom);
	$lArticleMetaQuery = '/article/front/article-meta';
	$lArticleMetaResult = $lXpath->query($lArticleMetaQuery);
	if(!$lArticleMetaResult->length){//Ако няма article-meta - не правим нищо
		return;
	}
	$lArticleMetaNode = $lArticleMetaResult->item(0);

	//article-id трябва да е 1ви child
	$lArticleIdQuery = './article-id';
	$lArticleIdResult = $lXpath->query($lArticleIdQuery, $lArticleMetaNode);
	if($lArticleIdResult->length){//Ако няма article-meta - не правим нищо
		$lArticleIdNode = $lArticleIdResult->item(0);
		$lArticleIdNode->setAttribute('pub-id-type', 'doi');
		if($lArticleIdNode !== $lArticleMetaNode->firstChild){
			$lArticleMetaNode->insertBefore($lArticleIdNode, $lArticleMetaNode->firstChild);
		}
	}
	//махаме директните наследници от тип <bold>, <italic> i <journal-id>
	$lStripTagNames = array('bold', 'italic', 'journal-id');
	foreach($lStripTagNames as $lCurrentStripTagName){
		$lStripTagQuery = './' . $lCurrentStripTagName;
		$lStripTagResult = $lXpath->query($lStripTagQuery, $lArticleMetaNode);
		for($i = $lStripTagResult->length - 1; $i >= 0; --$i){
			$lArticleMetaNode->removeChild($lStripTagResult->item($i));
		}
	}

	//History и self-uri са поставени на местата си от съответните функции за тяхното конструиране

}

/**
	Попълва информацията за journal-а в xml-a.
	За повече информация -
	https://projects.etaligent.net/issues/472
	https://projects.etaligent.net/attachments/247/4_-_journals-PMT-meta.xml

	<journal-meta>
            <journal-id journal-id-type="publisher-id">ZooKeys</journal-id>    // idva ot Journal title
            <journal-title-group>
	      				<journal-title>ZooKeys</journal-title>												// idva ot Journal title
	      				<abbrev-journal-title>ZK</abbrev-journal-title>								// idva ot Journal title abrev
    				</journal-title-group>
            <issn pub-type="ppub">1313-2989</issn>														// idva ot ISSN print
            <issn pub-type="epub">1313-2970</issn>														// idva ot ISSN online
            <publisher>
                <publisher-name>Pensoft Publishers</publisher-name>						// idva ot Publisher
            </publisher>
	</journal-meta>
*/
function fillArticleJournalInfo(&$pXmlDom, $pJournalId){
	$lCon = Con();
	$lCon->Execute('SELECT * FROM journals WHERE id = ' . (int) $pJournalId);
	$lCon->MoveFirst();
	if(!$lCon->mRs['id'])//Ако няма такъв journal - нищо не правим
		return;

	$lXpath = new DOMXPath($pXmlDom);
	$lJournalMetaQuery = '/article/front/journal-meta';
	$lJournalMetaResult = $lXpath->query($lJournalMetaQuery);
	$lJournalMetaNode = false;
	if($lJournalMetaResult->length){
		$lJournalMetaNode = $lJournalMetaResult->item(0);
	}else{//Ако няма такъв възел добавяме го преди article-meta
		$lArticleMetaQuery = '/article/front/article-meta';
		$lArticleMetaResult = $lXpath->query($lArticleMetaQuery);
		if(!$lArticleMetaResult->length){//Ако няма article-meta - не правим нищо
			return;
		}
		$lArticleMetaNode = $lArticleMetaResult->item(0);
		$lJournalMetaNode = $lArticleMetaNode->parentNode->insertBefore($pXmlDom->createElement('journal-meta'), $lArticleMetaNode);
	}
	if(!$lJournalMetaNode)
		return;
	//Махаме съдържанието на възела
	while($lJournalMetaNode->firstChild){
		$lJournalMetaNode->removeChild($lJournalMetaNode->firstChild);
	}
	//Journal id
	$lJournalIdNode = $lJournalMetaNode->appendChild($pXmlDom->createElement('journal-id'));
	$lJournalIdNode->setAttribute('journal-id-type', 'publisher-id');
	$lJournalIdNode->appendChild($pXmlDom->createTextNode($lCon->mRs['pensoft_title']));

	//Journal title group
	$lJournalTitleGroupNode = $lJournalMetaNode->appendChild($pXmlDom->createElement('journal-title-group'));
	$lJournalTitleNode = $lJournalTitleGroupNode->appendChild($pXmlDom->createElement('journal-title'));
	$lAbrevJournalTitleNode = $lJournalTitleGroupNode->appendChild($pXmlDom->createElement('abbrev-journal-title'));

	$lJournalTitleNode->appendChild($pXmlDom->createTextNode($lCon->mRs['pensoft_title']));
	$lAbrevJournalTitleNode->appendChild($pXmlDom->createTextNode($lCon->mRs['title_abrev']));

	//Issn print
	$lIssnPrintNode = $lJournalMetaNode->appendChild($pXmlDom->createElement('issn'));
	$lIssnPrintNode->setAttribute('pub-type', 'ppub');
	$lIssnPrintNode->appendChild($pXmlDom->createTextNode($lCon->mRs['issn_print']));

	//Issn online
	$lIssnOnlineNode = $lJournalMetaNode->appendChild($pXmlDom->createElement('issn'));
	$lIssnOnlineNode->setAttribute('pub-type', 'epub');
	$lIssnOnlineNode->appendChild($pXmlDom->createTextNode($lCon->mRs['issn_online']));

	//Publisher
	$lPublisherNode = $lJournalMetaNode->appendChild($pXmlDom->createElement('publisher'));
	$lPublisherNameNode = $lPublisherNode->appendChild($pXmlDom->createElement('publisher-name'));
	$lPublisherNameNode->appendChild($pXmlDom->createTextNode($lCon->mRs['publisher']));
}

/**
	Обработва се keywords тага - сплитват се keyword-ите на база на разделител запетайка
	За повече информация
	https://projects.etaligent.net/attachments/308/10_-_keywords.xml
	https://projects.etaligent.net/issues/613
*/
function parseIndesignKeywords(&$pXmlDom){
	$lXpath = new DOMXPath($pXmlDom);
	$lKeywordsQuery = '/article/front/article-meta/kwd-group';
	$lKeywordsResult = $lXpath->query($lKeywordsQuery);
	if(!$lKeywordsResult->length){
		return;
	}
	$lKeywordsNode = $lKeywordsResult->item(0);
	$lLabelNodeResult = $lXpath->query('./label', $lKeywordsNode);
	$lLabel = '';
	if($lLabelNodeResult->length){
		$lLabelNode = $lLabelNodeResult->item(0);
		$lLabel = $lLabelNode->textContent;
		//Махаме лейбъла за да може да вземем после само ключовите думи, без заглавието
		$lLabelNode->parentNode->removeChild($lLabelNode);
	}
	$lKwdNode = $pXmlDom->CreateElement('kwd');

	while($lKeywordsNode->firstChild){
		$lKwdNode->appendChild($lKeywordsNode->firstChild);
	}
	$lKwdNode = $lKeywordsNode->appendChild($lKwdNode);
	splitTextNodesByRegularExpression($lKwdNode, '/\s*,\s*/ism', 'kwd');
	//~ $lKeywordsTxt = $lKeywordsNode->textContent;
	//~ //Изпразваме възела, за да може после да е с правилно съдържание, без излишни възли
	//~ while($lKeywordsNode->firstChild){
		//~ $lKeywordsNode->removeChild($lKeywordsNode->firstChild);
	//~ }
	//Слагаме лейбъла, ако е имало такъв
	if($lLabel){
		$lLabelNode = false;
		if($lKeywordsNode->firstChild){
			$lLabelNode = $lKeywordsNode->insertBefore($pXmlDom->createElement('label'), $lKeywordsNode->firstChild);
		}else{
			$lLabelNode = $lKeywordsNode->appendChild($pXmlDom->createElement('label'));
		}
		$lLabelNode->appendChild($pXmlDom->createTextNode($lLabel));
	}
	//~ //Слагаме ключовите думи
	//~ $lKeywordsArr = explode(',', $lKeywordsTxt);
	//~ foreach($lKeywordsArr as $lCurrentKeyword){
		//~ $lCurrentKeyword = trim($lCurrentKeyword);
		//~ if($lCurrentKeyword){
			//~ $lKwdNode = $lKeywordsNode->appendChild($pXmlDom->createElement('kwd'));
			//~ $lKwdNode->appendChild($pXmlDom->createTextNode($lCurrentKeyword));
		//~ }
	//~ }
}

/**
	Добавя се permissions тага. За повече информация
	https://projects.etaligent.net/attachments/307/09_-_permissions.xml
	https://projects.etaligent.net/issues/613

	Тук само слагаме атрибутите на тага, а премахването на italic таговете
	става във ф-ята removeIndesignXmlFormattingTags
*/
function parseIndesignPermissions(&$pXmlDom){
	$lXpath = new DOMXPath($pXmlDom);
	$lMetaDataQuery = '/article/front/article-meta';
	$lMetaDataResult = $lXpath->query($lMetaDataQuery);
	if(!$lMetaDataResult->length)
		return;
	$lMetadataNode = $lMetaDataResult->item(0);
	$lPermissionsNode = false;

	$lHistoryQuery = './history';
	$lHistoryResult = $lXpath->query($lHistoryQuery, $lMetadataNode);
	if($lHistoryResult->length){
		$lHistoryNode = $lHistoryResult->item(0);
		if($lHistoryNode->nextSibling){
			$lPermissionsNode = $lMetadataNode->insertBefore($pXmlDom->createElement('permissions'), $lHistoryNode->nextSibling);
		}else{
			$lPermissionsNode = $lMetadataNode->appendChild($pXmlDom->createElement('permissions'));
		}
	}else{
		$lPermissionsNode = $lMetadataNode->appendChild($pXmlDom->createElement('permissions'));
	}
	$lCopyrightNode = $lPermissionsNode->appendChild($pXmlDom->createElement('copyright-statement'));

	$lAuthorsQuery = './/contrib/name';
	$lAuthorsNodes = $lXpath->query($lAuthorsQuery, $lMetadataNode);
	for($i = 0; $i < $lAuthorsNodes->length; ++$i){//Взимаме имената на авторите и ги слагаме в permissions възела
		$lCurrentAuthor = $lAuthorsNodes->item($i);
		$lFirstName = '';
		$lLastName = '';
		$lFirstNameQuery = './given-names';
		$lLastNameQuery = './surname';
		$lFirstNameResult = $lXpath->query($lFirstNameQuery, $lCurrentAuthor);
		$lLastNameResult = $lXpath->query($lLastNameQuery, $lCurrentAuthor);
		if($lFirstNameResult->length){
			$lFirstName = trim($lFirstNameResult->item(0)->textContent);
		}
		if($lLastNameResult->length){
			$lLastName = trim($lLastNameResult->item(0)->textContent);
		}
		$lName = '';
		if(!$lFirstName && !$lLastName){
			$lName = $lCurrentAuthor->textContent;
		}else{
			$lName = $lFirstName . ' ' . $lLastName;
		}
		$lName = trim($lName);
		if($i > 0 )
			$lName = ', ' . $lName;
		$lCopyrightNode->appendChild($pXmlDom->createTextNode($lName));
	}

	$lLicenseNode = $lPermissionsNode->appendChild($pXmlDom->createElement('license'));
	$lLicenseNode->setAttribute('license-type', 'creative-commons-attribution');
	$lLicenseNode->setAttributeNS(XLINK_NAMESPACE_URL, 'xlink:href', 'http://creativecommons.org/licenses/by/3.0');
	$lLicenseNode->setAttributeNS(XLINK_NAMESPACE_URL, 'xlink:type', 'simple');
	$lLicensePNode = $lLicenseNode->appendChild($pXmlDom->createElement('license-p'));
	$lLicensePNode->appendChild($pXmlDom->createTextNode('This is an open access article distributed under the terms of the Creative Commons Attribution License 3.0 (CC-BY), which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.'));
}

/**
	Обработва се self uri тага. За повече информация
	https://projects.etaligent.net/attachments/306/08_-_selfuri.xml
	https://projects.etaligent.net/issues/613

	Тук само слагаме атрибутите на тага, а премахването на italic таговете
	става във ф-ята removeIndesignXmlFormattingTags
*/
function parseIndesignSelfUri(&$pXmlDom){
	$lXpath = new DOMXPath($pXmlDom);
	$lUriQuery = '/article/front/article-meta/self-uri';
	$lUriNodeResult = $lXpath->query($lUriQuery);
	if(!$lUriNodeResult->length)
		return;
	$lUriNode = $lUriNodeResult->item(0);
	$lUriNode->setAttribute('content-type', 'lsid');
	$lUriNode->setAttributeNs(XLINK_NAMESPACE_URL, 'xlink:type', 'simple');

	//Местим тага да е директно след permissions възела

	$lPermissionsQuery = '/article/front/article-meta/permissions';
	$lPermissionsResult = $lXpath->query($lPermissionsQuery);
	if(!$lPermissionsResult->length)
		return;
	$lPermissionsNode = $lPermissionsResult->item(0);
	if($lPermissionsNode->nextSibling){
		$lPermissionsNode->parentNode->insertBefore($lUriNode, $lPermissionsNode->nextSibling);
	}else{
		$lPermissionsNode->parentNode->appendChild($lUriNode);
	}
}

/**
	Обработва се author nodes тага. За повече информация
	https://projects.etaligent.net/attachments/304/06_-_author-notes.xml
	https://projects.etaligent.net/issues/613

	Тук само слагаме типовете на fn таговете, а премахването на italic таговете
	става във ф-ята removeIndesignXmlFormattingTags
*/
function parseIndesignAuthorNotes(&$pXmlDom){
	$lXpath = new DOMXPath($pXmlDom);
	$lFnQuery = '/article/front/article-meta/author-notes/fn';
	$lFnNodes = $lXpath->query($lFnQuery);
	for($i = 0; $i < $lFnNodes->length; ++$i){
		$lCurrentFn = $lFnNodes->item($i);
		$lTextContent = $lCurrentFn->textContent;
		if(preg_match('/corresponding\s+author/ism', $lTextContent)){
			$lCurrentFn->setAttribute('fn-type', 'corresp');
		}elseif(preg_match('/academic\s+editor/ism', $lTextContent)){
			$lCurrentFn->setAttribute('fn-type', 'edited-by');
		}
	}

	//Слагаме атрибути на email таговете
	$lEmailQuery = '/article/front/article-meta/author-notes/fn//email';
	$lEmailNodes = $lXpath->query($lEmailQuery);
	for($i = 0; $i < $lEmailNodes->length; ++$i){
		$lCurrentEmail = $lEmailNodes->item($i);
		$lCurrentEmail->setAttributeNS(XLINK_NAMESPACE_URL, 'xlink:type', 'simple');
	}
}

/**
	Обработва се history тага. За повече информация
	https://projects.etaligent.net/attachments/305/07_-_history.xml
	https://projects.etaligent.net/issues/613
*/
function parseIndesignHistory(&$pXmlDom){
	$lXpath = new DOMXPath($pXmlDom);
	$lHistoryQuery = '/article/front/article-meta/history';
	$lHistoryNodeResult = $lXpath->query($lHistoryQuery);
	if(!$lHistoryNodeResult->length)
		return;
	$lHistoryNode = $lHistoryNodeResult->item(0);
	$lDatesQuery = './/date';
	$lDatesResult = $lXpath->query($lDatesQuery, $lHistoryNode);
	$lDateReceived = '';
	$lDateAccepted = '';
	//Първата дата е received, а втората - accepted

	if($lDatesResult->length){
		$lDateReceived = $lDatesResult->item(0)->textContent;
		if($lDatesResult->length >= 2){
			$lDateAccepted = $lDatesResult->item(1)->textContent;
		}
	}

	$lPubDateQuery = './/pub-date';
	$lPubDateResult = $lXpath->query($lPubDateQuery, $lHistoryNode);
	$lPubDate = '';
	if($lPubDateResult->length){
		$lPubDate = $lPubDateResult->item(0)->textContent;
	}

	//Добавяме <pub-date pub-type="collection"> и <pub-date pub-type="epub"> преди хисторито
	if($lPubDate){
		$lPubDateParts = splitDateToArray($lPubDate);
		//~ var_dump($lPubDate, $lPubDateParts);
		if($lPubDateParts !== false){
			$lCollectionTag = $lHistoryNode->parentNode->insertBefore($pXmlDom->createElement('pub-date'), $lHistoryNode);
			$lCollectionTag->setAttribute('pub-type', 'collection');
			$lCollectionYearTag = $lCollectionTag->appendChild($pXmlDom->createElement('year'));
			$lCollectionYearTag->appendChild($pXmlDom->createTextNode($lPubDateParts['year']));

			$lEpubTag = $lHistoryNode->parentNode->insertBefore($pXmlDom->createElement('pub-date'), $lHistoryNode);
			$lEpubTag->setAttribute('pub-type', 'epub');
			$lEpubTagDayTag = $lEpubTag->appendChild($pXmlDom->createElement('day'));
			$lEpubTagMonthTag = $lEpubTag->appendChild($pXmlDom->createElement('month'));
			$lEpubTagYearTag = $lEpubTag->appendChild($pXmlDom->createElement('year'));

			$lEpubTagDayTag->appendChild($pXmlDom->createTextNode($lPubDateParts['day']));
			$lEpubTagMonthTag->appendChild($pXmlDom->createTextNode(GetMonthNumber($lPubDateParts['month'])));
			$lEpubTagYearTag->appendChild($pXmlDom->createTextNode($lPubDateParts['year']));
		}
	}
	//Ако имаме дата received или accepted
	$lDateReceivedParts = splitDateToArray($lDateReceived);
	$lDateAcceptedParts = splitDateToArray($lDateAccepted);

	if($lDateReceivedParts !== false || $lDateAcceptedParts !== false){
		//Правим нов history таг и го слагаме след lpage тага
		$lNewHistoryTag = $pXmlDom->createElement('history');
		if($lDateReceivedParts !== false){
			$lReceivedTag = $lNewHistoryTag->appendChild($pXmlDom->createElement('date'));
			$lReceivedTag->setAttribute('date-type', 'received');
			$lReceivedTagDayTag = $lReceivedTag->appendChild($pXmlDom->createElement('day'));
			$lReceivedTagMonthTag = $lReceivedTag->appendChild($pXmlDom->createElement('month'));
			$lReceivedTagYearTag = $lReceivedTag->appendChild($pXmlDom->createElement('year'));

			$lReceivedTagDayTag->appendChild($pXmlDom->createTextNode($lDateReceivedParts['day']));
			$lReceivedTagMonthTag->appendChild($pXmlDom->createTextNode(GetMonthNumber($lDateReceivedParts['month'])));
			$lReceivedTagYearTag->appendChild($pXmlDom->createTextNode($lDateReceivedParts['year']));
		}
		if($lDateAcceptedParts !== false){
			$lAcceptedTag = $lNewHistoryTag->appendChild($pXmlDom->createElement('date'));
			$lAcceptedTag->setAttribute('date-type', 'accepted');
			$lAcceptedTagDayTag = $lAcceptedTag->appendChild($pXmlDom->createElement('day'));
			$lAcceptedTagMonthTag = $lAcceptedTag->appendChild($pXmlDom->createElement('month'));
			$lAcceptedTagYearTag = $lAcceptedTag->appendChild($pXmlDom->createElement('year'));

			$lAcceptedTagDayTag->appendChild($pXmlDom->createTextNode($lDateAcceptedParts['day']));
			$lAcceptedTagMonthTag->appendChild($pXmlDom->createTextNode(GetMonthNumber($lDateAcceptedParts['month'])));
			$lAcceptedTagYearTag->appendChild($pXmlDom->createTextNode($lDateAcceptedParts['year']));
		}

		//Търсим lpage
		$lLPageQuery = '/article/front/article-meta/lpage';
		$lLPageNodeResult = $lXpath->query($lLPageQuery);
		if($lLPageNodeResult->length){//Ако го намерим - слагаме новото history след него
			$lLPageNode = $lLPageNodeResult->item(0);
			if($lLPageNode->nextSibling){
				$lLPageNode->parentNode->insertBefore($lNewHistoryTag, $lLPageNode->nextSibling);
			}else{
				$lLPageNode->parentNode->appendChild($lNewHistoryTag);
			}
		}else{//Иначе го слагаме преди текущото history
			$lHistoryNode->parentNode->insertBefore($lNewHistoryTag, $lHistoryNode);
		}
	}
	//Трием самото History
	$lHistoryNode->parentNode->removeChild($lHistoryNode);

}

/**
	Обработва contrib тага.
	За повече информация https://projects.etaligent.net/issues/474
*/
function parseIndesignContribTags(&$pXmlDom){
	parseIndesignAffTags($pXmlDom);
	parseIndesignUriGroup($pXmlDom);
	splitIndesignContribTags($pXmlDom);
	processIndesignContribTags($pXmlDom);
}

/**
	Тази функция тагва имената на контрибуторите и слага линковете към адрес-а и lsid-a
	За повече информация - да се види parseIndesignContribTags
*/
function processIndesignContribTags(&$pXmlDom){
	$lXpath = new DOMXPath($pXmlDom);
	$lContribQuery = '/article/front/article-meta/contrib-group/contrib';
	$lContribNodes = $lXpath->query($lContribQuery);
	for($i = $lContribNodes->length - 1; $i >= 0; --$i){
		$lCurrentContrib = $lContribNodes->item($i);
		if($lXpath->query('.//name', $lCurrentContrib)->length){//Ако вече е тагнато - продължаваме нататък
			continue;
		}
		//В тези масиви ще пазим ид-тата на адресите/лсид-овете на текущия автор
		$lAddressesArr = array();
		$lLsidsArr = array();
		$lXrefQuery = './/xref';
		$lXrefNode = $lXpath->query($lXrefQuery, $lCurrentContrib);
		if($lXrefNode->length){//Ако има такъв възел, сплитваме съдържанието му на база запетайка
			$lXref = $lXrefNode->item(0);
			$lXrefContent = trim($lXref->textContent);
			//Махаме xref възела за да можем да вземем след това само имената на автора
			$lXref->parentNode->removeChild($lXref);

			$lXrefArr = explode(',', $lXrefContent);
			foreach($lXrefArr as $lCurrentXref){
				$lCurrentXref = trim($lCurrentXref);
				if(is_numeric($lCurrentXref)){//Числата са адреси
					$lAddressesArr[] = $lCurrentXref;
				}else{//Символите са lsid
					$lLsidsArr[] = $lCurrentXref;
				}
			}
		}
		if(!count($lAddressesArr)){//Ако няма адреси, а в xml-a има адреси слагаме линк към първия
			$lAffQuery = '/article/front/article-meta/aff[@id=\'A1\']';
			$lAffNodes = $lXpath->query($lAffQuery);
			if($lAffNodes->length){
				$lAddressesArr[] = 1;
			}
		}
		$lContribName = trim($lCurrentContrib->textContent);
		$lContribName = preg_replace('/\s+/ism', ' ', $lContribName);//Махаме двойните интервали
		$lSpacePosition = strrpos($lContribName, ' ');
		$lContribFirstNames = '';
		$lContribLastName = $lContribName;
		if($lSpacePosition){
			$lContribFirstNames = mb_substr($lContribName, 0, $lSpacePosition);
			$lContribLastName = mb_substr($lContribName, $lSpacePosition + 1);
		}
		$lCurrentContrib->setAttribute('contrib-type', 'author');
		$lCurrentContrib->setAttributeNs(XLINK_NAMESPACE_URL, 'xlink:type', 'simple');

		//Махаме текста от възела
		while($lCurrentContrib->firstChild){
			$lCurrentContrib->removeChild($lCurrentContrib->firstChild);
		}
		$lNameNode = $lCurrentContrib->appendChild($pXmlDom->createElement('name'));
		$lNameNode->setAttribute('name-style', 'western');
		$lSurnameNode = $lNameNode->appendChild($pXmlDom->createElement('surname'));
		$lGivennameNode = $lNameNode->appendChild($pXmlDom->createElement('given-names'));
		$lSurnameNode->appendChild($pXmlDom->createTextNode($lContribLastName));
		$lGivennameNode->appendChild($pXmlDom->createTextNode($lContribFirstNames));

		//Добавяме референции към адресите
		foreach($lAddressesArr as $lAddressId){
			$lAffQuery = '/article/front/article-meta/aff[@id=\'A' . $lAddressId . '\']';
			if(!$lXpath->query($lAffQuery)->length)//Ако няма такъв адрес - продължаваме
				continue;
			$lXrefNode = $lCurrentContrib->appendChild($pXmlDom->createElement('xref'));
			$lXrefNode->setAttribute('ref-type', 'aff');
			$lXrefNode->setAttribute('rid', 'A' . $lAddressId);
			$lXrefNode->appendChild($pXmlDom->createTextNode($lAddressId));
		}
		//Копираме възлите с lsid-овете
		foreach($lLsidsArr as $lLsidLabel){
			$lLsidQuery = '/article/front/article-meta/uri-group/uri[@leadto=\'' . $lLsidLabel . '\']';
			$lLsidNodeResult = $lXpath->query($lLsidQuery);
			if(!$lLsidNodeResult->length)//Ако няма такъв lsid - продължаваме
				continue;
			$lLsidNode = $lLsidNodeResult->item(0);

			$lLsidCopyNode = $lCurrentContrib->appendChild($lLsidNode->cloneNode(true));
			//Махаме leadto атрибута
			$lLsidCopyNode->removeAttribute('leadto');
		}
	}

	//Махаме fake-uri таговете
	$lUriHolderQuery = '/article/front/article-meta/uri-group';
	$lUriHolders = $lXpath->query($lUriHolderQuery);

	for($i = 0; $i< $lUriHolders->length; ++$i){
		$lUriHolder = $lUriHolders->item($i);
		$lUriHolder->parentNode->removeChild($lUriHolder);
	}
}

/**
	Тази функция сплитва общия контриб таг на множество единични контриб тагове
	използвайки за разделител запетайка.
	За повече информация - да се види parseIndesignContribTags
*/
function splitIndesignContribTags(&$pXmlDom){
	$lXpath = new DOMXPath($pXmlDom);
	$lContribGroupQuery = '/article/front/article-meta/contrib-group';
	$lContribGroupNodes = $lXpath->query($lContribGroupQuery);
	for($i = $lContribGroupNodes->length - 1; $i >= 0; --$i){
		$lCurrentContribGroup = $lContribGroupNodes->item($i);
		$lCurrentContribNodes = $lXpath->query('.//contrib', $lCurrentContribGroup);
		if($lCurrentContribNodes->length){//Ако вече е тагнато - продължаваме нататък
			splitTextNodesByRegularExpression($lCurrentContribNodes->item(0), '/\,/ism', 'contrib');
			continue;
		}
		$lCurrentContrib = $pXmlDom->CreateElement('contrib');
		/**
			Местим децата в нов contrib и после в contrib-а търсим запетайки и правим нови contrib
		*/
		while($lCurrentContribGroup->hasChildNodes()){
			$lCurrentContrib->appendChild($lCurrentContribGroup->firstChild);
		}
		$lCurrentContrib = $lCurrentContribGroup->appendChild($lCurrentContrib);
		splitTextNodesByRegularExpression($lCurrentContrib, '/\,/ism', 'contrib');
	}
}

/**
	Парсва адресите на контрибуторите
	За повече информация да се види parseIndesignContribTags
*/
function parseIndesignAffTags(&$pXmlDom){
	$lXpath = new DOMXPath($pXmlDom);
	$lTagQuery = '/article/front/article-meta/aff';
	$lTags = $lXpath->query($lTagQuery);
	if(!$lTags->length)
		return;
	for($i = 0; $i< $lTags->length; ++$i){
		$lTagHolder = $lTags->item($i);

		$lLabelQuery = './/bold';
		$lContentQuery = './/italic';
		$lContentTags = $lXpath->query($lContentQuery, $lTagHolder);
		$lLabelTags = $lXpath->query($lLabelQuery, $lTagHolder);
		$lTagHolderParent = $lTagHolder->parentNode;
		for($j = 0; $j < $lContentTags->length; ++$j){
			$lContentTag = $lContentTags->item($j);
			$lContent = trim($lContentTag->textContent);
			$lNewTag =  $pXmlDom->createElement('aff');

			$lLabelContent = ($j + 1);
			if($lLabelTags->length > $j){
				$lLabelContent = trim($lLabelTags->item($j)->textContent);
			}
			$lLabelTag = $lNewTag->appendChild($pXmlDom->createElement('label'));
			$lLabelTag->nodeValue = $lLabelContent;
			$lNewTag->setAttribute('id', 'A' . $lLabelContent);

			$lNewTag->appendChild($pXmlDom->createTextNode($lContent));
			$lTagHolderParent->insertBefore($lNewTag, $lTagHolder);
		}
		$lTagHolderParent->removeChild($lTagHolder);
	}
}

/**
	Парсва lsid адресите на контрибуторите
	За повече информация да се види parseIndesignContribTags
*/
function parseIndesignUriGroup(&$pXmlDom){
	$lXpath = new DOMXPath($pXmlDom);
	$lTagHolderQuery = '/article/front/article-meta/uri';
	$lTagHolders = $lXpath->query($lTagHolderQuery);
	if(!$lTagHolders->length)
		return;
	//Ще слагаме новите възли във фалшив holder, който ще трием след обработката на контрибуторите
	for($i = 0; $i< $lTagHolders->length; ++$i){
		$lTagHolder = $lTagHolders->item($i);
		$lFakeTagHolder = $lTagHolder->parentNode->appendChild($pXmlDom->createElement('uri-group'));
		$lLabelQuery = './/bold';
		$lContentQuery = './/italic';
		$lContentTags = $lXpath->query($lContentQuery, $lTagHolder);
		$lLabelTags = $lXpath->query($lLabelQuery, $lTagHolder);
		$lTagHolderParent = $lTagHolder->parentNode;
		for($j = $lContentTags->length - 1; $j >=0; --$j){
			$lContentTag = $lContentTags->item($j);
			$lContent = trim($lContentTag->textContent);
			if($lLabelTags->length > $j){//Zadyljitelno za vsqko uri trqbva da ima label
				$lNewTag =  $pXmlDom->createElement('uri');
				$lNewTag->setAttribute('content-type', 'lsid');
				$lNewTag->setAttribute('leadto', trim($lLabelTags->item($j)->textContent));
				$lNewTag->setAttributeNS(XLINK_NAMESPACE_URL, 'xlink:type', 'simple');
				$lNewTag->appendChild($pXmlDom->createTextNode($lContent));
				$lFakeTagHolder->appendChild($lNewTag);
				//Mahame obrabotenite vyzli
				$lTagHolder->removeChild($lContentTag);
				$lTagHolder->removeChild($lLabelTags->item($j));
			}

		}
		$lTagHolderParent->removeChild($lTagHolder);
	}
}

//Premestva vsi4ki element childove na opredeleni tagove predi samite tqh i sled tova trie tezi tagove
function parseIndesignXmlIgnoreTags(&$pXmlDom){
	$lXpath = new DOMXPath($pXmlDom);
	$lIgnoreQuery = '//' . IGNORE_TAG_NAME;
	$lIgnoreNodes = $lXpath->query($lIgnoreQuery);
	for( $i = $lIgnoreNodes->length - 1; $i >= 0; --$i){
		$lCurrentNode = $lIgnoreNodes->item($i);
		$lCurrentChild = $lCurrentNode->firstChild;
		$lParent = $lCurrentNode->parentNode;
		while($lCurrentChild){
			$lChildToAppend = $lCurrentChild;
			$lCurrentChild = $lCurrentChild->nextSibling;
			if( $lChildToAppend->nodeType == XML_ELEMENT_NODE || $lChildToAppend->nodeType == XML_COMMENT_NODE  ){//Obrabotvame samo element i comment node-ovete
				$lParent->insertBefore($lChildToAppend, $lCurrentNode);
			}
		}
		$lParent->removeChild($lCurrentNode);
	}
}

/*
	Обработваме таблиците
	<table-wrap>   dobavya se attribute   -->   <table-wrap content-type="key">

	<tr>xxx tab xxx tab xxx</tr>

	da se convertira v:

	<tr>
		<td>xxx</td>
		<td>xxx</td>
		<td>xxx</td>
	</tr>
*/
function parseIndesignTableTags(&$pXmlDom){
	$lXpath = new DOMXPath($pXmlDom);
	$lTableQuery = '//table-wrap';
	$lTableNodes = $lXpath->query($lTableQuery);
	for($i = $lTableNodes->length - 1; $i >= 0; --$i){
		$lCurrentTable = $lTableNodes->item($i);
		$lCurrentTable->setAttribute('content-type', 'key');
		$lTrQuery = './/tr';
		$lTrNodes = $lXpath->query($lTrQuery, $lCurrentTable);
		for($j = $lTrNodes->length - 1; $j >= 0; --$j){
			$lCurrentTr = $lTrNodes->item($j);
			$lCurrentTd = $pXmlDom->CreateElement('td');
			/**
				Местим децата на реда в ново td и после в td-то търсим табове и правим ново тд
			*/
			while($lCurrentTr->hasChildNodes()){
				$lCurrentTd->appendChild($lCurrentTr->firstChild);
			}
			$lCurrentTd = $lCurrentTr->appendChild($lCurrentTd);
			splitTextNodesByRegularExpression($lCurrentTd, '/\t+/ism', 'td');
		}
	}
}

/**
	В директните деца от текстов тип на подадения възел търсим подадения regular_expression
	Ако намерим - местим следващите го неща в нов element node от дадения тип на същото ниво и го обработваме него
	Важно е да се отбележи, че намерения regular_expression се маха, т.е. разделителите изчезват
*/
function splitTextNodesByRegularExpression(&$pCurrentNode, $pSplitExpression, $pNodeName){
	$lNextNode = null;
	for($i = 0; $i < $pCurrentNode->childNodes->length; ++$i){
		$lCurrentChild = $pCurrentNode->childNodes->item($i);
		if($lCurrentChild->nodeType != XML_TEXT_NODE){
			continue;
		}
		$lTextContent = $lCurrentChild->textContent;

		if(preg_match($pSplitExpression, $lTextContent, $lMatch, PREG_OFFSET_CAPTURE)){
			$lOwnerDocument = $pCurrentNode->ownerDocument;
			$lPos = $lMatch[0][1];
			$lMatchedString = $lMatch[0][0];
			$lMatchedStringLength = mb_strlen($lMatchedString);
			$lBeforeTxt = mb_substr($lTextContent, 0, $lPos);
			$lAfterTxt = mb_substr($lTextContent, $lPos + $lMatchedStringLength);
			//Pravim novo td samo ako imame oshte tekst ili oshte elementi
			if($lAfterTxt != '' || $lCurrentChild->nextSibling){
				$lNewElement = $lOwnerDocument->createElement($pNodeName);
				if($lAfterTxt != ''){
					$lNewElement->appendChild($lOwnerDocument->createTextNode($lAfterTxt));
				}
				while($lCurrentChild->nextSibling){
					$lNewElement->appendChild($lCurrentChild->nextSibling);
				}
			}
			if($lBeforeTxt != ''){
				$pCurrentNode->appendChild($lOwnerDocument->createTextNode($lBeforeTxt));
			}
			$pCurrentNode->removeChild($lCurrentChild);
			if($pCurrentNode->nextSibling){
				$lNextNode = $pCurrentNode->parentNode->insertBefore($lNewElement, $pCurrentNode->nextSibling);
			}else{
				$lNextNode = $pCurrentNode->parentNode->appendChild($lNewElement);
			}
			break;
		}
	}
	if($lNextNode){
		splitTextNodesByRegularExpression($lNextNode, $pSplitExpression, $pNodeName);
	}
}

//Splitva dadeni nodove, koito sydyrjat split node child. pTagsToSplit - imenata na tagovete sred koito tyrsim
function parseIndesignXmlBreakTags(&$pXmlDom){
	$lXPaths = getBreakTagsXpaths();
	if( is_array( $lXPaths ) ){
		$lXpath = new DOMXPath($pXmlDom);
		$lXpath->registerNamespace('tp', 'TP_NAMESPACE_URL');
		foreach( $lXPaths as $lRuleId => $lRuleData ){
			$lCurrentXPath = $lRuleData['xpath'];
			$lXPathNodes = $lXpath->query($lCurrentXPath);
			for( $i = $lXPathNodes->length - 1; $i >= 0; --$i ){
				$lParentNode = $lXPathNodes->item($i);
				$lTagQuery = './/' . SPLIT_TAG_NAME;
				$lNodesToSplit = $lXpath->query($lTagQuery, $lParentNode);
				for( $j = $lNodesToSplit->length - 1; $j >= 0; --$j ){
					$lCurrentClimb = $lRuleData['climb_up'];
					$lCurrentSplitNode = $lNodesToSplit->item($j);
					$lCurrentNode = $lCurrentSplitNode;
					$lNewNode = null;
					$lSiblingFound = false;
// 					var_dump($lCurrentNode->ownerDocument->saveXML($lCurrentNode->parentNode));
// 					echo "\n";
					while($lCurrentNode && $lCurrentNode !== $lParentNode ){//Obikalqme nadolu po dyrvoto i slagame node-ovete v nov node
						$lNewParent = tagSplitProcessNodeLevel($pXmlDom, $lSiblingFound, $lNewNode, $lCurrentNode);
					}

					while($lCurrentNode && $lCurrentNode->parentNode != $pXmlDom && $lCurrentClimb > 0){
						$lNewParent = tagSplitProcessNodeLevel($pXmlDom, $lSiblingFound, $lNewNode, $lCurrentNode);
						$lCurrentClimb--;
					}
					$lParent = $lCurrentNode->parentNode;
					if( $lNewNode && $lSiblingFound ){
						$lNextSibling = $lCurrentNode->nextSibling;
						if( $lNextSibling ){
							$lCurrentNode->parentNode->insertBefore( $lNewParent, $lNextSibling );
						}else{
							$lCurrentNode->parentNode->appendChild( $lNewParent );
						}
					}
// 					var_dump($lParent->ownerDocument->saveXML($lParent));
// 					echo "\n\n";

				}
			}

		}
	}
	//~ exit;
}

//Обработва дадено ниво във възела, в който има сплит таг - обикаля всички следващи sibling-и на това ниво и ги премества в новия възел, който ще се добави след текущия
function tagSplitProcessNodeLevel(&$pXmlDom, &$pSiblingFound, &$pNewNode, &$pCurrentNode){
	if( $pCurrentNode->parentNode ){
		$lNewParent = $pXmlDom->createElement($pCurrentNode->parentNode->nodeName);
		if( $pNewNode )
			$lNewParent->appendChild( $pNewNode );
		$lCurrentSibling = $pCurrentNode->nextSibling;

		while( $lCurrentSibling ){
			$pSiblingFound = true;
			if( $lCurrentSibling->nodeType == XML_ELEMENT_NODE  || $lCurrentSibling->nodeType == XML_TEXT_NODE || $lCurrentSibling->nodeType == XML_COMMENT_NODE){
				$lNewParent->appendChild( $lCurrentSibling );//Важно е, че lCurrentSibling вече се мести в новия възел и изчезва от текущия
			}
			$lCurrentSibling = $pCurrentNode->nextSibling;//Понеже lCurrentSibling вече е в новия възел и е изчезнал от текущия възел
		}
		$pNewNode = $lNewParent;
		$pCurrentNode = $pCurrentNode->parentNode;
	}
	return $lNewParent;
}

//Връща масив от xpath expression-и, в които ще се търсят брейк тагове за да се сплитнат
function getBreakTagsXpaths(){
	$lResult = array();
	$lCon = Con();
	$lSql = 'SELECT id, xpath, climb_up FROM node_split';
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	while(!$lCon->eof()){
		$lId = (int) $lCon->mRs['id'];
		$lClimbUp = (int) $lCon->mRs['climb_up'];
		$lXPath = $lCon->mRs['xpath'];
		$lResult[$lId] = array('xpath' => $lXPath, 'climb_up' => $lClimbUp);
		$lCon->MoveNext();
	}
	return $lResult;
}

function removeIndesignXmlBoldTags(&$pXmlDom){
	$lXPaths = getFormattingBoldTagsXpaths();
	removeIndesignXmlFormattingTagsHelper($pXmlDom, array('bold'), $lXPaths);
}

function removeIndesignXmlItalicTags(&$pXmlDom){
	$lXPaths = getFormattingItalicTagsXpaths();
	removeIndesignXmlFormattingTagsHelper($pXmlDom, array('italic'), $lXPaths);
}

function removeIndesignXmlFormattingTags(&$pXmlDom){
	removeIndesignXmlBoldTags($pXmlDom);
	removeIndesignXmlItalicTags($pXmlDom);
}

/**
	Маха подадените възлите  от определени тагове
	За целта мести децата им преди самите тях и ги трие

	pFormattingTags - масив с имената на таговете, които ще махаме
		например - array('bold')
	pXPaths - масив с xpath експрешъни в които ще търсим горните тагове. Формата е следния
		ид на правило => масив със следния формат
			xpath => XPathExpr
				където XPathExpr е дадения xpath expr
		например
			array(
			  [1]=>
			  array(1) {
				["xpath"]=>
				string(35) "//article_figs_and_tables/fig/label"
			  }
			  [2]=>
			  array(1) {
				["xpath"]=>
				string(46) "//front/article-meta/title-group/article-title"
			  }
			  [3]=>
			  array(1) {
				["xpath"]=>
				string(32) "/article/front/meta/author-notes"
			  }
			)

*/
function removeIndesignXmlFormattingTagsHelper(&$pXmlDom, $pFormattingTags, $pXPaths){

	if( is_array( $pXPaths ) ){
		$lXpath = new DOMXPath($pXmlDom);
		$lXpath->registerNamespace('tp', 'TP_NAMESPACE_URL');
		foreach( $pXPaths as $lRuleId => $lRuleData ){
			$lCurrentXPath = $lRuleData['xpath'];
			$lXPathNodes = $lXpath->query($lCurrentXPath);

			/**
				Обикаляме възлите наобратно за да не инвалидираме останалите, ако почнем от 1я
			*/
			for( $i = $lXPathNodes->length - 1; $i >= 0; --$i ){
				$lCurrentNode = $lXPathNodes->item($i);
				//~ $lFormattingTags = array('italic', 'bold');
				//~ $lFormattingTags = array('bold');
				foreach($pFormattingTags as $lCurrentFormattingTag){
					$lTagQuery = './/' . $lCurrentFormattingTag;
					$lNodesToRemove = $lXpath->query($lTagQuery, $lCurrentNode);
					for( $j = $lNodesToRemove->length - 1; $j >= 0; --$j ){
						$lCurrentFormattingNode = $lNodesToRemove->item($j);
						$lParentNode = $lCurrentFormattingNode->parentNode;
						//Местим децата
						while($lCurrentFormattingNode->hasChildNodes()){
							$lChild = $lCurrentFormattingNode->firstChild;
							$lParentNode->insertBefore($lChild, $lCurrentFormattingNode);
						}
						//Трием самия форматиращ възел
						$lParentNode->removeChild($lCurrentFormattingNode);
					}
				}
			}

		}
	}
}

/**
	Връща масив от xpath expression-и, в които ще се търсят bold тагове за да се махнат
	Ако е подаден параметър pGetItalicTags връща xpath expression-и, в които ще се търсят italic тагове
*/
function getFormattingBoldTagsXpaths($pGetItalicTags = false){
	$lResult = array();
	$lCon = Con();
	$lTable = 'indesign_remove_bold_formatting_nodes';
	if($pGetItalicTags){
		$lTable = 'indesign_remove_italic_formatting_nodes';
	}
	$lSql = 'SELECT id, xpath FROM ' . $lTable;
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	while(!$lCon->eof()){
		$lId = (int) $lCon->mRs['id'];
		$lXPath = $lCon->mRs['xpath'];
		$lResult[$lId] = array('xpath' => $lXPath);
		$lCon->MoveNext();
	}
	return $lResult;
}

function getFormattingItalicTagsXpaths(){
	return getFormattingBoldTagsXpaths(true);
}


function showNewParent($pRs){
	return showYesNo($pRs['new_parent']);
}

function showIsNeeded($pRs){
	return showYesNo($pRs['is_needed']);
}

function showChangeBefore($pRs){
	return showYesNo($pRs['change_before']);
}

function showChangeAfter($pRs){
	return showYesNo($pRs['change_after']);
}

function showAutotagAnnotateShow($pRs){
	return showYesNo($pRs['autotag_annotate_show']);
}


function yesNoAppendedToXml($pRs){
	return showYesNo($pRs['appended_to_xml_file']);
}

function yesNoIsUploaded($pRs){
	return showYesNo($pRs['is_uploaded']);
}

function yesNoUploadHasErrors($pRs){
	return showYesNo($pRs['upload_has_errors']);
}

function showYesNo($pParam){
	if((int)$pParam )
		return getstr('global.yes');
	return getstr('global.no');
}
/**
	Тук инициализираме съдържанието на балоните в експортната статия в HTML формат.
	Взимаме всички таксони и за всеки таксон взимаме съдържанието от ptp сайта, където би трябвало да се взима статично, за да не бави показването на статията.
	За всеки от таксоните вкарваме запис в таблицата за генериране на кеш за таксони
	и по-късно чрез отложен скрипт генерираме кеша, за да не бавим показването на статията тук.
*/
function GetHtmlBallons($pXSLResult){
	$lTaxonNamePattern = '/\{\*\*_(.*)_\*\*\}/';
	$lTaxonDivPattern = '/\{\$\$__\$\$\}/';
	error_reporting(E_ALL);
	ini_set('display_errors', 'Off');
	if( preg_match_all( $lTaxonNamePattern, $pXSLResult, $lMatch ) ){
		$lTaxonNames = array();
		$lTaxonNamesParsed = array();
		foreach( $lMatch[1] as $lSingleMatch ){
			$lTaxonNamesParsed = ParseTaxonNameLink($lSingleMatch, 0, 1);
			foreach($lTaxonNamesParsed as $lSingleParsedtaxonName) {
				$lTaxonNames[] = $lSingleParsedtaxonName;
			}
		}
		$lTaxonNames = array_unique( $lTaxonNames );
		$lTaxonDivs = '';
		$lTaxonCacheSql = '';

		//Взимаме съдържанието на балоните
		foreach($lTaxonNames as $lSingleTaxonName){
			$lLinks = GetLinksArray($lSingleTaxonName, false);
			$lCurrentTaxonDiv = new csimple(array(
				'templs' => array(
					G_DEFAULT => 'article_html.taxonBaloonDiv',
				),

				'gbif_href' => $lLinks['gbif']['href'],
				'gbif_title' => $lLinks['gbif']['title'],
				'ncbi_href' => $lLinks['ncbi']['href'],
				'ncbi_title' => $lLinks['ncbi']['title'],
				'eol_href' => $lLinks['eol']['href'],
				'eol_title' => $lLinks['eol']['title'],
				'biodev_href' => $lLinks['biodev']['href'],
				'biodev_title' => $lLinks['biodev']['title'],
				'wikipedia_href' => $lLinks['wikipedia']['href'],
				'wikipedia_title' => $lLinks['wikipedia']['title'],

				'cache' => 'article_html_taxon_baloon',
				'cachetimeout' => CACHE_TIMEOUT_LENGTH,
				'taxon_name' => $lSingleTaxonName,
			));
			//~ trigger_error("Exec: " . $lSingleTaxonName , E_USER_NOTICE);
			$lTaxonDivs .= $lCurrentTaxonDiv->DisplayC();
			//Строим sql-a за скрипта за кеша
			$lTaxonCacheSql .= 'SELECT * FROM spCreateTaxonCacheEntry(\'' . q($lSingleTaxonName) . '\');';
		}
		//Изпълняваме sql-a за скрипта за кеша. По-късно отложения скрипт ще мине и ще генерира кеша
		if ($lTaxonCacheSql != '') {
			$lCon = Con();
			$lCon->CloseRs();
			$lCon->Execute($lTaxonCacheSql);
		}


		$pXSLResult = preg_replace($lTaxonNamePattern, '', $pXSLResult);
		$pXSLResult = preg_replace($lTaxonDivPattern, $lTaxonDivs, $pXSLResult);
	}
	return $pXSLResult;

}

/**
 *
 * Ако $pParseType е 0 връща html, в който са поставени линкове за таксоните
 * към ptp сайта. Ако е подаден и параметъра pPutHover се слагат hover-ите за балоните.
 * Ако таксонът се състои от няколко думи и първата започва с главна буква (напр. Genus indet) на всяка от думите се поставя линк,
 * като на думите с главна буква се слага линк към страниците на самите тях, а на останалите - линк към целия таксон
 * (тук на Genus ще се постави линк към Genus, а на indet - към Genus indet)
 * Ако $pParseType е 1 връща масив с различните части на таксона - т.е. думите с големи букви и цялото име на таксона
 * (за горния пример резултата ще е
 * 		array('Genus', 'Genus indet')
 * )
 * @param unknown_type $pTaxonName
 * @param unknown_type $pPut_Hover
 * @param unknown_type $pParseType
 */
function ParseTaxonNameLink($pTaxonName, $pPut_Hover, $pParseType = 0) {

	$lTaxonNamesArr = array();
	$lTaxonNamesArr = split(' ', trim($pTaxonName));

	if(!(int)$pParseType) {
		//връща парснатите имена на таксоните като линкове с hover-а
		$lRes = '<span>';
		$lLink = TAXON_NAME_LINK;
		$lLinkClassName = 'taxonNameLink';
		if((int)count($lTaxonNamesArr)) {
			foreach($lTaxonNamesArr as $k => $v) {
				if(substr($v, 0, 1) == '(' || substr($v, strlen($v) - 1) == ')') {
					$lTxName = substr($v, 1, -1);
				} else {
					$lTxName = $v;
				}
				$lTxName = trim($lTxName);
				if(preg_match('([A-Z])', substr($lTxName, 0, 1))) {
					$lParsedTaxonName = trim($lTxName);
					$lParsedTaxonName = preg_replace('/\.|\s+/i', '_', $lParsedTaxonName);

					$lRes .= '<a target="_blank" class="' . $lLinkClassName . '" href="' . $lLink . $lTxName . '" ' . ((int)$pPut_Hover > 0 ? 'onMouseOver="showBaloon(2, \'' . $lParsedTaxonName . '\', event)" onMouseOut="hideBaloonEvent(\'' . $lParsedTaxonName . '\', event)"' : '') . '>' . $v . '</a> ';
				} else {
					$lParsedTaxonName = trim($pTaxonName);
					$lParsedTaxonName = preg_replace('/\.|\s+/i', '_', $lParsedTaxonName);
					$lRes .= '<a target="_blank" class="' . $lLinkClassName . '" href="' . $lLink . $pTaxonName . '" ' . ((int)$pPut_Hover > 0 ? 'onMouseOver="showBaloon(2, \'' . $lParsedTaxonName . '\', event)" onMouseOut="hideBaloonEvent(\'' . $lParsedTaxonName . '\', event)"' : '') . '>' . $v . '</a> ';
				}
			}
		} else {
			$lRes .= $pTaxonName;
		}
		$lRes = trim($lRes);
		$lRes .= '</span>';

		$lxml = $lRes;
		$lDoc = new DOMDocument;
		$lDoc->loadXml($lxml);
		return $lDoc;
	} else {
		//връща масив от парснатите имена на таксоните (това е за балончетата при mouseover)
		$lParsedTaxonNamesArr = array();
		if((int)count($lTaxonNamesArr)) {
			foreach($lTaxonNamesArr as $k => $v) {
				if(substr($v, 0, 1) == '(' || substr($v, strlen($v) - 1) == ')') {
					$lTxName = substr($v, 1, -1);
				} else {
					$lTxName = $v;
				}

				if(preg_match('([A-Z])', substr($lTxName, 0, 1))) {
					$lParsedTaxonNamesArr[] = $lTxName;
				} else {
					$lParsedTaxonNamesArr[] = $pTaxonName;
				}
			}
		} else {
			$lParsedTaxonNamesArr[] = $pTaxonName;
		}

		return $lParsedTaxonNamesArr;
	}
}

function getSingleTaxonBaloon($pTaxonName){
	$lUrl = TAXON_BALOON_SRV . '?taxon_name=' . rawurlencode($pTaxonName);
	return executeExternalQuery($lUrl);
}

function parseTaxonNameForBaloon($pTaxonName){
	return preg_replace('/\.|\s+/i', '_', $pTaxonName);
}

function parseContribName($pFullName, $pReturnFirstName = 1){
	$pFullName = trim($pFullName);
	$lLastSpace = mb_strrpos($pFullName, ' ');
	$lFirstName = $pFullName;
	$lLastName = '';
	if( $lLastSpace !== false ){
		$lFirstName = trim(mb_substr($pFullName, 0, $lLastSpace));
		$lLastName = trim(mb_substr($pFullName, $lLastSpace + 1));
	}
	if( $pReturnFirstName )
		return $lFirstName;
	return $lLastName;
}

function getContribSurname($pFullName){
	return parseContribName($pFullName, 0);
}

function getContribGivenNames($pFullName){
	return parseContribName($pFullName);
}

function parseContribUriAffNum($pFakeAddrUriText, $pReturnAffNum = 1){
	$pFakeAddrUriText = trim($pFakeAddrUriText);
	$lLastComa = mb_strrpos($pFakeAddrUriText, ',');
	$lAddrNum = '';
	$lUriSym = $pFakeAddrUriText;
	if( $lLastComa !== false ){
		$lAddrNum = trim(mb_substr($pFakeAddrUriText, 0, $lLastComa));
		$lUriSym = trim(mb_substr($pFakeAddrUriText, $lLastComa + 1));
	}
	if( $pReturnAffNum )
		return $lAddrNum;
	return $lUriSym;

}

function getContribAffNum($pFakeAddrUriText){
	return parseContribUriAffNum($pFakeAddrUriText);
}

function getContribUriSym($pFakeAddrUriText){
	return parseContribUriAffNum($pFakeAddrUriText, 0);
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

function bhl_showvolume($pVolume) {
	if ($pVolume)
		return $pVolume . ":";
	else
		return '';
}

function bhl_writecomma($pCount, $pCounter){
	if ($pCounter < $pCount)
		return ', ';
	else
		return '';
}

function cb_bhl($pCurl, $pBuf) {//callback funkcia pri chetene na xml-a ot BHL
	global $gBHLData;
	$gBHLData .= $pBuf;
	$lLen = strlen($gBHLData);
	//~ if ($_SERVER['REMOTE_ADDR'] == '193.194.140.198') {
		//~ echo $lLen . "??" . BHL_MAX_READ_LEN;
	//~ }
	if ((int)$lLen > (int)BHL_MAX_READ_LEN) {
		return 0;
	}
	return strlen($pBuf);
}


function bhl_showimage($pTaxonName, $pImgUrl, $pImg, $pNodata) {
	if ($pNodata)
		return '';
	else
		return '<a class="bhl-img" href="' . ParseTaxonExternalLink($pTaxonName, $pImgUrl) . '"><img border="0" align="right" src="' . $pImg . '"></img></a>';
}

function GetFormattedXml($pXml, $pReplaceBrSymbol = 0){
	if( $pReplaceBrSymbol ){
		$pXml = str_replace("<" . SPLIT_TAG_NAME . "/>", "\n", $pXml);
		$pXml = str_replace("<" . SPLIT_TAG_NAME . "></" . SPLIT_TAG_NAME . ">", "\n", $pXml);
	}
	return formatXmlString($pXml);
}

function showSpecial($pRs){
	$lSpecial = (int) $pRs['special'];
	switch($lSpecial){
		default:
		case 0: return getstr('admin.indesign_templates_details.specialNo');
		case 1: return getstr('admin.indesign_templates_details.specialTable');
		case 2: return getstr('admin.indesign_templates_details.specialFigure');
	}
}

/** XML FORMAT START - следващите функции реализират форматиране на xml-a */
function formatXmlString($pXml) {
	//~ return $pXml;
	$lDOM = new DOMDocument("1.0");
	$lDOM->preserveWhiteSpace = true;
	$lDOM->resolveExternals = true;
	if (!$lDOM->loadXML($pXml)){
		return $pXml;
	}
	$lXpath  = new  DOMXPath($lDOM);
	$lContent = '';
	$lDeclaredNamespaces = array();
	formatXmlRecursive($lDOM->documentElement, $lContent, $lXpath,  $lDeclaredNamespaces, 0);
	$lContent = '<?xml version="' . $lDOM->version . '"?>' . parseXmlDoctype($lDOM->doctype) . $lContent;
	return $lContent;
}

function parseXmlDoctype(&$pDoctype){
	if( $pDoctype ){
		return $pDoctype->internalSubset;
	}
	return '';
}

function formatXmlRecursive(&$pCurrentElement, &$pContent, &$pXPath, &$pDeclaredNamespaces, $pDepth = 0, $pFormatXml = 1){
	/**
		Ако $pFormatXml е 0 не форматираме - не слагаме нови редове и форматиращи символи. Това се случва само при определени тагове - p, title ...
	*/
	$lFormatXml = $pFormatXml;
	if( in_array($pCurrentElement->nodeType, array(1, 8))){//Element ili comment
		$lPreviousSibling = $pCurrentElement->previousSibling;
		while( $lPreviousSibling && !in_array($lPreviousSibling->nodeType, array(1, 3, 8) )){//Otivame do predniq sibling koito e tekst, comment ili element
			$lPreviousSibling = $lPreviousSibling->previousSibling;
		}

		$lPrevSiblingData = CheckPreviousSibling($lPreviousSibling, $pDepth);


		$lHasElementChildren = false;
		$lHasCommentChildren = false;
		$lLastTextChild = false;

		if( $lFormatXml )//За да може да го спрем рекурсивно ако веднъж е станало 0
			$lFormatXml = !CheckIfNodeIsNotToBeFormatted($pCurrentElement);

		$lTagStart = GetElementOpenTag($pCurrentElement, $pXPath, &$pDeclaredNamespaces);
		if( $pCurrentElement->nodeType == 1 ){//Element
			$lChildren = $pCurrentElement->childNodes;
			$lTagContent = '';
			for($i = 0; $i < $lChildren->length; $i++){
				$lChild = $lChildren->item($i);

				if( !in_array($lChild->nodeType, array(1, 3, 8) )){
					continue;
				}
				if( $lChild->nodeType == 1 ){
					$lHasElementChildren = true;
					$lLastTextChild = null;//Za da slagame nov red ako posledniq tekstov child e predi element child
				}elseif( $lChild->nodeType == 3){
					$lLastTextChild = $lChild;
				}elseif( $lChild->nodeType == XML_COMMENT_NODE){
					$lHasCommentChildren = true;
				}

				formatXmlRecursive($lChild, $lTagContent, $pXPath, &$pDeclaredNamespaces, $pDepth + 1, $lFormatXml);
			}
		}else{//Comment
			$lTagContent = EscapeTextContent($pCurrentElement->nodeValue);
		}

		$lTagEnd = GetElementCloseTag($pCurrentElement);
		$lHasMeaningfulChildren = $lHasElementChildren ? true : $lHasCommentChildren;
		if( !CheckForEmptyFormattingTag($pCurrentElement, $lHasElementChildren) ){
			//Обработваме само неформатиращите тагове + форматиращите тагове, които не съдържат само whitespace-ове

			if( $pFormatXml ){//Гледаме дали трябва да форматираме xml-a
				FormatWithPrefixString($pContent, $lPrevSiblingData[0], $lPrevSiblingData[1]);
			}
			$pContent .= $lTagStart;
			$pContent .= $lTagContent;
			if( $lHasElementChildren && $pFormatXml ){
				$lLastTextChildData = CheckPreviousSibling($lLastTextChild, $pDepth);
				FormatWithPrefixString($pContent, $lLastTextChildData[0], $lLastTextChildData[1]);
			}
			$pContent .= $lTagEnd;
		}elseif($lHasCommentChildren){
			//If the node does not have element/text children but has comment children
			//Add the comments but not the formatting tag
			$pContent .= $lTagContent;
		}

	}elseif( $pCurrentElement->nodeType == 3){//Text
		$pContent .= EscapeTextContent($pCurrentElement->textContent);
	}
}

function CheckIfNodeIsNotToBeFormatted(&$pCurrentNode){
	/** Връща true, ако децата на текущия node не трябва да се форматират. Връща false в противен случай
	*/
	if( $pCurrentNode->nodeType != 1 )
		return false;
	$lNodesNotToBeFormatted = array('p', 'title', 'label', 'tp:nomenclature-citation', 'ref', 'caption', 'th', 'td', 'kwd');
	if( !in_array( mb_strtolower($pCurrentNode->nodeName), $lNodesNotToBeFormatted ) )
		return false;
	return true;
}

function CheckForEmptyFormattingTag(&$pCurrentNode, $pHasElementChildren){
	/**
		Връща true, ако възела е форматиращ(болд, италик ...) и съдържанието му е само от whitespace-ове и няма element деца.
		В противен случай връща false;
	*/

	if( $pCurrentNode->nodeType != 1 )
		return false;
	if( $pHasElementChildren )
		return false;
	$lFormattingTagsArr = array('underline', 'italic', 'bold', 'p');
	if( !in_array( mb_strtolower($pCurrentNode->nodeName), $lFormattingTagsArr ) )
		return false;
	$lNonWhiteSpacePattern = '/\S/u';

	//~ if( preg_match($lNonWhiteSpacePattern, $pCurrentNode->textContent, $lMatch) ){//съдържанието не е само whitespace-ове
	//~ if( trim($pCurrentNode->textContent) ){
		//~ var_dump(trim($pCurrentNode->textContent));
	//~ }
	if( preg_match($lNonWhiteSpacePattern, $pCurrentNode->textContent, $lMatch) ){//съдържанието не е само whitespace-ове
		return false;
	}
	return true;
}

function EscapeTextContent($pText){
	return htmlspecialchars($pText);
}

function CalculateNodeFormattingSymbols($pDepth){
	/**
		Връща броя на форматиращите символи, които съответстват на съответната дълбочина
	*/
	return XML_FORMATTING_SYMBOL_PER_LEVEL * $pDepth;
}

function CheckPreviousSibling($pPreviousSibling, $pDepth){
	/**	Opredelq s kolko simvola trqbva da se podravni faila.
		Ako predhojdashtiq go element e tekstov node koito zavyrshva na \n i formatirashti simvoli - proqt na tezi koito trqbva da se dobavqt
		namalqva. Ako formatirashtite simvoli sa prekaleno mnogo - trqbva da se reje ot teksta.
	*/
	$lPutNewline = true;
	$lPutFormattingSymbols = CalculateNodeFormattingSymbols($pDepth);
	if( !$pPreviousSibling || $pPreviousSibling->nodeType != 3 ){
		return array($lPutNewline, $lPutFormattingSymbols);
	}
	$lText = $pPreviousSibling->textContent;
	$lLastNewLinePos = mb_strrpos($lText, "\n");
	if( $lLastNewLinePos === false ){
		return array($lPutNewline, $lPutFormattingSymbols);
	}
	$lFollowingText = mb_substr($lText, $lLastNewLinePos + 1);
	$lFollowingLength = mb_strlen($lFollowingText);
	for( $i = 0; $i < $lFollowingLength; ++$i ){
		if( $lFollowingText[$i] !== XML_FORMATTING_SYMBOL ){
			//~ echo 111;
			//~ var_dump($lFollowingText[$i]);
			//~ var_dump(XML_FORMATTING_SYMBOL);
			//~ var_dump($lFollowingText[$i] === ' ');
			//~ exit;
			return array($lPutNewline, $lPutFormattingSymbols);
		}
	}

	return array(false, $lPutFormattingSymbols - $lFollowingLength);
}

function FormatWithPrefixString( &$pContent, $pPutNewLine, $pFormattingSymbolsCount ){
	/**	Formatira stringa. $pPutNewLine opredelq dali she se slaga nov red, a $pFormattingSymbolsCount - broq na simvolite,
		koito shte se dobavqt/iztriqt.
	*/
	if( $pPutNewLine ){
		$pContent .= "\n";
	}
	//~ var_dump($lPutFormattingSymbols);
	if( $pFormattingSymbolsCount < 0 ){//Ako trqbva da rejem simvoli ot teksta
		$pContent = mb_substr($pContent, 0, $pFormattingSymbolsCount);
	}else{//Ako trqbva da dobavqme simvoli
		for( $i = 0; $i < $pFormattingSymbolsCount; ++$i)
			$pContent .= XML_FORMATTING_SYMBOL;
	}
}

function GetElementOpenTag(&$pCurrentElement, &$pXPath, &$pDeclaredNamespaces){
	/**
		Връща текста на отварящият таг за текущия node. В него се съдържат и атрубутите и декларираните namespace-ове.
	*/
	if( $pCurrentElement->nodeType == 8 ){//Comment
		return '<!--';
	}
	$lResult = '<';
	//~ if( $pCurrentElement->prefix ){
		//~ $lResult .= $pCurrentElement->prefix . ':';
	//~ }
	$lResult .= $pCurrentElement->nodeName;
	$lAttributes = $pCurrentElement->attributes;
	for( $i = 0; $i < $lAttributes->length; ++$i){

		$lResult .= ' ';
		$lSingleAttribute = $lAttributes->item($i);

		$lResult .= $lSingleAttribute->nodeName . '="' . EscapeTextContent($lSingleAttribute->nodeValue) . '"';
	}
	//Dobavqme i namespace-ovete
	$lNameSpaceXpath = './namespace::*';
	$lXpathResult = $pXPath->query($lNameSpaceXpath, $pCurrentElement);
	for( $i = 0; $i < $lXpathResult->length; ++$i){
		$lNameSpace = $lXpathResult->item($i);
		if( !array_key_exists($lNameSpace->nodeName, $pDeclaredNamespaces) || $pDeclaredNamespaces[$lNameSpace->nodeName] != $lNameSpace->nodeValue){//Ako imame nov namespace
			$lResult .= ' ';
			$lResult .= $lNameSpace->nodeName . '="' . $lNameSpace->nodeValue . '"';
			$pDeclaredNamespaces[$lNameSpace->nodeName] = $lNameSpace->nodeValue;
		}
	}
	$lResult .= '>';
	return $lResult;
}

function GetElementCloseTag(&$pCurrentElement){
	if( $pCurrentElement->nodeType == 8 ){//Comment
		return '-->';
	}
	return '</' . $pCurrentElement->nodeName . '>';
}

function SerializeAjaxOutput($pObject){
	$pObject->GetDataC();
	$lResult = $pObject->Display();
	$lResultArray = array(
		'result' => $lResult,
		'rescnt' => (int) $pObject->GetResultCount(),
		'req_objects' => $pObject->m_AjaxRequiredObjectIds,
		'got_data_from_cache' => (int)$pObject->m_got_data_from_cache,
	);
	echo json_encode($lResultArray);
}

/** XML FORMAT END */

function SaveArticleXml($pArticleId, $pXml){
	global $user;
	if( !$pArticleId )
		return;
	$lCon = Con();

	$lXml = GetFormattedXml($pXml);
	$lSql = 'SELECT * FROM spArticle(1, ' . (int) $pArticleId. ', \'' . q($lXml) . '\', ' . (int) $user->id . ')';
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	if( !$lCon->Eof()){
		SyncXmlData($lArticleId, $lXml);
		return (int)$lCon->mRs['id'];
	}
}

function GetArticleXml($pArticleId){
	$lCon = Con();
	$lSql = 'SELECT * FROM spArticle(0, ' . (int) $pArticleId. ', null, null)';
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	if( !$lCon->Eof()){
		return $lCon->mRs['xml'];
	}
}

function displayMenuLink($pResultsExist){
	if($pResultsExist)
		return;
	return ' class="inactiveMenuLink" title="' . getstr('admin.articles.menuLinkDoesNotExist') . '" ';
}

function getArticlePicId($pArticleId, $pImgName){
	$lCon = Con();
	$lSql = 'SELECT p.guid FROM
		photos p
		JOIN storyproperties sp ON sp.valint = p.guid AND sp.propid = ' . (int) ARTICLE_PHOTOS_PROPID . '
		JOIN articles a ON a.id = sp.guid
		WHERE a.id = ' . (int) $pArticleId . ' AND p.filenameupl = \'' . q($pImgName) . '\' LIMIT 1';
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	return (int)$lCon->mRs['guid'];
}

function AddPhotoToArticle($pArticleId, $pPicId){
	if((int) $pArticleId && (int) $pPicId){
		$lCon = Con();
		$lSql = 'SELECT * FROM spAddPhotoToArticle(' . (int) $pArticleId . ', ' . (int) $pPicId . ', ' . (int) ARTICLE_PHOTOS_PROPID .')';
		$lCon->Execute($lSql);
	}
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
	$lCn->Execute('SELECT spDeletePicFromArticles('. (int) $pPicId . ', ' . (int) ARTICLE_PHOTOS_PROPID . ')');
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

function DeleteArticlePics($pArticleId){
	if((int) $pArticleId ){
		$lCon = Con();
		$lSql = 'SELECT p.guid
		FROM photos p
		JOIN storyproperties sp ON sp.valint = p.guid AND sp.propid = ' . (int) ARTICLE_PHOTOS_PROPID . '
		JOIN articles s ON s.id = sp.guid
		WHERE s.id = ' . $pArticleId;
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		$lPictures = array();
		while( !$lCon->Eof()){
			$lPictures[] = (int) $lCon->mRs['guid'];
			$lCon->MoveNext();
		}
		foreach( $lPictures as $lPicId ){
			if((int) $lPicId )
				DeletePic( $lPicId );
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

//Izkarva taxonite sys specialnite poleta ot xml-a za eol i dava link za redaktiraneto im
function GetEolExportTaxons($pExportId, $pXml){
	$lTempls = array(
		G_ROWTEMPL => 'eol_export.taxonRow',
		G_HEADER => 'eol_export.taxonHead',
		G_STARTRS => 'eol_export.taxonStart',
		G_ENDRS => 'eol_export.taxonEnd',
		G_FOOTER => 'eol_export.taxonFoot',
		G_NODATA => 'eol_export.taxonNoData',
		G_ERR_ROW => 'eol_export.taxonErrRow',
	);

	$lObject = new crs_xml(array(
		'xml' => $pXml,
		'templs' => $lTempls,
		'row_select_xpath_query' => '//def:taxon',
		'register_xpath_namespaces' => array(
			'xsd' => 'http://www.w3.org/2001/XMLSchema',
			'dc' => 'http://purl.org/dc/elements/1.1/',
			'dwc' => 'http://rs.tdwg.org/dwc/dwcore/',
			'def' => 'http://www.eol.org/transfer/content/0.3',
		),
		'export_id' => $pExportId,
		'row_fields_query' => array(
			'identifier' => array(
				'xpath' => './dc:identifier',
			),
			'kingdom' => array(
				'xpath' => './dwc:Kingdom',
			),
			'family' => array(
				'xpath' => './dwc:Family',
			),
			'scientificName' => array(
				'xpath' => './dwc:ScientificName'
			),
			'fig_count' => array(
				'xpath' => 'count(./def:dataObject[def:dataType=\'http://purl.org/dc/dcmitype/StillImage\'])',
				'evaluate' => true,
			),
			'desc_count' => array(
				'xpath' => 'count(./def:dataObject[dc:title=\'Description\'])',
				'evaluate' => true,
			),
			'dist_count' => array(
				'xpath' => 'count(./def:dataObject[dc:title=\'Distribution\'])',
				'evaluate' => true,
			),
		),
	));
	return $lObject->Display();
}

/**
	Синхронизира полетата от експорта с kfor-а.
	Ако pWriteFields е false тогава стойносттите на полетата се слагат в kfor-а иначе се записват от kfor-а във xml-a.
	pDataArray - Ако вместо един таксон(kfor) искаме да ъпдейтнем/вземем стойността на няколко таксона подаваме масив със следния формат
		doi_identifier => масив от полетата на съответния таксон със следния формат
			име–на–поле => стойност


*/
function SyncEolExportFields($pKfor, $pWriteFields = false, &$pDataArray = false){
	$lCon = Con();
	$lCon->Execute('SELECT xml FROM eol_export WHERE id = ' . (int) $pKfor->lFieldArr['export_id']['CurValue']);
	$lCon->MoveFirst();
	if( $lCon->Eof() ){//Ako ne syshtestvuva exporta
		$pKfor->SetError('export_id', getstr('admin.eol_export.noSuchExport') );
	}else{//Ima takava statiq
		$lXml = $lCon->mRs['xml'];
		$pKfor->lFieldArr['xml']['CurValue'] = $lXml;
		$lXmlDocument = new DOMDocument("1.0");
		$lXmlDocument->resolveExternals = true;
		if( !$lXmlDocument->loadXML($lXml)){//Ako xml-a e greshen
			$pKfor->SetError('export_id', getstr('admin.eol_export.wrongExportXml') );
		}else{//XML-a e OK
			$lXPath = new DOMXPath($lXmlDocument);
			$lNamespaces = array(
				'xsd' => 'http://www.w3.org/2001/XMLSchema',
				'dc' => 'http://purl.org/dc/elements/1.1/',
				'dwc' => 'http://rs.tdwg.org/dwc/dwcore/',
				'def' => 'http://www.eol.org/transfer/content/0.3',
			);//Namespace-ove za xpatha
			foreach($lNamespaces as $lNamespaceName => $lNamespaceUrl){
				$lXPath->registerNamespace($lNamespaceName, $lNamespaceUrl);
			}



			/**
				Масив с полетата. Форматът е:
					име на полето => масив с информация за полето
						Форматът на масива е
							xpath -> xpath query за намиране на възела в него
							write_to_xml => true/false определя дали да го пишем в xml-a
							namespace_uri => адреса на ns-a на възела. Ползва се при създаване на нов възел
							qualified_name => qualified name на възела. Ползва се при създаване на нов възел
							following_elements_xpath => масив от xpath query-та на възли, които са след него
								Поонеже ако трябва да създаваме възела във xml-а трябва да знаем къде точно да го създадем
			*/
			$lFieldsExpressions = array(
				'kingdom' => array(
					'xpath' => './dwc:Kingdom',
					'write_to_xml' => true,
					'namespace_uri' => $lNamespaces['dwc'],
					'qualified_name' => 'Kingdom',
					'following_elements_xpath' => array(
						'./dwc:Phylum',
						'./dwc:Class',
						'./dwc:Order',
						'./dwc:Family',
						'./dwc:Genus',
						'./dwc:ScientificName',
						'./def:rank',
						'./def:commonName',
						'./def:synonym',
						'./def:agent',
						'./dcterms:created',
						'./dcterms:modified',
						'./reference',
						'./def:additionalInformation',
						'./def:dataObject',

					),
				),
				'family' => array(
					'xpath' => './dwc:Family',
					'namespace_uri' => $lNamespaces['dwc'],
					'qualified_name' => 'Family',
					'write_to_xml' => true,
					'following_elements_xpath' => array(
						'./dwc:Genus',
						'./dwc:ScientificName',
						'./def:rank',
						'./def:commonName',
						'./def:synonym',
						'./def:agent',
						'./dcterms:created',
						'./dcterms:modified',
						'./reference',
						'./def:additionalInformation',
						'./def:dataObject',

					),
				),
				'scientific_name' => array(
					'xpath' => './dwc:ScientificName',
					'write_to_xml' => false,
					'namespace_uri' => $lNamespaces['dwc'],
					'qualified_name' => 'ScientificName',
					'following_elements_xpath' => array(
						'./def:rank',
						'./def:commonName',
						'./def:synonym',
						'./def:agent',
						'./dcterms:created',
						'./dcterms:modified',
						'./reference',
						'./def:additionalInformation',
						'./def:dataObject',

					),
				),
			);
			/**
				За да уеднаквим нещата правим така че ако работим само с kfor-a на практика пак да работим с масив
				lUseKfor - за да може после да ъпдейтнем полетата на kfor-a ако правим четене
			*/
			$lUseKfor = false;
			if( !is_array($pDataArray) || !count($pDataArray)){//Rabotim s kfora
				$pDataArray = array();
				$lUseKfor = true;
				$lDoi = $pKfor->lFieldArr['doi']['CurValue'];
				$pDataArray[$lDoi] = array();
				foreach($lFieldsExpressions as $lFieldName => $lFieldArr ){
					$pDataArray[$lDoi][$lFieldName] = $pKfor->lFieldArr[$lFieldName]['CurValue'];
				}
			}

			//Обикаляме всички таксони
			foreach($pDataArray as $lDoiNum => &$lTaxonFields){



				$lTaxonXpath = '//def:taxon[dc:identifier=\'' . $lDoiNum . '\']';
				$lTaxonResult = $lXPath->query($lTaxonXpath);


				if( !$lTaxonResult->length ){//Ako nqma takyv takson
					$pKfor->SetError('export_id', getstr('admin.eol_export.noTaxonWithDoi') . ' ' . $lDoiNum);
					break;
				}else{//Syshtestvuva si taksona
					$lTaxonNode = $lTaxonResult->item(0);
					//Populvame poletata

					foreach($lFieldsExpressions as $lFieldName => $lFieldInfoArr){
						if( $pWriteFields && !$lFieldInfoArr['write_to_xml'] )//Ako vyzela ne trqbva da se update-wa - prodyljavame
							continue;

						$lFieldXPath = $lFieldInfoArr['xpath'];
						$lFieldResult = $lXPath->query($lFieldXPath, $lTaxonNode);
						if( $lFieldResult->length ){//Ako node-a systestvuva
							if( !$pWriteFields ){//Vzimame stoinostta
								$lTaxonFields[$lFieldName] = $lFieldResult->item(0)->textContent;
							}else{//Pishem po xml-a
								$lFieldResult->item(0)->nodeValue = $lTaxonFields[$lFieldName];
							}
						}elseif($pWriteFields){//Ako node-a ne syshtestvuva i pishem po xml-a trqbva da go syzdadem
							/**
								Обикаляме xpath-овете на следващите го елементи и ако намерим такъв елемент
								слагаме новия елемент преди намерения. Ако не сме намерили нищо го слагаме като последен
							*/
							$lFollowingElementsXpaths = $lFieldInfoArr['following_elements_xpath'];
							$lFollowingNode = false;
							foreach($lFollowingElementsXpaths as $lXpath){
								$lFollowingNodeResult = $lXPath->query($lXpath, $lTaxonNode);
								if( $lFollowingNodeResult->length ){
									$lFollowingNode = $lFollowingNodeResult->item(0);
									break;
								}
							}

							$lNodeNsUri = $lFieldInfoArr['namespace_uri'];
							$lQualifiedName = $lFieldInfoArr['qualified_name'];
							$lNewNode = $lXmlDocument->CreateElementNS($lNodeNsUri, $lQualifiedName, $lTaxonFields[$lFieldName]);

							if( $lFollowingNodeResult === false ){//Ako ne e nameren sledvasht vyzel - appendvame
								$lTaxonNode->appendChild($lNewNode);
							}else{//Nameren e sledvasht vyzel - insertvame predi nego
								$lTaxonNode->insertBefore($lNewNode, $lFollowingNode);
							}
						}
					}

				}
				if($lUseKfor && !$pWriteFields){//Ako trqbva da popylnim kfor-a
					foreach($lTaxonFields as $lFieldName => $lValue ){
						$pKfor->lFieldArr[$lFieldName]['CurValue'] = $lValue;
					}
				}
			}

			if( $pWriteFields && !$pKfor->lErrorCount ){//Zapazvame xml-a v bazata
				$lCon->Execute('UPDATE eol_export SET xml = \'' . q( $lXmlDocument->SaveXML() ) . '\' WHERE id = ' . (int) $pKfor->lFieldArr['export_id']['CurValue']);
				//~ echo '<pre>' . $lXmlDocument->SaveXML() . '</pre>';
			}


		}

	}

}

/*
 * Връща url името на journal-а на статията с това ID (ид-то е ид в базата на пенсофт)
 */
function getArticleJournalUrlTitle($pArticleId){
	$lCon = GetCmsDBConnection();
	$lSql = 'SELECT j.URL_TITLE as url_title
		FROM J_ARTICLE a
		JOIN J_JOURNALS j ON j.journal_id = a.journal_id
		WHERE a.article_id = ' . (int)$pArticleId;
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	return trim($lCon->mRs['url_title']);
}

/*
 * Връща url към абстракта на статията
 */
function getArticleAbstractLink($pArticleId){
	$lJournalUrlName = getArticleJournalUrlTitle($pArticleId);
	return 'http://www.pensoft.net/journals/' . $lJournalUrlName . '/article/' . $pArticleId . '/abstract/';
}

/*
	Генерира xml-a за дадения export.
	Взима xml-a на статията от базата,
	пуска xsl-a и запазва новия xml(взависимост от подадените параметри) и го връща като стринг .
	При запазването в базата се гледа дали в xml-a има резултати - изпълнява се xpath expression, който е специфичен за всеки тип импорти

	Параметър pParseXmlTitles се ползва само за уики експорт
	Ако е подаден параметър pParseXmlTitles по време на xsl-a се гледа на сървъра на уики-то дали има дадена страница със име
	като някоя от страниците в резултатния xml и ако това е така - в заглавията в xml-a се добавя автора
	Ако е подаден параметър pStrict и е подаден параметър pSaveResultToDb - ако е започнало генериране на този експорт от процес с друг pid - гърмим
*/
function GenerateExportXml($pExportId, $pSaveResultToDb = true, $pParseXmlTitles = false, $pStrict = false){
	global $gXslProcessedArticleId;
	$lCon = Con();
	$lSelectSql = 'SELECT * FROM spGenerateExport(' . (int)$pExportId . ', ' . (int)$pSaveResultToDb . ', ' . (int)$pStrict . ', ' . (int)getmypid() . ')';

	if(!$lCon->Execute($lSelectSql)){
		throw new Exception(getstr($lCon->GetLastError()));
	}
	$lCon->MoveFirst();

	//~ var_dump($lCon->mRs);
	$lArticleId = (int)$lCon->mRs['article_id'];



	$lXslFile = trim($lCon->mRs['xsl_file']);
	$lResultsXPath = trim($lCon->mRs['results_xpath_expr']);
	$lFormatXml = (int)$lCon->mRs['format_xml'];

	if(!$lArticleId || $lXslFile == '')
		return;

	$lArticleXml = getArticleXml($lArticleId);


	if( !$pParseXmlTitles ){
		global $gWikiExportIgnorePageTitles;
		$gWikiExportIgnorePageTitles = 1;
	}
	$gXslProcessedArticleId = $lArticleId;
	$lTransformedXml = transformXmlWithXsl($lArticleXml, PATH_CLASSES . 'xsl/' . $lXslFile);
	
	$gXslProcessedArticleId = false;

	//За някои от експортите форматираме xml-a
	if($lFormatXml){
		$lTransformedXml = GetFormattedXml($lTransformedXml, false);
	}
	//~ var_dump($lTransformedXml);
	//~ exit;
	if( $pSaveResultToDb){
		//Гледаме дали има резултати
		$lResultsFound = 0;
		if($lResultsXPath){
			$lTempXmlDocument = new DOMDocument("1.0");
			$lTempXmlDocument->resolveExternals = true;
			if( $lTempXmlDocument->loadXML($lTransformedXml)){
				$lXPath = new DOMXPath($lTempXmlDocument);
				$lNamespaces = array(
					'xsd' => 'http://www.w3.org/2001/XMLSchema',
					'dc' => 'http://purl.org/dc/elements/1.1/',
					'dwc' => 'http://rs.tdwg.org/dwc/dwcore/',
					'eol' => 'http://www.eol.org/transfer/content/0.3',
					'mw' => 'http://www.mediawiki.org/xml/export-0.4/',
				);//Namespace-ove za xpatha
				foreach($lNamespaces as $lNamespaceName => $lNamespaceUrl){
					$lXPath->registerNamespace($lNamespaceName, $lNamespaceUrl);
				}
				//~ $lResultsXPath = '/eol:response/eol:taxon';
				//~ var_dump($lResultsXPath);
				//~ var_dump($lTransformedXml);
				$lResultNodes = $lXPath->query($lResultsXPath);
				//~ var_dump($lResultNodes->length);
				if($lResultNodes->length){
					$lResultsFound = 1;
				}
			}
		}
		
		//~ exit;
		$lCon->CloseRs();
		$lCon->Execute('UPDATE export_common SET
			xml = \'' . q ($lTransformedXml) . '\',
			is_generated = 1,
			has_results = ' . (int) $lResultsFound . '
			WHERE id = ' . (int) $pExportId
		);
	}
	return $lTransformedXml;
}

/**
	Тази функция ще ъплоудва експорта
*/
function UploadExportXml($pExportId){
	$lCon = Con();
	$lSelectSql = 'SELECT * FROM spUploadExport(' . (int)$pExportId . ', ' . (int)getmypid() . ')';
	if(!$lCon->Execute($lSelectSql)){
		throw new Exception(getstr($lCon->GetLastError()));
	}
	$lCon->MoveFirst();
	$lExportType = (int)$lCon->mRs['export_type'];
	//Ако ъплоуда гръмне - това е понеже е имало валидационна грешка, т.е. ъплоуда не е почнал
	//тогава трябва да маркираме ъплоуда като незапочнат за да може по-късно да се
	//ъплоудне като хората
	try{
		switch($lExportType){
			case (int) EOL_EXPORT_TYPE:{
				AppendEolExportToPensoftXmlFile($pExportId);
				break;
			}
			case (int) WIKI_EXPORT_TYPE:{
				UploadWikiExport($pExportId);
				break;
			}
			case (int) KEYS_EXPORT_TYPE:{
				UploadKeysExport($pExportId);
				break;
			}
		}
	}catch(Exception $lException){
		$lRestoreSql = 'UPDATE export_common
			SET upload_pid = 0, upload_started = 0
			WHERE id = ' . $pExportId . ' AND upload_started = 1 AND is_uploaded = 0';
		$lCon->Execute($lRestoreSql);
		//Наново хвърляме ексепшъна - за да покажем грешката в кфор-а
		throw($lException);
	}
}

/*
	Генерира наново xml-a като в него парсва заглавията на страниците.
	Взима от xml-a на статията всички картинки и ги ъплоудва на wiki сървъра.
	След това ъплоудва генерирания xml на wiki сървъра
*/
function UploadWikiExport($pExportId){
	$lWikiUploader = new cwiki_uploader($pExportId);
	$lWikiUploader->getData();
	$lCon = Con();
	if( $lWikiUploader->getErrCnt() ){
		$lSql = 'SELECT * FROM spWikiExportReport(' . (int) $pExportId . ', 1, \'' . q($lWikiUploader->getErrMsg()) . '\')';
	}else{
		$lSql = 'SELECT * FROM spWikiExportReport(' . (int) $pExportId . ', 0, \'' . q($lWikiUploader->getReportMsg()) . '\')';
	}
	$lCon->Execute($lSql);
}

/*
	Генерира наново xml-a като в него парсва заглавията на страниците.
	Взима от xml-a на статията всички картинки и ги ъплоудва на wiki сървъра.
	След това ъплоудва генерирания xml на wiki сървъра
*/
function UploadKeysExport($pExportId){
	$lKeysUploader = new ckeys_uploader($pExportId);
	$lKeysUploader->getData();
	//Ако е станала грешка при проверката на xml-a трябва да съобщим за нея, но да не маркираме експорта като ъплоуднат
	if(!$lKeysUploader->getCheckResult()){
		//Само хвърляме ексепшъна, а кфор-а автоматично си го прихваща
		throw new Exception($lKeysUploader->getCheckError());
	}
	//~ var_dump('Errors:', $lKeysUploader->getErrCnt());
	//~ var_dump('ErrMsg:', $lKeysUploader->getErrMsg());
	//~ var_dump('ReportMsg:', $lKeysUploader->getReportMsg());
	//~ return;

	$lCon = Con();
	if( $lKeysUploader->getErrCnt() ){
		$lSql = 'SELECT * FROM spExportReport(' . (int) $pExportId . ', 1, \'' . q($lKeysUploader->getErrMsg()) . '\')';
	}else{
		$lSql = 'SELECT * FROM spExportReport(' . (int) $pExportId . ', 0, \'' . q($lKeysUploader->getReportMsg()) . '\')';
	}
	$lCon->Execute($lSql);
}

/**
	ot pensoft.net, po ID na statiya ot tablica J_ARTICLE, pole 'taxon' se vzemat Id-tata na taxonite. --> posle po tezi Id-ta ot tablica TRUBRIK, pole 'RID_NAME' se vzemat imenata im
*/
function getKeysExportTaxonomicScope($pArticleId){
	if(!(int)$pArticleId)
		return;
	$lCon = GetCmsDBConnection();
	$lSql = 'SELECT GROUP_CONCAT(t.RID_NAME SEPARATOR \', \') as result
		FROM J_ARTICLE a
		JOIN TRUBRIK t ON LOCATE(CONCAT(\'|\', t.RID, \'|\'), a.taxon) > 0
		WHERE a.article_id = ' . (int) $pArticleId . '
		GROUP BY a.article_id
	';
	$lCon->Execute($lSql);
	return $lCon->mRs['result'];
}

/**
	ot pensoft.net, po ID na statiya ot tablica J_ARTICLE, pole 'geo_spatial' se vzemat Id-tata na geograpskiya region. --> posle po tezi Id-ta ot tablica SRUBRIK, pole 'RID_NAME' se vzemat imenata im
*/
function getKeysExportGeographicScope($pArticleId){
	if(!(int)$pArticleId)
		return;
	$lCon = GetCmsDBConnection();
	$lSql = 'SELECT GROUP_CONCAT(t.RID_NAME SEPARATOR \', \') as result
		FROM J_ARTICLE a
		JOIN SRUBRIK t ON LOCATE(CONCAT(\'|\', t.RID, \'|\'), a.geo_spatial) > 0
		WHERE a.article_id = ' . (int) $pArticleId . '
		GROUP BY a.article_id
	';

	$lCon->Execute($lSql);
	return $lCon->mRs['result'];
}




function GetCmsDBConnection(){
	$lCon = new DbCn(DBTYPE_CMS);
	$lCon->Open(PGDB_CMS_SRV, PGDB_CMS_DB, PGDB_CMS_USR, PGDB_CMS_PASS, PGDB_CMS_PORT);
	return $lCon;
}

/*
	Сваля картинката в указания файл
*/
function downloadImage($pFileName, $pImageUrl){
	$lPicContent = file_get_contents($pImageUrl);
	return file_put_contents($pFileName, $lPicContent);
}


/**
	Прилага xsl трансформация в/у xml-a
		pXSL - име на файла за xsl-a
		pXML - стринг, който съдържа xml-a
*/
function transformXmlWithXsl($pXML, $pXSL){	
	$lXML = new DOMDocument("1.0");
	$lXSL = new DOMDocument("1.0");


	if (!$lXSL->load($pXSL)) {
		throw new Exception(getstr('admin.articles.xslNotValid'));
		return;
	}



	$lXML->resolveExternals = true;
	if (!$lXML->loadXML($pXML)) {
		throw new Exception(getstr('admin.articles.xmlNotValid'));
		return;
	}
	// Configure the transformer
	$lXslProcessor = new XSLTProcessor;
	$lXslProcessor->registerPHPFunctions();
	$lXslProcessor->importStyleSheet($lXSL);

	$lXSLResult =  $lXslProcessor->transformToXML($lXML);

	return $lXSLResult;
}

/**
	Тази функция се ползва в xsl-a, понеже от doi номера трябва да се махнат първите 8 символа напр.:
		10.3897/zookeys.67.700 => zookeys.67.700
*/
function parseArticleDoi($pDoi){
	return mb_substr($pDoi, 8);
}

/**
 Тази функция се ползва в xsl-a на еол, за да може да вземем по 1 път цитираните фигури
 */
function GetTaxonDistinctFigsList($pFigs){	
	$lProcessedFigs = array();
	$lDom = new DOMDocument('1.0', 'utf-8');
	$lRoot = $lDom->appendChild($lDom->createElement('root'));
// 	var_dump($lFieldsArr);
	foreach($pFigs as $lCurrentFig){
		$lFigId = $lCurrentFig->GetAttribute('rid');
		if(in_array($lFigId, $lProcessedFigs)){
			continue;
		}
		$lProcessedFigs[] = $lFigId;
		$lChild= $lRoot->appendChild($lDom->importNode($lCurrentFig, 1));
	}
// 	var_dump($lDom->saveXML());
	return $lDom;
}

/**
	Тази функция се ползва в xsl-a, понеже трябва да се взима само първата буква от дадено име
		10.3897/zookeys.67.700 => zookeys.67.700
*/
function getNameFirstLetter($pName){
	return mb_substr($pName, 0, 1);
}

/**
	Тази функция се ползва в xsl-a, понеже трябва да се конструира пътя до картинката
		$pPicBaseUrl - шаблон за името на картинката като в него параметрите са във формат {име–на–параметър} и трябва да се replace-нат
*/
function getFigPicUrl($pPicBaseUrl, $pJournalId, $pArticleId, $pFileName){
	$pFileName = basename($pFileName);
	$lPatterns = array(
		'{journal_id}' => (int) $pJournalId,
		'{article_id}' => (int) $pArticleId,
		'{file_name}' => $pFileName
	);
	$lPicUrl = $pPicBaseUrl;
	foreach($lPatterns as $lSearch => $lReplacement){
		$lPicUrl = str_replace($lSearch, $lReplacement, $lPicUrl);
	}
	return $lPicUrl;
}

/**
	Връща пенсофт id-то на списанието, към което е статията с подаденото пенсофт ид
*/
function getArticleJournalId($pArticleId){
	$lCon = GetCmsDBConnection();
	$lSql = 'SELECT j.journal_id as id
		FROM J_ARTICLE a
		JOIN J_JOURNALS j ON j.journal_id = a.journal_id
		WHERE a.article_id = ' . (int)$pArticleId;
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	return (int)$lCon->mRs['id'];
}

/**
	Добавя към xml-a на пенсофт xml-a от текущия експорт
*/
function AppendEolExportToPensoftXmlFile($pExportId){
	$lFileNameSelectSql = '
		SELECT j.xml_file_name, e.xml, coalesce(e.is_uploaded, 0) as is_uploaded
		FROM eol_export e
		JOIN articles a ON a.id = e.article_id
		JOIN journals j ON j.id = a.journal_id
		WHERE e.id = ' . (int)$pExportId
	;

	$lCon = Con();
	$lCon->Execute($lFileNameSelectSql);
	$lCon->MoveFirst();

	$lFileName = $lCon->mRs['xml_file_name'];
	$lXml = $lCon->mRs['xml'];
	$lIsUploaded = (int)$lCon->mRs['is_uploaded'];

	if( (int)$lIsUploaded ){
		throw new Exception(getstr('admin.eol_export.exportAlreadyAppended'));
	}
	if( !$lFileName ){
		throw new Exception(getstr('admin.eol_export.noFileNameForSelectedJournal'));
	}
	if( !trim($lXml )){
		throw new Exception(getstr('admin.eol_export.emptyXml'));
	}
	//Pravim temp fail v koito shte stoi xml-a
	$lTempFile = tempnam(sys_get_temp_dir(), 'eol_export');
	file_put_contents($lTempFile, $lXml);

	$lExternalQueryUrl = str_replace('{sourcefile}', rawurlencode($lTempFile), PENSOFT_EOL_EXPORT_APPEND_URL);
	$lExternalQueryUrl = str_replace('{targetfile}', rawurlencode($lFileName), $lExternalQueryUrl);

	$lResult = executeExternalQuery($lExternalQueryUrl);

	unlink($lTempFile);//Mahame temp file-a

	if( $lResult === PENSOFT_EOL_EXPORT_APPEND_SUCCESSFUL_REPLY ){
		$lSql = 'SELECT * FROM spExportReport(' . (int) $pExportId . ', 1, \'' . q($lResult) . '\')';
	}else{
		$lSql = 'SELECT * FROM spExportReport(' . (int) $pExportId . ', 0, \'' . q($lResult) . '\')';
	}
	$lCon->Execute($lSql);
}

/**
	Vryshta link-a kym xml-a ili kym pdf-a na dadena statiq ot saita na pensoft. Id-to na statiqta e v identifier na taksona
*/
function getPensoftArticleLinkByIdentifier($pIdentifier, $pReturnXmlLink = false){
	if(!preg_match('/(.*)\.(\d)+\.(?P<article_id>\d+)\.sp\_(\d)+/i', $pIdentifier, $lMatchData))//Ako identifier-a e bygav
		return;
	$lArticleId = $lMatchData['article_id'];
	/**
		Pravim go statichno za da se vikne 1 pyt za celiq import
		Tuk se dyrjat linkovete kym statiite. Formata na dannite e sledniq
			pJournalTitle => masiv ot broeve v sledniq format
				nomer na broi => masiv ot statii sys sledniq format
					id na statiq => masiv ot linkove vyv sledniq format
						tip na linka => URL na linka
	*/
	static $lPensoftArticlesFileIds;
	if( !is_array($lPensoftArticlesFileIds[$lArticleId])){
		$lPensoftArticlesFileIds[$lArticleId] = array();
		$lCon = GetCmsDBConnection();
		$lSql = 'SELECT file_id, LOWER(label) as label_type
			FROM J_GALLEYS
			WHERE article_id = ' . (int) $lArticleId . '
			AND (LOWER(label) = \'xml\' OR LOWER(label) = \'pdf\')
		';
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		while(!$lCon->Eof()){
			$lPensoftArticlesFileIds[$lArticleId][$lCon->mRs['label_type']] = (int)$lCon->mRs['file_id'];
			$lCon->MoveNext();
		}

	}
	$lFileType = 'pdf';
	if($pReturnXmlLink){
		$lFileType = 'xml';
	}
	$lFileId = $lPensoftArticlesFileIds[$lArticleId][$lFileType];
	if($lFileId){
		$lLink = str_replace('{file_id}', $lFileId, PENSOFT_ARTICLE_LINK_PATTERN);
		$lLink = '<a target="_blank" href="' . $lLink . '">click</a>';
	}
	return $lLink;
}

/**
	Vryshta link-a kym xml-a ili kym pdf-a na dadena statiq ot saita na pensoft. Id-to na statiqta e v identifier na taksona
*/
function getPensoftArticleLink($pJournalTitle, $pIssueId, $pIdentifier, $pReturnXmlLink = false){
	/**
		Pravim go statichno za da se vikne 1 pyt za celiq import
		Tuk se dyrjat linkovete kym statiite. Formata na dannite e sledniq
			pJournalTitle => masiv ot broeve v sledniq format
				nomer na broi => masiv ot statii sys sledniq format
					id na statiq => masiv ot linkove vyv sledniq format
						tip na linka => URL na linka
	*/
	static $lJournalArticlesInfo;
	if(!is_array($lJournalArticlesInfo))
		$lJournalArticlesInfo = array();
	if( !trim($pJournalTitle) || !(int)$pIssueId )
		return;
	if( !is_array($lJournalArticlesInfo[$pJournalTitle][$pIssueId]) || !count($lJournalArticlesInfo[$pJournalTitle][$pIssueId])){//Vzimame linkovete ot saita na pensoft
		$lJournalArticlesInfo[$pJournalTitle][$pIssueId] = array();
		$lIssueExportUrl = PENSOFT_ISSUE_EXPORT_URL . '?title=' . rawurlencode($pJournalTitle) . '&volume=' . (int)$pIssueId;

		$lResult = executeExternalQuery($lIssueExportUrl);

		if( $lResult === false ){
			return;
		}
		$lTempXmlDocument = new DOMDocument("1.0");
		$lTempXmlDocument->resolveExternals = true;

		if( !$lTempXmlDocument->loadXML($lResult)){//Ako xml-a e greshen
			return;
		}
		$lXPath = new DOMXPath($lTempXmlDocument);
		$lArticleXPathQuery = '/journal/articles/article';
		$lArticleXPathQueryResult = $lXPath->query($lArticleXPathQuery);
		for($i = 0; $i < $lArticleXPathQueryResult->length; ++$i){
			$lCurrentArticle = $lArticleXPathQueryResult->item($i);
			$lCurrentArticleId = $lCurrentArticle->getAttribute('id');
			$lJournalArticlesInfo[$pJournalTitle][$pIssueId][$lCurrentArticleId] = array();
			$lLinksXPath = 'pdf_link|xml_link';//Tipovete linkove, koito shte pazim
			$lLinksXPathResult = $lXPath->query($lLinksXPath, $lCurrentArticle);
			for($j = 0; $j < $lLinksXPathResult->length; ++$j){
				$lCurrentLink = $lLinksXPathResult->item($j);
				$lCurrentLinkType = $lCurrentLink->nodeName;
				$lJournalArticlesInfo[$pJournalTitle][$pIssueId][$lCurrentArticleId][$lCurrentLinkType] = $lCurrentLink->nodeValue;
			}
		}
	}
	if(!preg_match('/(.*)\.(\d)+\.(?P<article_id>\d+)\.sp\_(\d)+/i', $pIdentifier, $lMatchData))//Ako identifier-a e bygav
		return;
	$lArticleId = $lMatchData['article_id'];
	if( $pReturnXmlLink ){
		$lLink = $lJournalArticlesInfo[$pJournalTitle][$pIssueId][$lArticleId]['xml_link'];
	}else{
		$lLink = $lJournalArticlesInfo[$pJournalTitle][$pIssueId][$lArticleId]['pdf_link'];
	}
	if( $lLink ){
		$lLink = '<a target="_blank" href="' . $lLink . '">click</a>';
	}
	return $lLink;

}
/*
	Връща id-то на статията с подадения doi
	10.3897/zookeys.84.774
	id-то са последните цифри след точката
*/
function getArticleIdFromDoi($pDoi){
	if( preg_match('/\.\s*(\d+)\s*$/', $pDoi, $lMatch)){
		return $lMatch[1];
	}
	return '';
}

/*
	Връща id-то на journal-а с подаденото име
*/
function getJournalIdFromJournalTitle($pJournalTitle){
	$lCon = Con();
	$lCon->Execute('SELECT pensoft_id as id FROM journals WHERE pensoft_title = \'' . q(trim($pJournalTitle)) . '\'');
	$lCon->MoveFirst();
	return (int) $lCon->mRs['id'];
}

/*
	Ако последният символ е точка - махаме го
*/
function parseMediaWikiSecTitle($pTitle){
	$pTitle = trim($pTitle);
	if( mb_substr($pTitle, -1) == '.')
		return mb_substr($pTitle, 0, mb_strlen($pTitle) - 1);
	return $pTitle;
}

/*
	Ако имаме нов ред с интервали след него - махаме ги
*/
function parseMediaWikiSecContent($pContent){
	$pContent = trim($pContent);
	$pContent = preg_replace('/ +/', ' ', $pContent);
	$pContent = preg_replace("/\n +/", "\n", $pContent);
	return $pContent;
}

function createIntFromString($pString){
	return preg_replace('/[A-Za-z]/', '', $pString);
	return (int)$pString;
}

/*
	Понеже в експорта за wikimedia трябва да изкарваме само по 1 път референциите
	а в xsl-а няма как да стане това - правим го тук.
	За всеки таксон инициализираме статичния масив наново на празен масив и в него отбелязваме кои цитати сме изкарали
*/
function checkForDuplicateRefId($pRefId, $pClearArray){
	static $lReferences = array();
	if( (int)$pClearArray ){
		$lReferences = array();
		return true;
	}
	if( !in_array($pRefId, $lReferences) ){
		$lReferences[] = $pRefId;
		return 1;
	}
	return 0;
}

/*
	Понеже в експорта за wikimedia трябва да изкарваме само по 1 път фигурите
	а в xsl-а няма как да стане това - правим го тук.
	За всеки таксон инициализираме статичния масив наново на празен масив и в него отбелязваме кои фигури сме изкарали
	Ако фигурата не е изкарвана вадим поредния и номер понеже ни трябва в xsl-a
*/
function checkForDuplicateFigId($pFigId, $pOper){
	static $lFigs = array();
	if( (int)$pOper == 1 ){//Zanulqvame masiva
		$lFigs = array();
		return true;
	}
	if( (int) $pOper == 2 )//Vryshtame informaciq za razmera na masiva
		return count($lFigs);

	//Ako prosto trqbva da gledame za nova stoinost
	if( !in_array($pFigId, $lFigs) ){
		$lFigs[] = $pFigId;
		return count($lFigs);
	}
	return 0;
}

/*
	Понеже в upload-a на експорта за wikimedia трябва да изкарваме само по 1 път картинките
	а в xsl-а няма как да стане това - правим го тук.
*/
function checkForDuplicatePicUrl($lPicUrl){
	static $lPics = array();

	if( !in_array($lPicUrl, $lPics) ){
		$lPics[] = $lPicUrl;
		return 1;
	}
	return 0;
}

/*
	Понеже в експорта за wikimedia трябва да изкарваме
	page title като името на таксона само ако вече няма такава страница, а в останалите случаи да добавяме и авторите,
	а в xsl-а няма как да стане това - правим го тук.
	Ако досега не е имало такъв title - връщаме 1 и отбелязваме title-а. Иначе връщаме 0
*/
function checkForDuplicatePageTitle($pPageTitle){
	global $gWikiExportIgnorePageTitles;
	if( (int) $gWikiExportIgnorePageTitles)//Ако е указано да не се гледа - винаги връшаме, че няма такава статия
		return 1;
	static $lPageTitles = array();
	if( !in_array($pPageTitle, $lPageTitles) ){
		$lPageTitles[] = $pPageTitle;
		if( !getWikiPageId($pPageTitle) ){//Гледаме дали в уикито има статия със същото име
			return 1;
		}
		return 0;
	}
	return 0;
}

/*
	Връща ид-то на статията с подаденото име.
	Ако няма такава статия или стане грешка връща false
*/
function getWikiPageId($pPageTitle){
	try{
		$lWikiObject = new wikipedia(WIKI_ADDRESS);
		$lWikiObject->login(WIKI_USERNAME, WIKI_PWD);
		return $lWikiObject->getpageid($pPageTitle);
	}catch(Exception $pException){//Ако стане грешка при създаването на обекта
		return false;
	}
}

/*
	Премахваме множеството спейсове и правим нов ред последван от спейсове само на нов ред
*/
function prepareXslText($pText){
	$pText = preg_replace('/ +/', ' ', $pText);
	$pText = preg_replace("/\n /", "\n", $pText);
	return $pText;
}

/*
	Премахваме заменя множеството от whitespace-ове с единичен интервал и накрая прави трим
*/
function xslTrim($pText){
	$pText = preg_replace('/\s+/', ' ', $pText);
	$pText = trim($pText);
	return $pText;
}



/**
 * Escape-ва стойността за да можем да я сложим в xml-а
 *
 * Първо правим convertStringToUtf8  за да няма не-utf8 символи ,
 * а после правим htmlspecialchars за да се ескейпнат правилно <>& и т.н.
 */

function xmlEscape($pValue){
	$pValue = convertStringToUtf8($pValue);
	return htmlspecialchars($pValue);
}

/*
	Махаме xml-таговете. За целта лоудваме в xml dom и връщаме textContent-а на руута. Ако не стане лоуд-а - просто връщаме резултата от изпълнението на функцията xmlEscape
*/
function stripXmlTags($pValue){
	$pValue = convertStringToUtf8($pValue);
	$lXmlDom = new DOMDocument('1.0', 'utf-8');
	$lReplacements = array(
		'&' => '&amp;',
		'\'' => '&apos;'
	);

	$lXml = '<root>' . str_replace(array_keys($lReplacements), array_values($lReplacements), $pValue) . '</root>';//Escape-ваме xml ентитата не заместваме " и <> за да може да се разпознаят таговете правилно

	if($lXmlDom->loadXML($lXml)){
		return xmlEscape($lXmlDom->documentElement->textContent);
	}
	//Ако не успеем - махаме всичко между < и >
	$pValue = preg_replace('/\<.*\>/smU', '', $pValue);//U modifier за да може да не мачнем всичко от начално < и най-крайно(на друг таг) >
	return xmlEscape($pValue);
}

/*
 * Конвертираме стринга в utf-8, за да няма непознати символи в xml-a
 * Първо правим html_entity_decode за да може ако има entity-та (напр &micro;) да станата символи
 * После правим mb_convert_encoding в utf-8 за да няма не-utf8 символи

*/
function convertStringToUtf8($pValue){
	$pValue = html_entity_decode($pValue, ENT_COMPAT, "UTF-8");
	//$pValue = mb_convert_encoding($pValue, 'UTF-8', "ISO-8859-15");
	return $pValue;
}

/**
	Обработва подадената дата в формат дд Месец ГГГГ
	и връща масив във формат
		day => дд,
		month => Месец,
		year => YYYY
	Ако не успее да разчете датата връща false
*/
function splitDateToArray($pDate){
	//~ var_dump($pDate, date_parse('20 May 2011'));
	if(preg_match('/(\d{1,2})[\s\/\-\. ]+([A-Za-z]+)[\s\/\-\. ]+(\d{4})/', $pDate, $lMatch)){
		$lResult = array(
			'day' => $lMatch[1],
			'month' => $lMatch[2],
			'year' => $lMatch[3],
		);

		return $lResult;
	}
	return false;
}

/**
	Връща номера на месеца с подаденото име
*/
function GetMonthNumber($pMonthName){
	$lMatchArr = array(
		'/january/i' => 1,
		'/february/i' => 2,
		'/march/i' => 3,
		'/april/i' => 4,
		'/may/i' => 5,
		'/june/i' => 6,
		'/july/i' => 7,
		'/august/i' => 8,
		'/september/i' => 9,
		'/october/i' => 10,
		'/november/i' => 11,
		'/december/i' => 12,
	);
	foreach($lMatchArr as $lExpression => $lReturn){
		if(preg_match($lExpression, $pMonthName))
			return $lReturn;
	}
	//Ако не разпознаем номера - връщаме името
	return $pMonthName;

}

/**
	Връща името на месеца с подадения номер
*/
function GetMonthName($pMonthNum){
	global $gMonths;
	$pMonthNum = (int)$pMonthNum;
	//Връщаме името с главна буква
	return ucwords($gMonths[$pMonthNum]);

}

function parseToInt($pItem){
	return (int) $pItem;
}

/**
 * Връща специалния символ, който ще е label на това uri.
 * За целта пази уритата в статичен масив
 * @param unknown_type $pUri
 */
function getUriSymbol($pUri){
	static $lUris = array();
	static $lUriSymbols = array("†","‡", "§", "|", "¶", "#");
	$pUri = trim($pUri);
	if(array_key_exists($pUri, $lUris)){
		return $lUris[$pUri];
	}
	$lUrisLength = count($lUris);
	$lSymbolsLength = count($lUriSymbols);
	$lUris[$pUri] = str_repeat($lUriSymbols[$lUrisLength % $lSymbolsLength], floor($lUrisLength / $lSymbolsLength) + 1);
	return $lUris[$pUri];
}

/**
 * Връща номера на aff възела - маха от него всички букви, т.е.
 * 		'А1' => 1
 * @param unknown_type $pSymbol
 */
function parseAffSymbol($pSymbol){
	$pSymbol = trim($pSymbol);
	if($pSymbol == '')
		return $pSymbol;
	return preg_replace('/[A-Z]/im', '', $pSymbol);
}

/*
 * a) ако започва с urn:lsid:zoobank.org връща 1:

b) ако започва http:// ili www. връща 2
	иначе връща 0
 */
function parseUriText($pUri){
	$pUri = trim($pUri);
	if(preg_match('/^urn\:lsid\:zoobank\.org/ism', $pUri)){
		return 1;
	}
	if(preg_match('/^(http:\/\/|www\.)/ism', $pUri)){
		return 2;
	}
	return 0;
}

/**
	Изкарваме резултата (json_encode-нат) и exit-ваме
*/
function returnAjaxResult($pResult){
	echo json_encode($pResult);
	exit();
}

/**
	Пробва да зареди от xml-a на статията, стойностите на кеш полетата за финализирането на статията
	//Зареждаме полетата:
		заглавие,
		автори,
		issue,
		fpage,
		lpage,
		DOI,
		articleID
*/
function FetchDataForArticleFinalization($pKfor){
	$lArticleId = (int)$pKfor->lFieldArr['article_id']['CurValue'];
	if(!$lArticleId)
		return;
	$lCon = Con();
	$lSql = 'SELECT id, xml_content, journal_id FROM articles WHERE id = ' . (int)$lArticleId;
	$lCon->Execute($lSql);

	//Попълваме журнала
	$pKfor->lFieldArr['journal_id']['CurValue'] = (int)$lCon->mRs['journal_id'];
	$lXml = $lCon->mRs['xml_content'];

	$lXmlDom = new DOMDocument('1.0');
	if(!$lXmlDom->loadXML($lXml)){
		return ;
	}
	$lXPath = new DOMXPath($lXmlDom);

	/**
		Това е масива на полетата, които ще попълваме. Формата е следния
			име–на–полето–в–кфора => масив със следния формат
				XPath => Xpath за селектиране на възлите с информация за полето,
				ConcatMultiple => Ако с xpath-а са намерени няколко възела и този параметър не е подаден - връща се стойността на първия възел.
					Ако стойността на този възел е true - за стойност на полето се връщат конкатенираната стойност на всички възли.
				ConcatSeparator => Разделител, който да се използва, когато се конкатенират стойностите на възлите
	*/
	$lFieldExpressions = array(
		'fpage' => array(
			'XPath' => '/article/front/article-meta/fpage',
			'ConcatMultiple' => false,
		),
		'lpage' => array(
			'XPath' => '/article/front/article-meta/lpage',
			'ConcatMultiple' => false,
		),
		'issue' => array(
			'XPath' => '/article/front/article-meta/issue',
			'ConcatMultiple' => false,
		),
		'doi' => array(
			'XPath' => '/article/front/article-meta/article-id[@pub-id-type=\'doi\']',
			'ConcatMultiple' => false,
		),
		'title' => array(
			'XPath' => '/article/front/article-meta/title-group/article-title',
			'ConcatMultiple' => false,
		),
		'authors' => array(
			'XPath' => '/article/front/article-meta/contrib-group/contrib[@contrib-type=\'author\']/name',
			'ConcatMultiple' => true,
			'ConcatSeparator' => ', ',
		),
	);

	foreach($lFieldExpressions as $lCurrentField => $lFieldData){
		$lQuery = $lFieldData['XPath'];
		$lNodes = $lXPath->query($lQuery);
		$lResult = '';
		if( $lNodes->length > 1 && (int)$lFieldData['ConcatMultiple']){
			for($i = 0; $i < $lNodes->length; ++$i){
				if($i > 0){//Слагаме разделителя
					$lResult .= $lFieldData['ConcatSeparator'];
				}
				$lResult .= trim($lNodes->item($i)->textContent);
			}
		}else{
			$lResult = trim($lNodes->item(0)->textContent);
		}
		$lResult = preg_replace('/\s+/', ' ', $lResult);
		$lResult = trim($lResult);
		if($lResult){
			$pKfor->lFieldArr[$lCurrentField]['CurValue'] = $lResult;
		}
	}
	//Накрая взимаме пенсофт id-то на статията от дой номера
	$lDoi = $pKfor->lFieldArr['doi']['CurValue'];
	if($lDoi){
		$pKfor->lFieldArr['pensoft_id']['CurValue'] = getArticleIdFromDoi($lDoi);
	}

}

function showEol_ExportLinkIfExists($pRs){
	return showExportLinkIfExistsDbList($pRs, 'eol_');
}

function showWiki_ExportLinkIfExists($pRs){
	return showExportLinkIfExistsDbList($pRs, 'wiki_');
}

function showKeys_ExportLinkIfExists($pRs){
	return showExportLinkIfExistsDbList($pRs, 'keys_');
}

function showExportLinkIfExistsDbList($pRs, $pPrefix){
	return showExportLinkIfExists($pRs[$pPrefix . 'export_id'], $pRs[$pPrefix . 'link'], $pRs[$pPrefix . 'link_title'], $pRs[$pPrefix . 'is_generated'], $pRs[$pPrefix . 'has_results'], $pRs[$pPrefix . 'is_uploaded'], $pRs[$pPrefix . 'upload_has_errors']);
}

function showExportLinkIfExists($pExportId, $pLink, $pTitle, $pIsGenerated, $pHasResults, $pIsUploaded, $pUploadHasErrors){
	if(!(int)$pExportId)
		return;
	$lClass = 'export_not_generated';
	if((int)$pIsUploaded){
		if((int)$pUploadHasErrors){
			$lClass = 'export_uploaded_with_errors';
		}else{
			$lClass = 'export_uploaded_without_errors';
		}
	}elseif((int)$pIsGenerated){
		if(!(int)$pHasResults){
			$lClass = 'export_generated_without_results';
		}else{
			$lClass = 'export_generated_with_results';
		}
	}
	$lResult = '<a href="' . $pLink . '" target="_blank" class="' . $lClass . '">' . $pTitle . '</a>';
	return $lResult;
}

function getStaticCount(){
	static $lCount = 1;
	return $lCount++;
}

/**
	Връща името на списанието на статията върху която се изпълнява xsl трансформация.
*/
function getXslTransformedArticleJournalTitle(){
	global $gXslProcessedArticleId;
	if(!$gXslProcessedArticleId)
		return '';
	$lCon = Con();
	$lSql = 'SELECT j.pensoft_title FROM journals j
		JOIN articles a ON a.journal_id = j.id
		WHERE a.id = ' . (int) $gXslProcessedArticleId . '
	';
	$lCon->Execute($lSql);
	return trim($lCon->mRs['pensoft_title']);
}

/**
	Връща името на списанието на статията върху която се изпълнява xsl трансформация.
*/
function getXslTransformedArticleJournalUrlTitle(){
	global $gXslProcessedArticleId;
	if(!$gXslProcessedArticleId)
		return '';
	$lCon = Con();
	$lSql = 'SELECT j.pensoft_id FROM journals j
		JOIN articles a ON a.journal_id = j.id
		WHERE a.id = ' . (int) $gXslProcessedArticleId . '
	';
	$lCon->Execute($lSql);
	$lPensoftJournalId = (int) $lCon->mRs['pensoft_id'];

	$lCmsCon = GetCmsDBConnection();
	$lSql = 'SELECT j.URL_TITLE as url_title
		FROM J_JOURNALS j
		WHERE j.journal_id= ' . (int)$lPensoftJournalId;
	$lCmsCon->Execute($lSql);
	$lCmsCon->MoveFirst();
	return trim($lCmsCon->mRs['url_title']);
}
?>