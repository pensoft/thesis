<?php

define("ACTION_CHECK", 1);		// checkva samo dali tipovete na promenlivite sa si takiva kakvito trjabva
define("ACTION_CCHECK", 2);		// checkva i ostanalite custom checkove
define("ACTION_EXEC", 4);		// execute-va sql-a
define("ACTION_FETCH", 8);		// fetch-va resultatite ot sql-a
define("ACTION_SHOW", 16);		// pokajva formata
define("ACTION_REDIRECT", 32);	// redirect-va kum urlto na action-a
define("ACTION_REDIR", 32);	// redirect-va kum urlto na action-a (alias na REDIRECT)
define("ACTION_VIEW", 64);	// display-va formata vyv view mode
define("ACTION_REDIRVIEW", 128);	// display-va formata vyv view mode
define("ACTION_REDIRERROR", 256);	// redirva-va formata vyv view mode ako e imalo error na actiona i displayva errora

//Date Type 1 = wrong time, 2 wrong date, 3 wrong datetime
define("DATE_TYPE_ALL", 0); //Date zaduljitelno + time (ne zaduljitelno)
define("DATE_TYPE_TIME", 1); //Samo time puska
define("DATE_TYPE_DATE", 2); //Samo date puska
define("DATE_TYPE_DATETIME", 3); //Trqbva zaduljitelno i date i time

define("MANY_TO_MANY", 1);
define("MANY_TO_STRING", 2);
define("MANY_TO_BIT", 3);
define("MANY_TO_BIT_ONE_BOX", 4);
define("MANY_TO_SQL_ARRAY", 5);


define("ERR_WRONG_TIME", getstr('form.errorWrongTime'));
define("ERR_WRONG_DATE", getstr('form.errorWrongDate'));
define("ERR_WRONG_DATETIME", getstr('form.errorWrongDateTime'));

define("ERR_EMPTY_NUMERIC", getstr('form.errorEmptyNumericField'));
define("ERR_EMPTY_STRING", getstr('form.errorEmptyStringField'));
define("ERR_EMPTY_XML", getstr('form.errorEmptyXmlField'));
define("ERR_WRONG_XML", getstr('form.errorWrongXmlField'));
define("ERR_NAN", getstr('form.errorNotANumber'));

define('ERR_NO_CONTROLLER_METHOD_FOR_ACTION', getstr('form.errorNoControllerMethodForAction'));
define('ERR_NO_CONTROLLER', getstr('form.errorNoControllerPassedToForm'));
define('ERR_COULD_NOT_EXECUTE_CONTROLLER_METHOD', getstr('form.errorCouldNotExecuteControllerMethodForCurrentAction'));
define('ERR_CONTROLLER_STATES_THERE_ARE_ERRORS_BUT_DOESNT_PROVIDE_THEM', getstr('form.errorControllerSignaledThereAreErrorsButDidntProvideThem'));
define('ERR_CONTROLLER_METHOD_IS_NOT_CALLABLE', getstr('form.errorControllerMethodIsNotCallable'));
define('ERR_CAPTCHA_WRONG_CODE', getstr('form.errorWrongCaptchaCode'));
define('DEFAULT_FORM_ACTION', 'new');
define('DEFAULT_FORM_CHECK_ACTION', 'check_fld');
define('FORM_DEFAULT_METHOD', 'get');
// define za greshkite
//~ define("ERR_WRONG_TIME", "Invalid time! <br> Please re-enter time using this format: HH:MM AM");
//~ define("ERR_WRONG_DATE", "Invalid date value. DD/MM/YYYY");
//~ define("ERR_WRONG_DATETIME", "Wrong date or time value. DD/MM/YYYY HH:MM");

//~ define("ERR_EMPTY_NUMERIC", "Empty Numeric Value");
//~ define("ERR_EMPTY_STRING", "Empty String Value");
//~ define("ERR_NAN", "Not a numeric Value");


define("PRIMARY_KEY", 1);	// display-va formata vyv view mode


//~ define("MANY_TO_MANY", 1);
//~ define("MANY_TO_STRING", 2);
//~ define("MANY_TO_BIT", 3);
//~ define("MANY_TO_BIT_ONE_BOX", 4);

// tva e DEFAULTEN separator mejdu radio i checkbox
define("DEF_CONTR_SEP", '<br>');

// tva e defaulten separator mejdu valuetata pri MANY_TO_STRING conversiata
define("DEF_SQLSTR_SEPARATOR", ",");
define("DEF_EXT_ERROR_STRING", 'style="background-image: url(/images/req/20x20_red_non-req.gif);"');

define("HISTORY_PASIVE", 0);
define("HISTORY_CLEAR", 2);
define("HISTORY_ACTIVE", 1);


function Getbackurl() {
    if ($_POST["backurl"]) return $_POST["backurl"];
	if ($_REQUEST["backurl"]) return $_REQUEST["backurl"];
	if ($_SESSION["backurl"]) return $_SESSION["backurl"];
	return getenv("HTTP_REFERER");
}

function ClearParaminURL($url,$param) {
	$url = preg_replace("/\&+$param=([^\&]*)/", "", $url);
	$url = preg_replace("/\?+$param=([^\&]*)/", "?", $url);
	if ((substr($url, -1) == '?') || (substr($url, -1) == '&')) return substr($url, 0, -1);
	return $url;
}

function ClearHistory() {
	global $backurl, $forwardurl;
	$backurl = '';
	$forwardurl = getenv('REQUEST_URI');
	unset($_SESSION["backurl"]);
	$_SESSION["backurl"] = $forwardurl;
}

function CheckSameUrl($purl, $purl1) {
    $url = split("[\?&#]", $purl, 2);
    $url1 = split("[\?&#]", $purl1, 2);
    if (strcmp($url[0], $url1[0])) {
        if (substr($url[0], -9) == "index.php") $url[0] = substr($url[0], 0, -9);
        if (substr($url1[0], -9) == "index.php") $url1[0] = substr($url1[0], 0, -9);
        return !strcmp($url[0], $url1[0]);
    }
	return 1;
}

// vika se v htmlstart-a
function ProccessHistory() {
    global $backurl, $forwardurl, $historypagetype;
    switch ($historypagetype) {
		case HISTORY_ACTIVE:
			$backurl = Getbackurl();
			$forwardurl = getenv('REQUEST_URI');
			$forwardurl = ClearParaminURL($forwardurl, "backurl");

			if (!CheckSameUrl($forwardurl, $backurl)) {
				$forwardurl = $forwardurl . ((getenv('QUERY_STRING')) ? '&' : '?') . 'backurl=' . urlencode($backurl);
			} else {
				preg_match("/backurl=([^\&]*)/", $backurl, $match);
				$backurl = urldecode($match[1]);
				$forwardurl = $forwardurl . ((getenv('QUERY_STRING')) ? '&' : '?') . 'backurl=' . $match[1];
			}
			break;
		case HISTORY_CLEAR:
			ClearHistory();
			break;
		default:
			break;
    }
}


// vika se v htmlend-a
function UpdateHistory() {
    global $forwardurl, $historypagetype;
    if ($historypagetype != HISTORY_PASIVE) {
        unset($_SESSION["backurl"]);
        $_SESSION["backurl"] = $forwardurl;
    }
}

// razni podpomagashti funkcii

function array2bitint($pArr) {
	return array_sum($pArr);
}

function int2bitarray($pInt) {
	$lala = decbin($pInt);
	$bitArr = preg_split('//', $lala, - 1, PREG_SPLIT_NO_EMPTY);
	$bitArr = array_reverse($bitArr);
	$tArr = array();
	foreach($bitArr as $k => $v){
		if($v){
			$tArr[] = pow(2, $k);
		}
	}
	return $tArr;
}

/*
 * tova se vika dva pati - pri validirane, i pri zamestvane na stojnostite v
* sql-a
*/
function manageckdate(&$pStr, $pType, $pCheck = 1) {
	$pStr = trim($pStr);
	$lstrRes = '';
	$lstrError = '';
	//~ var_dump($pStr );

	if(! $pType || ($pType == DATE_TYPE_ALL) || ($pType == DATE_TYPE_DATETIME)){

		$lstrError = ERR_WRONG_DATETIME;
		$lSpacePos = strpos($pStr, ' ');

		if(! $lSpacePos){
			$lPossibleDate = $pStr;
		}else{
			$lPossibleDate = trim(substr($pStr, 0, $lSpacePos));
			$lPossibleTime = trim(substr($pStr, $lSpacePos));
		}
		$lRes = true;
		if(($pType == DATE_TYPE_DATETIME) && ! $lSpacePos){
			$lRes = false;
		}
		if(! $lSpacePos && $lRes){
			$lRes = ckdt3($lPossibleDate, $pCheck);
			if(! $pCheck && is_array($lRes)){
				$lstrRes = adodb_mktime(0, 0, 0, $lRes[2], $lRes[1], $lRes[3]);
				$lstrRes = newformatsqldate($lstrRes);
			}
		}elseif($lRes){
			$lRes = ckdt3($lPossibleDate, $pCheck);
			if(($pCheck && $lRes) || (! $pCheck && is_array($lRes))){
				if(! $pCheck){
					$lstrRes = adodb_mktime(0, 0, 0, $lRes[2], $lRes[1], $lRes[3]);
					$lstrRes = newformatsqldate($lstrRes);
				}
				$lRes = cktm($lPossibleTime, $pCheck);
				// ~ echo "manageckdate" . $lRes;

				if(! $pCheck && is_array($lRes)){
					$lstrRes .= " " . newformatsqltime($lRes);
				}
			}
		}

	}elseif($pType == DATE_TYPE_TIME){
		$lRes = cktm($pStr, $pCheck);
		$lstrRes = newformatsqltime($lRes);
		$lstrError = ERR_WRONG_TIME;
	}elseif($pType == DATE_TYPE_DATE){
		// ~ echo $pStr . ' ' . $pCheck . '<br>';

		$lRes = ckdt3($pStr, $pCheck);
		/*
		 * това не работи с двуцифрени години, а само с четирицифрени $lstrRes =
		* adodb_mktime(0, 0, 0, $lRes[2], $lRes[1], $lRes[3]); Това : '20' .
		* $lRes[3] трябва да се съглавува с js-то,
		*/

		// ~ var_dump( $lRes );
		// ~ echo '<br>'; // mm/dd/yy

		// ~ $lstrRes = adodb_mktime(0, 0, 0, $lRes[1], $lRes[2], '20' .
		// $lRes[3]);
		$lstrRes = adodb_mktime(0, 0, 0, $lRes[3], $lRes[2], $lRes[1]);
		$lstrRes = newformatsqldate($lstrRes);

		// ~ var_dump( $lstrRes ); echo '<br>';

		$lstrError = ERR_WRONG_DATE;
	}

	if($pCheck){
		if($lRes){
			return '';
		}else{
			return $lstrError;
		}
	}else{
		if($lRes === false){
			return $lRes;
		}else{
			return $lstrRes;
		}
	}

}

function ckdt3_a($pStr, $pCheck = 1) {
	if(! preg_match('/[\/\\\.\-]/', $pStr, $lMatches)){
		return false;
	}

	$lSeparator = $lMatches[0]; // Kato nqma skobi v reg expa v 0-q element e
	// kakvoto e machnalo

	if(! preg_match('/^(\d{2,4})\\' . $lSeparator . '(\d{1,2})\\' . $lSeparator . '(\d{1,2})$/i', $pStr, $lMatches)){
		return false;
	}

	if(! checkdate($lMatches[2], $lMatches[3], $lMatches[1])){
		return false;
	}

	// ~ $lNewMatches[1] = $lMatches[2];
	// ~ $lNewMatches[2] = $lMatches[1];
	// ~ $lNewMatches[3] = $lMatches[3];

	if(! $pCheck){
		return $lMatches;
	}else{
		return true;
	}
}


function ckdt3($pStr, $pCheck = 1) {
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



function cktm($pStr, $pCheck = 1) {
	if(! preg_match('/^(\d{1,2}):(\d{2}):(\d{2})$/i', $pStr, $lMatches))
		if(! preg_match('/^(\d{1,2}):(\d{2})$/i', $pStr, $lMatches))
		if(! preg_match('/^(\d{1,2})$/i', $pStr, $lMatches))
		return false;
	if($lMatches[1] < 0 || $lMatches[1] > 23){
		return false;
	}
	if(($lMatches[2] < 0 || $lMatches[2] > 59) && count($lMatches) > 2){
		return false;
	}
	/*
	 * # ifdef CHECK_SECONDS if (($lMatches[3] < 0 || $lMatches[3] > 59) &&
	 	* count($lMatches) > 3) { return false; } # endif
	*/
	if(! $pCheck){
		return $lMatches;
	}else{
		return true;
	}
}

function cktm_ampm($pStr, $pCheck = 1) {
	if(! preg_match('/^(\d{1,2}):(\d{2})\s*(am|pm)$/i', $pStr, $lMatches)){
		return false;
	}
	if($lMatches[1] < 1 || $lMatches[1] > 12){
		return false;
	}
	if($lMatches[2] < 0 || $lMatches[2] > 59){
		return false;
	}
	if(! $pCheck){
		if($lMatches[1] == 12)
			$lMatches[1] = 0;

		if(strtolower($lMatches[3]) == 'pm'){
			$lMatches[1] += 12;
		}
		return $lMatches;
	}else{
		return true;
	}
}

function formatsqldate($pTst) {
	// return adodb_date("m/d/Y H:i", $pTst); // Y-m-d H:i:s // H:ia
	return adodb_date("Y-m-d H:i", $pTst); // Y-m-d H:i:s // H:ia
}

// tova se vika,l kogato date se zamestva v sql-a
function newformatsqldate($pTst) {

	// return adodb_date("m-d-Y", $pTst); // Y-m-d H:i:s // H:ia
	return adodb_date("Y-m-d", $pTst); // Y-m-d H:i:s // H:ia
}

function newformatsqltime($parrTime) {
	return $parrTime[1] . ':' . ($parrTime[2] ? $parrTime[2] : '00') . ($parrTime[3] ? ':' . $parrTime[3] : ':00'); // .
	// ':'
	// .
	// ($parrTime[3]
	// ?
	// $parrTime[3]
	// :
	// '00');
}

// tova se vika, kogato date se vzema ot bazata
function formatformdate_a($pStr) {

	if(is_null($pStr))
		return '';

	// ~ var_dump( $pStr ); 01/09/2012
	$lSeparator = '/';

	// year in db is always 4 digits
	if(! preg_match('/^(\d{1,2})\\' . $lSeparator . '(\d{1,2})\\' . $lSeparator . '(\d{4})$/i', $pStr, $lMatches)){
		return '';
	}
	// ~ var_dump( $lMatches );
	$pStr = (int) $lMatches[2] . '/' . (int) $lMatches[1] . '/' . (int) substr($lMatches[3], 2, 2);
	return $pStr;

	// ~ if (preg_match('/\d+:\d+:\d+/', $pStr)) {
	// ~ return substr($pStr, 0, strrpos($pStr, ':')+3);
	// ~ }

	// ~ return $pStr; // date('d/m/Y H:i:s', strtotime($pStr));
	// return str_replace('.', '/', $pStr);
}


function formatformdate($pStr) {
        if (is_null($pStr))
                return '';

        if (preg_match('/\d+:\d+:\d+/', $pStr)) {
                return substr($pStr, 0, strrpos($pStr, ':')+3);
        }

        return $pStr; // date('d/m/Y H:i:s', strtotime($pStr));
        // return str_replace('.', '/', $pStr);
}


function formatformdate1($pStr) {
	if(is_null($pStr))
		return '';
	return $pStr; // date('d/m/Y H:i:s', strtotime($pStr));
	// return str_replace('.', '/', $pStr);
}

function getglobalformnumber() {
	static $globalformcount = 0;
	return (++ $globalformcount);
}

function getFCKtoolbar($tb) {
	global $FCK_Custom_Toolbars;
	$tbArr = array(
		1 => 'BasicTools',
		2 => 'AllTools'
	);
	if(is_array($FCK_Custom_Toolbars)){
		$tbArr = $tbArr + $FCK_Custom_Toolbars;
	}

	if(! array_key_exists($tb, $tbArr))
		return $tbArr[1];
	return $tbArr[$tb];
}

function float2array($pVal) {
	return ($pVal) ? $pVal : 'null';
}


function AddParamtoURL($url, $param) {
	if(! strlen($param))
		return $url;
	if(substr($param, - 1) == '=')
		return $url;
	if(strpos($url, '?') === FALSE)
		return $url . '?' . $param;
	else
		return $url . '&' . $param;
}

function CKMAXSTRLEN($pFld, $pMax) {
	return array(
		'Expr' => 'mb_strlen(' . $pFld . ', "UTF-8") > ' . $pMax . '',
		'ErrStr' => getstr('CKMAXSTRLEN') . $pMax . getstr('SYMB')
	);
}

function CKMINSTRLEN($pFld, $pMax) {
	return array(
		'Expr' => 'mb_strlen(' . $pFld . ', "UTF-8") < ' . $pMax . '',
		'ErrStr' => getstr('CKMINSTRLEN') . $pMax . getstr('SYMB')
	);
}

// ~ function CKEMAILADDR($pFld) {
// ~ return array("Expr" =>
// '!ereg("^[A-Za-z0-9_\.-\+]+@([A-Za-z0-9_\-])+(\.([A-Za-z0-9_\-])+)+$", ' .
// $pFld . ')', 'ErrStr' => 'CKEMAILADDR');
// ~ }

function CKEMAILADDR($pFld) {
	return array(
		"Expr" => '!preg_match("/^[A-Za-z0-9_\.-]+@([A-Za-z0-9_\.-])+\.[A-Za-z]{2,6}$/", ' . $pFld . ')',
		'ErrStr' => 'CKEMAILADDR'
	);
}

function CKPASSWORD($pFld) {
	return array(
		'Expr' => 'preg_match("/[^A-Za-z0-9\_\.\-\|\~\!\@\#\$\%\^\&\*\(\)\+\=\\\[\]\;\:\,\/\<\>\?]/", ' . $pFld . ')',
		'ErrStr' => 'CKPASSWORD'
	);
}

function CKUSERNAME($pFld) {
	return array(
		'Expr' => 'preg_match("/[^A-Za-z0-9\_\.\-\|]/", ' . $pFld . ')',
		'ErrStr' => 'CKUSERNAME'
	);
}


function checkIfFormSelectRowIsSelected($pIsSelected){
	if($pIsSelected){
		return ' selected="selected" ';
	}
}
function checkIfFormCheckboxRowIsSelected($pIsSelected){
	if($pIsSelected){
		return ' checked="checked" ';
	}
}

?>