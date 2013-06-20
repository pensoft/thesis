<?php
$gDontRedirectToLogin = 1; 
//~ $docroot = getenv('DOCUMENT_ROOT');
//~ require_once($docroot . '/lib/static.php');

$lCookieName = "h_cookie";    // Cookie's name
$lCookieValue = $_GET['autologin_hash'];
$lDaysCookieShallLast = 31;
$lCookieDirectory = "/";


$lLogOutFlag = (int)$_GET['logout'];

// CLEAR COOKIE
if((int)$lLogOutFlag) {
	$lCookieValue = "";
	$lDaysCookieShallLast = 0;
	
}

//~ $lCookieDomain = '.'.preg_replace('/^www\./','',$_SERVER{'HTTP_HOST'});
//~ $lCookieDomain = preg_replace('/:\d+$/','',$CookieDomain);

$lCookieDomain = $_SERVER['SERVER_NAME'];
//~ $lasting = ($lDaysCookieShallLast <= 0) ? (time() - 3600) : time()+($lDaysCookieShallLast*24*60*60);
$lasting = 0; //expire at the end of the session (when the browser closes). 

if((int)$lLogOutFlag) {
	setcookie($lCookieName, $lCookieValue, $lasting, $lCookieDirectory, $lCookieDomain, 1);
} else {
	setcookie($lCookieName, $lCookieValue, $lasting, $lCookieDirectory, $lCookieDomain);
}

$image = "R0lGODlhBQAFAJH/AP///wAAAMDAwAAAACH5BAEAAAIALAAAAAAFAAUAAAIElI+pWAA7\n";
header('Content-type: image/gif');
echo base64_decode($image);
exit;

?>