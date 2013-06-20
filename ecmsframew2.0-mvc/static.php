<?php
/*
 * ---------------------------------------/ Globalni funkcii za vsi4ki saitove
 * /---------------------------------------
 */
require_once ('db.php');
require_once ('lang.php');
require_once ('adodb-time.inc.php');
require_once ('kforhist.php');

$langfile = PATH_LANGUAGES . getlang(true) . '.str.php';
if(is_file($langfile)){
	require_once ($langfile);
}

/*
 * Понеже функцията за ескейпване на mysql иска конекция - ще си направим една
 * глобална вместо при всяко ескейпване да правим нова
 */
$gDbConForEscape = new DBCn();
$gDbConForEscape->Open();

$gMonths = array(
	1 => getstr('static.php.jan'),
	2 => getstr('static.php.feb'),
	3 => getstr('static.php.mrt'),
	4 => getstr('static.php.apr'),
	5 => getstr('static.php.may'),
	6 => getstr('static.php.jun'),
	7 => getstr('static.php.jul'),
	8 => getstr('static.php.aug'),
	9 => getstr('static.php.sep'),
	10 => getstr('static.php.okt'),
	11 => getstr('static.php.nov'),
	12 => getstr('static.php.dec')
);

$gDaysOfWeek = array(
	0 => getstr('static.php.sunday'),
	1 => getstr('static.php.monday'),
	2 => getstr('static.php.tuesday'),
	3 => getstr('static.php.wednesday'),
	4 => getstr('static.php.thursday'),
	5 => getstr('static.php.friday'),
	6 => getstr('static.php.saturday')
);

if(! function_exists('__autoload')){
	/**
	 * Тази функция се ползва когато се опитваме да създадем нов клас, който още
	 * не е дефиниран.
	 * Тя определя местата на които ще се търси дефиницията на класа.
	 * (Името на класа трябва да съвпада с името на файла в който е дефиниран
	 * класа)
	 * Тази функция може да се предефинира в различните сайтове.
	 * По подразбиране първо се гледа в папката с класове на текущия сайт. Ако
	 * класа го няма там се гледа
	 * в папката на общите класове за всички сайтове и ако го няма и там се
	 * гледа
	 * в папката на фреймуърка
	 *
	 * @param $pClassName string
	 *       	 - името на класа, чиято дефиниция ще търсим
	 */
	function __autoload($pClassName) {

		$pClassName = strtolower($pClassName);
		$lSearchPaths = array(
			PATH_CLASSES . SITE_NAME . "/" . 'models/',
			PATH_CLASSES . SITE_NAME . "/" . 'views/',
			PATH_CLASSES . SITE_NAME . "/" . 'views/pages/',
			PATH_CLASSES . SITE_NAME . "/" . 'controllers/',
			PATH_CLASSES . SITE_NAME . "/",

			PATH_CLASSES . 'models/',
			PATH_CLASSES . 'views/',
			PATH_CLASSES . 'views/pages/',
			PATH_CLASSES . 'controllers/',
			PATH_CLASSES,

			PATH_ECMSFRCLASSES . 'models/',
			PATH_ECMSFRCLASSES . 'views/',
			PATH_ECMSFRCLASSES . 'views/pages/',
			PATH_ECMSFRCLASSES . 'controllers/',
			PATH_ECMSFRCLASSES,

			PATH_ECMSSHOPCLASSES
		);
		foreach($lSearchPaths as $lCurrentPath){
			$lCurrentFilePath = $lCurrentPath . $pClassName . ".php";
			if(file_exists($lCurrentFilePath)){
				include_once ($lCurrentFilePath);
				return;
			}
		}

	}
}

function ipinnet($ip, $net) {
	$n = split('/', $net);
	$mask = (pow(2, $n[1]) - 1) << (32 - $n[1]);
	return (($mask & ip2long($n[0])) == ($mask & ip2long($ip)));
}

function checkAllowedIP($ip, $netarr) {
	foreach($netarr as $cnetwork){
		if(ipinnet($ip, $cnetwork)){
			return 1;
		}
	}
	return 0;
}

// html escape
function h($str) {
	return htmlspecialchars($str);
}

if(get_magic_quotes_gpc()){
	function s($str) {
		if(! is_null($str))
			return stripslashes($str);
		else
			return $str;
	}
}else{
	function s($str) {
		return $str;
	}
}

// query escape
function q($str) {
	global $gDbConForEscape;
	$lDb = $gDbConForEscape->DB;
	$lResult = $lDb->Escape_String(iconv('UTF-8', 'UTF-8//IGNORE', $str), $gDbConForEscape->mhCn);
	return $lResult;
}

function arrstr_q($str) {
	return "'" . q($str) . "'";
}
function arrint_q($str) {
	if(! is_int($str))
		return 'null';
	else
		return $str;
}

function pg_unescape_array($val) {
	$val = substr($val, 1, - 1);
	$valarr = explode(',', $val);
	$resarr = array();
	$rk = 1;
	$buf = null;

	foreach($valarr as $k => $v){
		if((substr($v, 0, 1) == '"') && (substr($v, - 1) == '"')){
			$resarr[$rk] = str_replace('\"', '"', substr($v, 1, - 1));
			$rk ++;
		}else if(substr($v, 0, 1) == '"'){
			$buf = substr($v, 1);
		}else if(substr($v, - 1) == '"'){
			$resarr[$rk] = str_replace('\"', '"', $buf . "," . substr($v, 0, strlen($v) - 1));
			$rk ++;
			$buf = null;
		}else if(! $buf){
			$resarr[$rk] = $v;
			$rk ++;
		}else
			$buf .= "," . $v;
	}

	return $resarr;
}

function sqlnull($p, $pt = 0) {
	if(! $p)
		return "NULL";
	else if($pt)
		return "'" . q($p) . "'";
	else
		return q($p);
}

function WinToUnicode($pStr) {
	$pStr = iconv('CP1251', 'UTF-8', $pStr);
	return $pStr;
}

function UnicodeToWin($pStr) {
	$pStr = iconv('UTF-8', 'CP1251', $pStr);
	return $pStr;
}

function CheckMail($email) {
	if(! ereg("^[A-Za-z0-9_\.\-]+@([A-Za-z0-9_\-])+(\.([A-Za-z0-9_\-])+)+$", $email)){
		return 0; // Ne e gotin mail
	}else{
		return 1;
	}
}

function CheckEGN($egn) {
	if(mb_strlen($egn, 'UTF-8') != 10)
		return - 1;
	if(! preg_match('/[0-9]+/', $egn))
		return - 2;

	$tegla = array(
		2,
		4,
		8,
		5,
		10,
		9,
		7,
		3,
		6
	);
	$tmpsum = 0;
	foreach($tegla as $i => $tgl){
		$tmpsum += (int) $egn{$i} * $tgl;
	}

	$checksum = $tmpsum % 11;
	if($checksum > 9)
		$checksum = 0;
	if((int) $egn{9} != $checksum)
		return - 3;

	return 1;
}

function CheckBULSTAT($bs) {
	if(mb_strlen($bs, 'UTF-8') != 9 && mb_strlen($bs, 'UTF-8') != 13)
		return - 1;
	if(! preg_match('/[0-9]+/', $bs))
		return - 2;

	$tmpsum = 0;
	$tegla = array(
		1,
		2,
		3,
		4,
		5,
		6,
		7,
		8
	);
	foreach($tegla as $i => $tgl){
		$tmpsum += $tgl * $bs{$i};
	}
	$checksum = $tmpsum % 11;
	if($checksum != 10 && $bs{8} == $checksum){
		if(mb_strlen($bs, 'UTF-8') == 9)
			return 1;
	}else{
		$tmpsum = 0;
		$tegla = array(
			3,
			4,
			5,
			6,
			7,
			8,
			9,
			10
		);
		foreach($tegla as $i => $tgl){
			$tmpsum += $tgl * $bs{$i};
		}
		$checksum = $tmpsum % 11;
		if($checksum == 10)
			$checksum = 0;
		if($bs{8} == $checksum){
			if(mb_strlen($bs, 'UTF-8') == 9)
				return 1;
		}else{
			return - 3;
		}
	}

	if(mb_strlen($bs, 'UTF-8') == 13){
		$tmpsum = 0;
		$tegla = array(
			8 => 4,
			9 => 9,
			10 => 5,
			11 => 7
		);
		foreach($tegla as $i => $tgl){
			$tmpsum += $tgl * $bs{$i};
		}
		$checksum = $tmpsum % 11;
		if($checksum == 10)
			$checksum = 0;
		if($bs{12} != $checksum){
			return - 4;
		}
		return 1;
	}
}

function BuildT2Query($txt, $fld = false) {
	setlocale(LC_CTYPE, 'bg_BG');
	// Buildvame tsearch stringa za tursene na dumi
	$lArr1 = array(
		UnicodeToWin(" и "),
		UnicodeToWin(" или "),
		" and ",
		" or "
	);
	$lArr2 = array(
		" & ",
		" | ",
		" & ",
		" | "
	);
	$lArr3 = array(
		'&',
		'|',
		'(',
		')'
	);

	$txt = WinToUnicode(str_ireplace($lArr1, $lArr2, UnicodeToWin($txt)));
	setlocale(LC_CTYPE, '');
	$res = explode(' ', $txt);

	$lTmpStr = '';
	if(count($res)){
		$i = 0;
		foreach($res as $rrr){
			if(mb_strlen($rrr, 'UTF-8') == 0)
				continue;

				// vrushta true ako e operaciq
			$this_oper = in_array($rrr, $lArr3);
			if(! $prev_oper && ! $this_oper){
				if($i)
					$lTmpStr .= ' &';
			}
			if(! $this_oper){
				// mahame neshtata deto ne sa dumi
				$rrr = str_replace(array(
					'"',
					',',
					';',
					'.'
				), '', $rrr);
				// Proverqvame za kusi dumi
				// ~ if (mb_strlen($rrr, 'UTF-8') < (int)T2STOPWORD_LEN) {
				// ~ $lTmpStr = substr($lTmpStr, 0, (strlen($lTmpStr) - 2));
				// ~ $this->skipwarr[$rrr] = $rrr;
				// ~ continue;
				// ~ }
			}

			$prev_oper = $this_oper;
			$lTmpStr .= ' ' . $rrr;
			$i ++;
		}
		$lTmpStr = trim($lTmpStr);
	}

	$lTmpStr = mb_strtolower(q($lTmpStr), 'UTF-8');

	if($fld)
		return $fld . ' @@ to_tsquery(\'default\', \'' . $lTmpStr . '\')';
	return $lTmpStr;
}

if(! function_exists('showItemIfExists')){
	function showItemIfExists($item, $leftCont, $rightCont) {
		return ($item ? $leftCont . $item . $rightCont : '');
	}
}

if(! function_exists('br2nl')){
	function br2nl($str) {
		return preg_replace('/\<br[\s\/]*\>/', "\n", $str);
	}
}

if(! function_exists('getstr')){
	function getstr($p) {
		global $STRARRAY;
		if(! is_array($STRARRAY))
			$STRARRAY = array();
		if(array_key_exists($p, $STRARRAY)){
			return $STRARRAY[$p];
		}else{
			return $p;
		}
	}
}

function dmy2tst($p, $strict = true) {
	if(! preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{1,4})/', $p, $a))
		return false;

	if($strict){
		if(! checkdate((int) $a[2], (int) $a[1], (int) $a[3]))
			return false;
	}

	return mktime(0, 0, 0, $a[2], $a[1], $a[3]);
}

function GetConfHash($uname, $email) {
	$secret = 'Levski_Shampion';
	$time = mktime();
	return md5($secret . $uname . $time . $email);
}

function BuildT2SearchClause($txt, $shema = 'default', $flds = array(), $vectors = array()) {
	static $prev_oper = false;
	if(! $txt)
		return ' true';
		// Buildvame like stringa za tursene na frazi
	if(preg_match_all('/"[^"]+"/', $txt, $a)){
		foreach($a[0] as $stext){
			$LikeSrch[] = mb_strtolower(str_replace('"', '', $stext), 'UTF-8');
		}
	}

	setlocale(LC_CTYPE, 'bg_BG');
	// Buildvame tsearch stringa za tursene na dumi
	$lArr1 = array(
		UnicodeToWin(" и "),
		UnicodeToWin(" или "),
		" and ",
		" or "
	);
	$lArr2 = array(
		" & ",
		" | ",
		" & ",
		" | "
	);
	$lArr3 = array(
		'&',
		'|',
		'(',
		')'
	);

	$txt = WinToUnicode(str_ireplace($lArr1, $lArr2, UnicodeToWin($txt)));
	setlocale(LC_CTYPE, '');
	$res = explode(' ', $txt);

	$lTmpStr = '';
	if(count($res)){
		$i = 0;
		foreach($res as $rrr){
			if(mb_strlen($rrr, 'UTF-8') == 0)
				continue;

				// vrushta true ako e operaciq
			$this_oper = in_array($rrr, $lArr3);
			if(! $prev_oper && ! $this_oper){
				if($i)
					$lTmpStr .= ' &';
			}
			if(! $this_oper){
				// mahame neshtata deto ne sa dumi
				$rrr = str_replace(array(
					'"',
					',',
					';',
					'.'
				), '', $rrr);
			}

			$prev_oper = $this_oper;
			$lTmpStr .= ' ' . $rrr;
			$i ++;
		}

		$VectorSrch = trim(q($lTmpStr));
	}

	$tsearchStr = '';
	$likeStr = '';

	if(! is_array($LikeSrch))
		$LikeSrch = array();

	foreach($flds as $fld){
		foreach($LikeSrch as $k => $like){
			$res2[] = 'lower(' . $fld . ') LIKE \'%' . $like . '%\'';
		}
		if(count($res2))
			$res1[] = '(' . implode(' AND ', $res2) . ')';
	}
	if(count($res1))
		$likeStr = ' AND (' . implode(' OR ', $res1) . ')';

	if($VectorSrch){
		$res3 = array();
		foreach($vectors as $fld){
			$res3[] = $fld . ' @@ to_tsquery(\'' . $shema . '\', \'' . $VectorSrch . '\')';
		}
		$tsearchStr = '(' . implode(' OR ', $res3) . ')';
	}

	return $tsearchStr . $likeStr;
}

function getPicDirName($guid) {
	/*
	 * ================================================================
	 * Da ne se promenq bez razreshenie ot Sasho Pochinkov !!!!!! /
	 * ================================================================
	 */
	$dir = PATH_DL;
	if(defined('SPLIT_PHOTO_DIR') && (int) SPLIT_PHOTO_DIR){
		if(! (int) $guid)
			return $dir;
		$subdir = ceil($guid / 10000);
		return $dir . $subdir . '/';
	}
	return $dir;
}

/**
 * Понеже грешките са във формат ERROR: pErrorMsg, а ни се налага да правим getstr - трябва да махнем ERROR:
 * @param unknown_type $pDbError
*/ 
function getDbError($pDbError){
	return trim(str_replace('ERROR: ', '', $pDbError));
}

function executeConsoleCommand($pCommand, $pArgs, $pUsePassThru = 1){
	if( !is_array($pArgs))
		$pArgs = array();
	/**
		Масив с кодовете на операциите
	*/
	if( (int) USE_PERL_EXECS ){
		$lCommandsArr = array(
			1 => 'convert',
			2 => 'identify',
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