<?php


global $user;

$gFormFieldsArr = array(
	'loginform.fpass' => array(
		'email' => array(
			'CType' => 'text',
			'VType' => 'string',
			'DisplayName' => getstr('regprof.email'),
			'AllowNulls' => false,
			'error_class' => 'aaa', // fld_error_notify //optional
			'error_templ' => 'div', //optional
			'check_event' => 'blur', // change event won't fire when programmatically changing input value 
			'req_js' => ' ',
			'error_js' => "addErrorClassById('P-RegFld-Email', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'valid_js' =>  "removeErrorClass('P-RegFld-Email', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'AddTags' => array('id'  => 'P-RegFld-Email', ),
		),
		'send' => array(
			'CType' => 'action',
			'DisplayName' => 'Send',
			'SQL' => '{email}',
			'AddTags' => array(
				'class' => 'coolbut',
			),
			'CheckCaptcha' => 1,
			'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW,
		),
	)
);


?>