<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

if ((int)$user->id) {
	header('Location: /');
	exit;
}

$ans = (int)$_GET['ans'];
if (!in_array($ans, array(1,2))) {
	
	$fpass = new ctplkfor(
		array(
			'ctype' => 'ctplkfor',
			'method' => 'POST',
			'setformname' => 'fpassfrm',
			'flds' => array(
				'email' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('regprof.email'),
					'AddTags' => array(
						'class' => 'calcinp',
					),
				),
				'send' => array(
					'CType' => 'action',
					'DisplayName' => getstr('regprof.fpassbut'),
					'SQL' => '{email}',
					'AddTags' => array(
						'class' => 'coolbut',
					),
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW,
				),
			),
			'templs' => array(
				G_DEFAULT => 'regprof.fpassform',
			),
		)
	);
	
	if ($fpass->KforAction() == 'send') {
		if ($fpass->getKforVal('email') && !CheckMail($fpass->getKforVal('email'))) {
			$fpass->KforSetErr('email', 'Email address is not valid!');
		}
	}
	
	$fpass->GetData();	
	$fpasssimple = $fpass->Display();
	
	if ($fpass->KforAction() == 'send' && $fpass->KforErrCnt() == 0) {
		
		$cn = Con();
		
		$sql = $fpass->KforSql('SELECT * FROM UserFpass({email})');
		$cn->Execute($sql);
		$cn->MoveFirst();
		
		if (!$cn->mRs['uname']) {
			header('Location: /fpass.php?ans=2');
			exit;
		}
		
		$mespubdata = array(
			'uname' => $cn->mRs['uname'],
			'pass' => $cn->mRs['pass'],
			'requestdate' => date('d/m/Y H:i'),
			'mailsubject' => MAILSUBJ_FPASS,
			'mailto' => $fpass->getKforVal('email'),
			'charset' => 'UTF-8',
			'boundary' => '--_separator==_',
			'from' => array(
				'display' => MAIL_DISPLAY,
				'email' => MAIL_ADDR,
			),
			'templs' => array(
				G_DEFAULT => 'regprof.fpassmail',
			),
		);
		
		$msg = new cmessaging($mespubdata);
		$msg->Display();
		
		header('Location: /fpass.php?ans=1');
		exit;
	} 
	
} else {
	$fpasssimple = new csimple(
		array(
			'ctype' => 'csimple',
			'msg' => getstr(($ans == 1 ? 'regprof.fpass_success' : 'regprof.fpass_failed')),
			'templs' => array(
				G_DEFAULT => 'global.system_msg',
			),
		)
	);
}

$t = array (
	'metatitle' => 'Password recovery',
	'contents' => $fpasssimple,
);

$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.simplepage'));
$inst->Display();

?>