<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

if ((int)$user->id) {
	header('Location: /');
	exit;
}

$success = (int)$_GET['success'];

if (!$success) {
	$reg = new ctplkfor(
		array(
			'ctype' => 'ctplkfor',
			'method' => 'POST" id="registerfrm',
			'setformname' => 'registerfrm',
			'flds' => array(
				'uname' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('regprof.username'),
					'AllowNulls' => false,
					'AddTags' => array(
						'class' => 'calcinp',
					),
				),

				'upass1' => array(
					'CType' => 'password',
					'VType' => 'string',
					'DisplayName' => getstr('regprof.upass1'),
					'AllowNulls' => false,
					'AddTags' => array(
						'class' => 'calcinp',
					),
				),
				
				'upass2' => array(
					'CType' => 'password',
					'VType' => 'string',
					'DisplayName' => getstr('regprof.upass2'),
					'AllowNulls' => false,
					'AddTags' => array(
						'class' => 'calcinp',
					),
				),
				
				'name' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('regprof.fname'),
					'AllowNulls' => false,
					'AddTags' => array(
						'class' => 'calcinp',
					),
				),
				
				'email' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('regprof.email'),
					'AllowNulls' => false,
					'AddTags' => array(
						'class' => 'calcinp',
					),
				),
				
				'phone' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('regprof.phone'),
					'AllowNulls' => false,
					'AddTags' => array(
						'class' => 'calcinp',
					),
				),
				
				'register' => array(
					'CType' => 'action',
					'DisplayName' => getstr('regprof.registerbut'),

					'SQL' => 'SELECT * FROM sp_regprof(1, null, {uname}, {upass1}, 1, {name}, {email}, {phone})/*{upass2}*/',
					'AddTags' => array(
						'class' => 'coolbut',
					),
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
			),
			'templs' => array(
				G_DEFAULT => 'regprof.registerfrm',
			),
		)
	);

	if ($reg->KforAction() == 'register') {
		if ($reg->getKforVal('upass1') != $reg->getKforVal('upass2')) {
			$reg->KforSetErr('upass2', getstr('regprof.pass_not_match'));
		}
		
		if (!CheckMail($reg->getKforVal('email'))) {
			$reg->KforSetErr('email', getstr('regprof.email_not_valid'));
		}
		
		if (in_array(strtolower($_POST['captcha']), $_SESSION['frmcapt'])) {
			foreach ($_SESSION['frmcapt'] as $captkey => $captval) {
				if ($captval == strtolower($_POST['captcha'])) unset($_SESSION['frmcapt'][$captkey]);
			}
		} else {
			$reg->KforSetErr(getstr('regprof.captchacode'), getstr('regprof.captchaerr'));
		}
	}
	
	$reg->GetData();

	if ($reg->KforAction() == 'register' && $reg->KforErrCnt() == 0) {
		
		$confhash = GetConfHash($reg->getKforVal('uname'), $reg->getKforVal('email'));
		
		$mespubdata = array(
			'confhash' => $confhash,
			'siteurl' => SITE_URL,
			'mailsubject' => MAILSUBJ_REGISTER,
			'mailto' => $reg->getKforVal('email'),
			'charset' => 'UTF-8',
			'boundary' => '--_separator==_',
			'from' => array(
				'display' => MAIL_DISPLAY,
				'email' => MAIL_ADDR,
			),
			'templs' => array(
				G_DEFAULT => 'regprof.mailcontent',
			),
		);
		
		$cn = Con();
		$cn->Execute('SELECT * FROM SetConfHash (\'' . q($reg->getKforVal('uname')) . '\', \'' . q($reg->getKforVal('email')) . '\', \'' . q($confhash) . '\')');
		
		$msg = new cmessaging($mespubdata);
		$msg->Display();
		
		header('Location: /register.php?success=1');
		exit;
	} else {
		$reg->setKforVal('upass1', '');
		$reg->setKforVal('upass2', '');
	}
	
	$regsimple = $reg->Display();
	
} else {
	$regsimple = new csimple(
		array(
			'ctype' => 'csimple',
			'msg' => getstr('regprof.registrationSuccess'),
			'templs' => array(
				G_DEFAULT => 'global.system_msg',
			),
		)
	);
}

$t = array(
	'metatitle' => 'Register',
	'contents' => $regsimple,
);

$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.simplepage'));
$inst->Display();
?>