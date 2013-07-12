<?php
$gDontRedirectToLogin = 1; 
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
global $COOKIE_DOMAIN;

$gFormFields =  array(
	'logins_templ_fields' => array(
		'back_uri' => array(
			'CType' => 'hidden',
			'VType' => 'string',
			'DisplayName' => 'back_uri',
			'AllowNulls' => true,
		),
		'username' => array(
			'CType' => 'text',
			'VType' => 'string',
			'DisplayName' => getstr('loginform.username'),
			'AllowNulls' => false,
			'AddTags' => array(
				'onfocus' => 'changeFocus(1, this);',
				'onblur'  => 'changeFocus(2, this);',
				'class'   => 'P-Input',
				//'fldattr'  => '0',
				'id' => 'P-Login-Username',
				'tabindex'  => '1'
			),
			'error_class' => 'aaa', // fld_error_notify //optional
			'error_templ' => 'div', //optional
			'check_event' => 'blur', // change event won't fire when programmatically changing input value 
			'req_js' => ' ',
			'error_js' => 'addErrorClassById(\'P-Login-Username\', \'loginErrors\', \'null\', \'P-Input-Inner-Wrapper\')',
			'valid_js' => 'removeErrorClass(\'P-Login-Username\', \'loginErrors\', \'null\', \'P-Input-Inner-Wrapper\')',
			'Checks' => array( 
				'checkEmailAddr({username})',
			),
		),
		'password' => array(
			'CType' => 'password',
			'VType' => 'string',
			'DisplayName' => getstr('loginform.password'),
			'AllowNulls' => false,
			'error_class' => 'aaa', // fld_error_notify //optional
			'error_templ' => 'div', //optional
			'check_event' => 'blur', // change event won't fire when programmatically changing input value 
			'req_js' => ' ',
			'error_js' => 'addErrorClassById(\'P-Login-Password\', \'loginErrors\', \'null\', \'P-Input-Inner-Wrapper\')',
			'valid_js' => 'removeErrorClass(\'P-Login-Password\', \'loginErrors\', \'null\', \'P-Input-Inner-Wrapper\')',
			'AddTags' => array(
				'onfocus' => 'changeFocus(1, this);',
				'onblur'  => 'changeFocus(2, this);',
				//'fldattr'  => '0',
				'id' => 'P-Login-Password',
				'tabindex'  => '2'
			),
		),
		// <input value="LOGIN" type="submit" class=""/>
		'login' => array(
			'CType' => 'action',
			'DisplayName' => getstr('loginform.loginbut'),
			'SQL' => '{username}, {password}, {back_uri}',
			'AddTags' => array(
				'tabindex'  => '3',
			),
			'ActionMask' => ACTION_CHECK   | ACTION_SHOW,
		),
		'confirmedtxt' => $Dummy,
	),
	'loginfpass_templ_fields' => array(
		'email' => array(
			'CType' => 'text',
			'VType' => 'string',
			'DisplayName' => getstr('regprof.email'),
			'AllowNulls' => false,
			'error_class' => 'aaa', // fld_error_notify //optional
			'error_templ' => 'div', //optional
			'check_event' => 'blur', // change event won't fire when programmatically changing input value 
			'req_js' => ' ',
			'error_js' => 'addErrorClassById(\'P-RegFld-Email\', \'loginErrors\', \'null\', \'P-Input-Full-Width\')',
			'valid_js' => 'removeErrorClass(\'P-RegFld-Email\', \'loginErrors\', \'null\', \'P-Input-Full-Width\')',
			'Checks' => array(
				'checkEmailAddr({email})',
			),
			'AddTags' => array(
				'onfocus' => 'changeFocus(1, this)',
				'onblur'  => 'changeFocus(2, this)',
				'fldattr'  => '0',
				'id'  => 'P-RegFld-Email',
			),
		),
		'captcha' => array(
			'CType' => 'text',
			'VType' => 'string',
			'DisplayName' => getstr('regprof.captcha'),
			'AllowNulls' => false,
			'error_class' => 'aaa', // fld_error_notify //optional
			'error_templ' => 'div', //optional
			'check_event' => 'blur', // change event won't fire when programmatically changing input value 
			'req_js' => ' ',
			'error_js' => 'addErrorClassById(\'captcha\', \'loginErrors\', \'null\', \'P-Input-Full-Width\')',
			'valid_js' => 'removeErrorClass(\'captcha\', \'loginErrors\', \'null\', \'P-Input-Full-Width\')',
			'Checks' => array(
				'CheckCaptcha({captcha})',
			),
			'AddTags' => array(
				'onfocus' => 'changeFocus(1, this)',
				'onblur'  => 'changeFocus(2, this)',
				'fldattr'  => '0',
				'id'  => 'captcha',
			),
		),
		'send' => array(
			'CType' => 'action',
			'DisplayName' => 'Send',
			'SQL' => '{email}, {captcha}',
			'AddTags' => array(
				'class' => 'coolbut',
			),
			'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW,
		),
	)
);

?>