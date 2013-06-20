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

?>