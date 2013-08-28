<?php
$x = session_start(); //na servera session_autostart e 0
//~ var_dump($x);
include_once(getenv("DOCUMENT_ROOT") . "/lib/conf.php");
require_once(PATH_ECMSFRCLASSES . 'static.php');
require_once(PATH_CLASSES . 'static.php');

$gTimeLogger = new ctime_logger(array());
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
	UserRedir($user);	}

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
				<br/>
			</div>
			<div class="unfloat"></div>
			<div id="footer">
				' . getstr('admin.copywright') . '
			</div>
			</div>
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

function parseIndesignXml($pXml, $pReplaceBrSymbol){
	global $G_INDESIGN_TAGS_SPLIT;
	global $gEcmsLibRequest;
	//Loadvame XML-a za da moje da go vzemem s hubav output - s neobhodimite intervali i dyrvovidna struktura
	
	if( !$gEcmsLibRequest )
		return GetFormattedXml($pXml, $pReplaceBrSymbol);	
	$lDOM = new DOMDocument("1.0");	
	$lDOM->preserveWhiteSpace = true;	
	if (!$lDOM->loadXML($pXml))
		return $pXml;
	
	parseIndesignXmlIgnoreTags($lDOM);	
	
	parseIndesignXmlBreakTags($lDOM);	
	//~ exit;

	return GetFormattedXml($lDOM->saveXML(), $pReplaceBrSymbol);
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
//Splitva dadeni nodove, koito sydyrjat split node child. pTagsToSplit - imenata na tagovete sred koito tyrsim
function parseIndesignXmlBreakTags(&$pXmlDom){	
	$lXPaths = getBreakTagsXpaths();
	if( is_array( $lXPaths ) ){
		$lXpath = new DOMXPath($pXmlDom);
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
					while($lCurrentNode && $lCurrentNode !== $lParentNode ){//Obikalqme nadolu po dyrvoto i slagame node-ovete v nov node					
						$lNewParent = tagSplitProcessNodeLevel($pXmlDom, $lSiblingFound, $lNewNode, $lCurrentNode);
					}				
					
					while($lCurrentNode && $lCurrentNode->parentNode != $pXmlDom && $lCurrentClimb > 0){
						$lNewParent = tagSplitProcessNodeLevel($pXmlDom, $lSiblingFound, $lNewNode, $lCurrentNode);
						$lCurrentClimb--;
					}
					
					if( $lNewNode && $lSiblingFound ){					
						$lNextSibling = $lCurrentNode->nextSibling;					
						if( $lNextSibling ){
							$lCurrentNode->parentNode->insertBefore( $lNewParent, $lNextSibling );
						}else{
							$lCurrentNode->parentNode->appendChild( $lNewParent );
						}
					}
					
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

function showYesNo($pParam){
	if((int)$pParam )
		return getstr('global.yes');
	return getstr('global.no');
}

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
		foreach($lTaxonNames as $lSingleTaxonName){
			$lCurrentTaxonDiv = new csimple(array(
				'templs' => array(
					'G_DEFAULT' => 'article_html.taxonBaloonDiv',
				),
				'cache' => 'article_html_taxon_baloon',
				'cachetimeout' => CACHE_TIMEOUT_LENGTH,
				'taxon_name' => $lSingleTaxonName,
			));
			//~ trigger_error("Exec: " . $lSingleTaxonName , E_USER_NOTICE);
			$lTaxonDivs .= $lCurrentTaxonDiv->DisplayC();
		}
		if (count($lTaxonNames)) {
			$ltmpfile=tempnam('/tmp/', 'TAXONP_');
			if (file_put_contents($ltmpfile, implode("\n", $lTaxonNames))) {
				$cmd="php ".TAXON_PROFILE_CC_SCRPT." ".$ltmpfile." ".'"'.TAXON_PROFILE_CC_BASEURL.'"'." 0 > /dev/null &"; 
				//~ $cmd="php ".TAXON_PROFILE_CC_SCRPT." ".$ltmpfile." ".'"'.TAXON_PROFILE_CC_BASEURL.'"'." 0 > $ltmpfile &"; 
				trigger_error("Exec: " .$cmd , E_USER_NOTICE);
				exec($cmd );
			} else 
				trigger_error("Failed to write toxon names to file: " .$ltmpfile , E_USER_NOTICE); //da se napravi na kvoto triabva
		}
		
		
		$pXSLResult = preg_replace($lTaxonNamePattern, '', $pXSLResult);
		$pXSLResult = preg_replace($lTaxonDivPattern, $lTaxonDivs, $pXSLResult);
	}
	return $pXSLResult;
	
}

function getSingleTaxonBaloon($pTaxonName){
	$lUrl = ADM_URL . TAXON_BALOON_SRV . '?taxon_name=' . rawurlencode($pTaxonName);
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
				}
				formatXmlRecursive($lChild, $lTagContent, $pXPath, &$pDeclaredNamespaces, $pDepth + 1, $lFormatXml);
			}
		}else{//Comment
			$lTagContent = EscapeTextContent($pCurrentElement->nodeValue);
		}
		
		$lTagEnd = GetElementCloseTag($pCurrentElement);
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
		
		$lResult .= $lSingleAttribute->nodeName . '="' . $lSingleAttribute->nodeValue . '"';
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
				if( !$lCn->Execute('SELECT PicsUpload(1, ' . (int) $pPicId. ', 0,\'' . q($pTitle) . '\', \'' . q($pFnUpl) . '\', \'' . q($PicText) . '\') as picid') ){
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
?>