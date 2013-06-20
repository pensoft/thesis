<?php

global $user;

$gFormFieldsArr = array(

	'profile.step1' => array(
		'userid' => array(
				'CType' => 'hidden',
				'VType' => 'int',
				'AllowNulls' => true,
				'AddTags' => array('id'  => 'userid',),
		),
		'editprofile' => array(
				'CType' => 'hidden',
				'VType' => 'int',
				'AllowNulls' => true,
				'DefValue' => 0,
		),
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
				'valid_js' => "removeErrorClass('P-RegFld-Email', 'loginErrors', 'null', 'P-Input-Full-Width')",
				'Checks' => array(
					'checkEmailAddr({email})',
				),
				'AddTags' => array('id'  => 'P-RegFld-Email',),
		),
		'password' => array(
				'CType' => 'password',
				'VType' => 'string',
				'DisplayName' => getstr('regprof.upass1'),
				'AllowNulls' => false,
				'error_class' => 'aaa', // fld_error_notify //optional
				'error_templ' => 'div', //optional
				'check_event' => 'blur', // change event won't fire when programmatically changing input value
				'req_js' => ' ',
				'error_js' => "addErrorClassById('P-RegFld-Pass', 'loginErrors', 'null', 'P-Input-Full-Width')",
				'valid_js' => "removeErrorClass('P-RegFld-Pass', 'loginErrors', 'null', 'P-Input-Full-Width')",
				'AddTags' => array('id'  => 'P-RegFld-Pass',),
		),
		'password2' => array(
				'CType' => 'password',
				'VType' => 'string',
				'AllowNulls' => false,
				'DisplayName' => getstr('regprof.upass2'),
				'error_class' => 'aaa',
				'error_templ' => 'div',
				'check_event' => 'blur',
				'req_js' => ' ',
				'error_js' => "addErrorClassById('P-RegFld-Pass2', 'loginErrors', 'null', 'P-Input-Full-Width')",
				'valid_js' => "removeErrorClass('P-RegFld-Pass2', 'loginErrors', 'null', 'P-Input-Full-Width')",
				'AddTags' => array('id'  => 'P-RegFld-Pass2', ),
		),
		'register' => array(
				'CType' => 'action',
				'SQL' => 'SELECT u.userid, u.email FROM spRegUsrStep1({userid}, 1, {email}, {password}) u',
				'AddTags' => array(),
				'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		),
		'showedit' => array(
				'CType' => 'action',
				'SQL' => 'SELECT * FROM GetUserDataStep1({userid})',
				'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		)
	),

	'profile.step2' => array(
		'success' => array(
			'CType' => 'hidden',
			'VType' => 'int',
			'AllowNulls' => true,
		),
		'userid' => array(
			'CType' => 'hidden',
			'VType' => 'int',
			'AddTags' => array('id'  => 'userid',),
		),
		'editprofile' => array(
			'CType' => 'hidden',
			'VType' => 'int',
			'AllowNulls' => true,
			'DefValue' => 0,
		),
		'photoid' => array(
			'CType' => 'hidden',
			'VType' => 'int',
			'AddTags' => array('id'  => 'photoid',),
		),
		'regstep' => array(
			'CType' => 'hidden',
			'VType' => 'int',
			'AddTags' => array('id'  => 'regstep',),
		),
		'firstname' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => false,
			'DisplayName' => getstr('regprof.firstname'),
			'error_class' => 'aaa',
			'error_templ' => 'div',
			'check_event' => 'blur',
			'req_js' => ' ',
			'error_js' => "addErrorClassById('P-RegFld-Fname', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'valid_js' => "removeErrorClass('P-RegFld-Fname', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'AddTags' => array('id'  => 'P-RegFld-Fname',),

		),
		'middlename' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => true,
			'DisplayName' => getstr('regprof.middlename'),
		),
		'lastname' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => false,
			'DisplayName' => getstr('regprof.lastname'),
			'error_class' => 'aaa',
			'error_templ' => 'div',
			'check_event' => 'blur',
			'req_js' => ' ',
			'error_js' => "addErrorClassById('P-RegFld-Lname', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'valid_js' => "removeErrorClass('P-RegFld-Lname', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'AddTags' => array('id'  => 'P-RegFld-Lname', ),
		),
		'usrtitle' => array(
			'VType' => 'int' ,
			'CType' => 'select' ,
			'DisplayName' => getstr('regprof.usrtitle'),
			'SrcValues' => 'SELECT id as id, name as name FROM usr_titles',
			'error_class' => 'aaa',
			'error_templ' => 'div',
			'check_event' => 'blur',
			'req_js' => ' ',
			'error_js' => "addErrorClassById('P-RegFld-UsrTitle', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'valid_js' => "removeErrorClass('P-RegFld-UsrTitle', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'AddTags' => array('id'  => 'P-RegFld-UsrTitle', ),
			'AllowNulls' => false,
		),
		'clienttype' => array(
			'CType' => 'radio',
			'VType' => 'int',
			'DisplayName' => getstr('regprof.usrtype'),
			'SrcValues' => 'SELECT id as id, name as name, (CASE WHEN id = 9 THEN 0 ELSE 1 END) as ord FROM client_types ORDER BY ord ASC, id ASC',
			'TransType' => MANY_TO_BIT,
			'error_class' => 'aaa',
			'error_templ' => 'div',
			'check_event' => 'blur',
			'req_js' => ' ',
			'error_js' => "addErrorClassById('P-RegFld-ClientType', 'loginErrors', 'null', 'P-Right-Col-Fields-ColRadios')",
			'valid_js' => "removeErrorClass('P-RegFld-ClientType', 'loginErrors', 'null', 'P-Right-Col-Fields-ColRadios')",
			'AllowNulls' => false,
			'AddTags' => array(
				'class' => 'radioInput',
				//~ 'id' => 'P-RegFld-ClientType',
			),
		),
		'affiliation' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => false,
			'DisplayName' => getstr('regprof.affiliation'),
			'error_class' => 'aaa',
			'error_templ' => 'div',
			'check_event' => 'blur',
			'req_js' => ' ',
			'error_js' => "addErrorClassById('P-RegFld-Aff', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'valid_js' => "removeErrorClass('P-RegFld-Aff', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'AddTags' => array('id'  => 'P-RegFld-Aff',	),
		),
		'department' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => true,
			'DisplayName' => getstr('regprof.department'),
		),
		'addrstreet' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => false,
			'DisplayName' => getstr('regprof.addrstreet'),
			'error_class' => 'aaa',
			'error_templ' => 'div',
			'check_event' => 'blur',
			'req_js' => ' ',
			'error_js' => "addErrorClassById('P-RegFld-Addr', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'valid_js' => "removeErrorClass('P-RegFld-Addr', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'AddTags' => array('id'  => 'P-RegFld-Addr', ),
		),
		'postalcode' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => false,
			'DisplayName' => getstr('regprof.postalcode'),
			'error_class' => 'aaa',
			'error_templ' => 'div',
			'check_event' => 'blur',
			'req_js' => ' ',
			'error_js' => "addErrorClassById('P-RegFld-Pcode', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'valid_js' => "removeErrorClass('P-RegFld-Pcode', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'AddTags' => array('id'  => 'P-RegFld-Pcode', ),
		),
		'city' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => false,
			'DisplayName' => getstr('regprof.city'),
			'error_class' => 'aaa',
			'error_templ' => 'div',
			'check_event' => 'blur',
			'req_js' => ' ',
			'error_js' => "addErrorClassById('P-RegFld-City', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'valid_js' => "removeErrorClass('P-RegFld-City', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'AddTags' => array('id'  => 'P-RegFld-City', ),
		),
		'country' => array (
			'VType' => 'int' ,
			'CType' => 'select' ,
			'DisplayName' => getstr('regprof.country'),
			'SrcValues' => 'SELECT a.id, a.name, class
									FROM (SELECT left(name, 1)::varchar as id, left(name, 1) as name, \'disabled\' as class
											FROM countries
											GROUP BY left(name, 1)
											UNION
											SELECT id::varchar as id, (\'&nbsp;\' || name) as name, \'\' as class
											FROM countries
									) as a
									ORDER BY CASE WHEN char_length(name) > 6 THEN substring(name from 6) ELSE name END;',
			'error_class' => 'aaa',
			'error_templ' => 'div',
			'check_event' => 'blur',
			'req_js' => ' ',
			'error_js' => "addErrorClassById('P-RegFld-Country', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'valid_js' => "removeErrorClass('P-RegFld-Country', 'loginErrors', 'null', 'P-Input-Full-Width')",
			'AddTags' => array('id'  => 'P-RegFld-Country', ),
			'AllowNulls' => false,
		),
		'phone' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => true,
			'DisplayName' => getstr('regprof.phone'),
		),
		'fax' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => true,
			'DisplayName' => getstr('regprof.fax'),
		),
		'vatnumber' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => true,
			'DisplayName' => getstr('regprof.vatnumber'),
		),
		'website' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => true,
			'DisplayName' => getstr('regprof.website'),
		),
		'register' => array(
			'CType' => 'action',
			'SQL' => 'SELECT * FROM spRegUsrStep2({userid}, 1, {firstname}, {middlename}, {lastname}, {usrtitle}, {clienttype}, {affiliation}, {department}, {addrstreet}, {postalcode}, {city}, {country}, {phone}, {fax}, {vatnumber}, {website}) as userid',
			'ActionMask' =>  ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
			),
		'showedit' => array(
			'CType' => 'action',
			'SQL' => 'SELECT * FROM GetUserDataStep2({userid})',
			'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		)
	),

	'profile.step3' => array(
		'userid' => array(
				'CType' => 'hidden',
				'VType' => 'int',
				'AllowNulls' => true,
				'AddTags' => array('id'  => 'userid', ),
		),
		'editprofile' => array(
			'CType' => 'hidden',
			'VType' => 'int',
			'AllowNulls' => true,
			'DefValue' => 0,
		),
		'regstep' => array(
			'CType' => 'hidden',
			'VType' => 'int',
			'DisplayName' => getstr('regprof.regstep'),
			'AddTags' => array(
				'id'  => 'regstep',
			),
		),
		'producttypes' => array(
			'VType' => 'int',
			'CType' => 'checkbox',
			'DefValue' => 0,
			'AllowNulls' => false,
			'DisplayName' => getstr('regprof.producttypes'),
			'TransType' => MANY_TO_SQL_ARRAY,
			'SrcValues' => 'SELECT id as id, name as name FROM product_types',
			'AddTags' => array('class' => 'producttypes', ),
		),
		'alertsfreq' => array(
			'CType' => 'radio',
			'VType' => 'int',
			'DisplayName' => getstr('regprof.alertsfreq'),
			'SrcValues' => 'SELECT id as id, name as name FROM usr_alerts_frequency',
			'TransType' => MANY_TO_BIT,
			'AllowNulls' => false,
		),
		'alerts_subject_cats' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => true,
			'DisplayName' => getstr('regprof.alerts_subject_cats'),
			'AddTags' => array('id' => 'alerts_subject_cats_autocomplete', ),
		),
		'alerts_chronical_cats' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => true,
			'DisplayName' => getstr('regprof.alerts_chronical_cats'),
			'AddTags' => array(
				'id' => 'alerts_chronical_cats_autocomplete',  /* тук задължително трябва да има _autocomplete, защото
																* на класа се подава alerts_chronical_cats като идентификатор
																* и той сам добавя _autocomplete към идентификатора
																*/

			),
		),
		'alerts_taxon_cats' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => true,
			'DisplayName' => getstr('regprof.alerts_taxon_cats'),
			'AddTags' => array('id' => 'alerts_taxon_cats_autocomplete', ),
		),
		'alerts_geographical_cats' => array(
			'CType' => 'text',
			'VType' => 'string',
			'AllowNulls' => true,
			'DisplayName' => getstr('regprof.alerts_geographical_cats'),
			'AddTags' => array( 'id' => 'alerts_geographical_cats_autocomplete', ),
		),
		'journals' => array(
			'VType' => 'int',
			'CType' => 'checkbox',
			'DefValue' => 0,
			'AllowNulls' => true,
			'DisplayName' => getstr('regprof.journals'),
			'TransType' => MANY_TO_SQL_ARRAY,
			'SrcValues' => 'SELECT id as id, name as name FROM journals',
			'AddTags' => array(
				//~ 'class' => 'scompchk',
			),
		),
		'register' => array(
			'CType' => 'action',
			'SQL' => 'SELECT * FROM spRegUsrStep3(' . ($user->id ? $user->id : ( $_SESSION['tmpusrid'] ? $_SESSION['tmpusrid'] : null )) . ', \'' . ($user->uname ? $user->uname : q($_SESSION['usermail'])) . '\'::varchar, ' . ($user->id ? '2' : '1') . ', {producttypes}, {alertsfreq}, {alerts_subject_cats}, {alerts_chronical_cats}, {alerts_taxon_cats}, {alerts_geographical_cats}, {journals}) as userid',
			'ActionMask' =>  ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		),
		'showedit' => array(
			'CType' => 'action',
			'SQL' => 'SELECT * FROM GetUserDataStep3({userid})',
			'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
		)
	),
);

?>