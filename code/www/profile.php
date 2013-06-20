<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

if (!(int)$user->id) {
	header("Location: /");
	exit;
}

$success = (int)$_GET['success'];

if (!$success) {
	$reg = new ctplkfor(
		array(
			'ctype' => 'ctplkfor',
			'method' => 'POST',
			'flds' => array(
				'upass1' => array(
					'CType' => 'password',
					'VType' => 'string',
					'DisplayName' => getstr('regprof.upass1'),
					'AllowNulls' => true,
					'AddTags' => array(
						'class' => 'calcinp',
					),
				),
				
				'upass2' => array(
					'CType' => 'password',
					'VType' => 'string',
					'DisplayName' => getstr('regprof.upass2'),
					'AllowNulls' => true,
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
					'AllowNulls' => true,
					'AddTags' => array(
						'class' => 'calcinp',
					),
				),
				
				'new' => array(
					'CType' => 'action',
					'Hidden' => true,
					'SQL' => 'SELECT * FROM sp_regprof(0, ' . (int)$user->id . ', null, null, null, null, null, null)',
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
				
				'save' => array(
					'CType' => 'action',
					'DisplayName' => getstr('regprof.savebut'),
					'SQL' => 'SELECT * FROM sp_regprof(1, ' . (int)$user->id . ', null, {upass1}, 1, {name}, {email}, {phone})/*{upass2}*/',
					'AddTags' => array(
						'class' => 'coolbut',
					),
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
			),
			'templs' => array(
				G_DEFAULT => 'regprof.profilefrm',
			),
		)
	);
	
	if ($reg->KforAction() == 'save') {
		if ($reg->getKforVal('upass1')) {
			$reg->setProp('upass2', 'AllowNulls', false);
			
			if ($reg->getKforVal('upass1') != $reg->getKforVal('upass2')) {
				$reg->KforSetErr('upass2', 'Passwords does not match!');
			}
		}
	}
	
	$reg->GetData();
	
	if ($reg->KforAction() == 'save' && $reg->KforErrCnt() == 0) {
		header('Location: /profile.php?success=1');
		exit;
	} else {
		$reg->setKforVal('upass', '');
		$reg->setKforVal('upass2', '');
	}
	
	$regsimple = $reg->Display();
	
} else {
	$regsimple =  new csimple(
		array(
			'ctype' => 'csimple',
			'msg' => getstr('regprof.profileSuccess'),
			'templs' => array(
				G_DEFAULT => 'global.system_msg',
			),
		)
	);
}

$t = array (
	'metatitle' => 'Profile',
	'contents' => $regsimple,
);


$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.simplepage'));
$inst->Display();

?>