<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
require_once($docroot . '/login_flds.php');
global $COOKIE_DOMAIN;

$lFlds = $gFormFields['logins_templ_fields'];

if(isset($_REQUEST['back_uri']) && $_REQUEST['back_uri'] != ''){
	$lUrl = $_REQUEST['back_uri'];
	$lUrl = urldecode($lUrl);
}else{
	$lUrl = '/index.php';
}

if(isset($_REQUEST['u_autolog_hash'])){
	$lAutologHash = $_REQUEST['u_autolog_hash'];

	$lParsedUrl = parse_url($lUrl);
	$lNewUrlParameters = rm_url_param('u_autolog_hash', $lParsedUrl['query']);
	$lUrl = $lParsedUrl['path'] . '?' . $lNewUrlParameters;

	$lParam['autologhash'] = $lAutologHash;
	$lParam['ip'] = $_SERVER['REMOTE_ADDR'];

	$user = new clogin($lParam);
	$_SESSION['suser'] = serialize($user);
	if ($user->state == 1) {
		header('Location: /set_pjs_cookie.php?redirurl=' . $lUrl );
		//~ header("Location: $lUrl");
		exit;
	}
}

if ((int)$_REQUEST['logout']) {
	unset($_SESSION['suser']);
	setcookie('rememberme', '', mktime(), '/', $COOKIE_DOMAIN);
	if(isset($_COOKIE['h_cookie'])) {
		setcookie('h_cookie', '', time() - 3600, '/', $COOKIE_DOMAIN);
	}
	$lAutologinCookieUrl = '/set_pjs_cookie.php?logout=1&redirurl=' . $lUrl;
	header("Location: $lAutologinCookieUrl");
	exit();
}

if ($user->id) {
	header("Location: $lUrl");
	exit();
}

function returTxt( $pKforCurVals ){
	return $pKforCurVals['confirmedtxt'];
}

$reg = new ctplkfor(
	array(
		'ctype' => 'ctplkfor',
		'method' => 'POST" id="loginfrm',
		'setformname' => 'loginfrm',
		'flds' => $lFlds,
		//~ 'js_validation' => JS_VALIDATION_ON,
		'js_validation' => false,
		'path_fields' => array (
			'path' => '/login_flds.php',
			'key' => 'logins_templ_fields',
		),
		'templs' => array(
			G_DEFAULT => 'loginform.form',
		),
	)
);

if($_REQUEST['confirmed']) {
	$reg->setKforVal('confirmedtxt', '<div class="P-Reg-Confirm">Registration is confirmed.</div>');
}

if(CheckIfLoginFormCaptchaShouldBeDisplayed()){
	if($reg->KforAction() == 'login'){
		if (in_array(strtolower($_POST['captcha']), $_SESSION['frmcapt'])) {
			foreach ($_SESSION['frmcapt'] as $captkey => $captval) {
				if ($captval == strtolower($_POST['captcha'])) unset($_SESSION['frmcapt'][$captkey]);
			}
		} else {
			$reg->KforSetErr(getstr('contact.captchaname'), ERR_CAPTCHA_WRONG_CODE);
		}
	}
}

$reg->GetData();

if(($reg->KforAction() == 'login' || $reg->KforAction() == 'send') && $reg->KforErrCnt()){
	$reg->SetKforVal('errs', 1);
}
if ($reg->KforAction() == 'login' && $reg->KforErrCnt() == 0) {
	$lUsername = $reg->GetKforVal('username');
	$lPassword = $reg->GetKforVal('password');
	/*
	if ((int)$reg->GetKforVal('rememberme')) {
		$lExpire = mktime() + (24 * 3600 * 30);
		setcookie('rememberme', md5(strtolower($lUsername) . '-' . md5($lPassword)), $lExpire, '/', $COOKIE_DOMAIN);
	}
	*/
	$user = new clogin($lUsername, $lPassword, $_SERVER['REMOTE_ADDR']);
	$_SESSION['suser'] = serialize($user);
	if ($user->state == 1) {
		$_SESSION['wrong_login_attempts'] = 0;
		header('Location: /set_pjs_cookie.php?redirurl=' . $lUrl );
		//~ header("Location: $lUrl");
		exit;
	}
	if(!isset($_SESSION['wrong_login_attempts'])){
		$_SESSION['wrong_login_attempts'] = 0;
	}
	$_SESSION['wrong_login_attempts']++;
	$reg->KforSetErr('login', GetLoginErr());
}

if ($reg->KforAction() == 'login' && ($lUsername == '' || $lPassword == '') )
	$reg->KforSetErr('login', getstr('loginform.nosuchuser'));

//$reg->StopErrDisplay(1);
$lDefTempl = DefObjTempl();


$c = new csimple(array(
	'ctype' => 'csimple',
	'templs' => array( G_DEFAULT => 'loginform.loginformwrapper' ) ,
	'form' => $reg->Display(),
	'errorholderclass' => ($reg->KforErrCnt() == 0 ) ? '' : 'loginErrors',
));

$t = array(
	'contents' => $c,
);

$inst = new cpage(array_merge($t, $lDefTempl), array(G_MAINBODY => 'global.loginpage'));
$inst->Display();

?>