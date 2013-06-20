<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
require_once($docroot . '/login_flds.php');

if ((int)$user->id) {
	header('Location: /');
	exit;
}

$ans = (int)$_REQUEST['ans'];
if (!in_array($ans, array(1,2))) {
	$fpass = new ctplkfor(
		array(
			'ctype' => 'ctplkfor',
			'method' => 'POST',
			'setformname' => 'fpassfrm',
			'flds' => $gFormFields['loginfpass_templ_fields'],
			'js_validation' => JS_VALIDATION_ON,
			'path_fields' => array (
				'path' => '/login_flds.php',
				'key' => 'loginfpass_templ_fields',
			),
			'templs' => array(
				G_DEFAULT => 'loginform.fpassform',
			),
		)
	);
	if ($fpass->KforAction() == 'send') {
		//~ if ($fpass->getKforVal('email') && !CheckMail($fpass->getKforVal('email'))) {
			//~ $fpass->KforSetErr('email', getstr('regprof.email_notvalid'));
		//~ }
		if(q($fpass->getKforVal('email')) == q(getstr('regprof.email'))){
			$fpass->KforSetErr(getstr('regprof.email'), getstr('regprof.errnodata'));
		} elseif (!CheckMail($fpass->getKforVal('email'))) {
			$fpass->KforSetErr('email', getstr('regprof.email_not_valid'));
		}
		
		//~ if (in_array(strtolower($_POST['captcha']), $_SESSION['frmcapt'])) {
			//~ foreach ($_SESSION['frmcapt'] as $captkey => $captval) {
				//~ if ($captval == strtolower($_POST['captcha'])) unset($_SESSION['frmcapt'][$captkey]);
			//~ }
		//~ } else {
			//~ $fpass->KforSetErr(getstr('regprof.captchacode'), getstr('regprof.captchaerr'));
		//~ }
	}
	
	$fpass->GetData();	
	//~ $fpasssimple = $fpass->Display();
	if ($fpass->KforAction() == 'send' && $fpass->KforErrCnt() == 0) {
		if (in_array(strtolower($_POST['captcha']), $_SESSION['frmcapt'])) {
			foreach ($_SESSION['frmcapt'] as $captkey => $captval) {
				if ($captval == strtolower($_POST['captcha'])) unset($_SESSION['frmcapt'][$captkey]);
			}
		}
		$cn = Con();
		
		$sql = $fpass->KforSql('SELECT * FROM spUserFpass({email}, null)');
		$cn->Execute($sql);
		$cn->MoveFirst();
		
		if (!$cn->mRs['uname']) {
			header('Location: /fpass.php?ans=2&email='.$fpass->kfor->lFieldArr['email']['CurValue']);
			exit;
		}
		
		/* UPDATE NA PASS V BAZATA NA PENSOFT */
		$lCon = new DbCn(MYSQL_DBTYPE);
		$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
		$lCon->Execute('UPDATE CLIENTS SET PASS = \'' . q($cn->mRs['upass']) . '\' WHERE email LIKE \'' . $cn->mRs['uname'] . '\' AND CID = ' . (int)$cn->mRs['oldpjs_cid']);
		$lCon->MoveFirst();
		$lCon->Close();
		/* UPDATE NA PASS V BAZATA NA PENSOFT */
		
		
		$mespubdata = array(
			'uname' => $cn->mRs['uname'],
			'pass' => $cn->mRs['upass'],
			'fullname' => $cn->mRs['fullname'],
			'requestdate' => date('d/m/Y H:i'),
			'mailsubject' => PENSOFT_MAILSUBJ_FPASS,
			'mailto' => $fpass->getKforVal('email'),
			'charset' => 'UTF-8',
			'boundary' => '--_separator==_',
			'from' => array(
				'display' => PENSOFT_MAIL_DISPLAY,
				'email' => PENSOFT_MAIL_ADDR,
			),
			'templs' => array(
				G_DEFAULT => 'loginform.fpassmail',
			),
		);
		
		
		$cn->Close();
		$msg = new cmessaging($mespubdata);
		$msg->Display();
		
		header('Location: /fpass.php?ans=1');
		exit;
	} 
	$c = new csimple(array(
		'ctype' => 'csimple',
		'templs' => array( G_DEFAULT => 'loginform.loginformwrapper') ,
		'form' => $fpass->Display(),
	));
} else {
	$c = new csimple(array(
		'ctype' => 'csimple',
		'templs' => array( G_DEFAULT => 'loginform.loginformwrapperfpasssucess'),
		'msg' => getstr(($ans == 1 ? 'loginform.fpass_success' : 'loginform.fpass_failed')),
		'email' => $_REQUEST['email'],
	));
}

$t = array(
	'contents' => $c,
);

$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.loginpage'));
$inst->Display();

?>