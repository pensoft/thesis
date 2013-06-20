<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
require_once($docroot . '/registration_flds.php');
//~ unset($_SESSION['regstep']);
//~ unset($_SESSION['tmpusrid']);
if(!isset($_SESSION['regstep']) || !in_array($_SESSION['regstep'], array(1,2,3)))
	$_SESSION['regstep'] = 1;


if (isset($_REQUEST['step'])) {
	$lStep = (int)$_REQUEST['step'];
	if(!in_array($lStep, array(1,2,3))){
		$lStep = 1;
	}
	$_SESSION['regstep'] = (int)$lStep;
	$lRegstep = $_SESSION['regstep'];
	if($user->id)
		unset($_SESSION['regstep']);
}

$lSuccess = (int) $_REQUEST['success'];
$lShowEdit = (int) $_REQUEST['showedit'];

if ($user->id && !$lShowEdit) {
	//~ header('Location: /index.php');
	//~ exit();
}

function PopulateSubjCats($pKforCurVals){
	if($pKforCurVals['alerts_subject_cats']) {
		return json_encode(getRegFieldAutoItems('subject_categories', prepAutocompleteKforField($pKforCurVals['alerts_subject_cats'])));
	} else {
		return '[]';
	}
}

function PopulateTaxonCats($pKforCurVals){
	if($pKforCurVals['alerts_taxon_cats']) {
		return json_encode(getRegFieldAutoItems('taxon_categories', prepAutocompleteKforField($pKforCurVals['alerts_taxon_cats'])));
	} else {
		return '[]';
	}
}

function PopulateChronCats($pKforCurVals){
	if($pKforCurVals['alerts_chronical_cats']) {
		return json_encode(getRegFieldAutoItems('chronological_categories', prepAutocompleteKforField($pKforCurVals['alerts_chronical_cats'])));
	} else {
		return '[]';
	}
}

function PopulateGeoCats($pKforCurVals){
	if($pKforCurVals['alerts_geographical_cats']) {
		return json_encode(getRegFieldAutoItems('geographical_categories', prepAutocompleteKforField($pKforCurVals['alerts_geographical_cats'])));
	} else {
		return '[]';
	}
}

function initSubjectsTree( $pKforCurVals ){
	return $pKforCurVals['treealerts_subject_cats'];
}

function initTaxonsTree( $pKforCurVals ){
	return $pKforCurVals['treealerts_taxon_cats'];
}

function initChronologicalTree( $pKforCurVals ){
	return $pKforCurVals['treealerts_chronical_cats'];
}

function initGeographicalTree( $pKforCurVals ){
	return $pKforCurVals['treealerts_geographical_cats'];
}

$lStep = ($user->id ? $lRegstep : (int)$_SESSION['regstep']);
if(!in_array($lStep, array(1,2,3))){
	$lStep = 1;
}
$lKforTeml = 'registerfrm.form_step'. $lStep;

switch ($user->id ? $lRegstep : (int)$_SESSION['regstep']) {
	default:
	case 1:
        $reg = new ctplkfor(
				array(
					'ctype' => 'ctplkfor',
					'method' => 'POST" id="registerfrm',
					'setformname' => 'registerfrm',
					'flds' => $gFormFields['registration_templ_fields_step1'],
					//~ 'js_validation' => JS_VALIDATION_ON,
					'js_validation' => false,
					'path_fields' => array (
						'path' => '/registration_flds.php',
						'key' => 'registration_templ_fields_step1',
					),
					'templs' => array(
						G_DEFAULT => $lKforTeml,
					),
				)
			);
        break;
    case 2:
        $reg = new ctplkfor(
				array(
					'ctype' => 'ctplkfor',
					'method' => 'POST" id="registerfrm',
					'setformname' => 'registerfrm',
					'flds' => $gFormFields['registration_templ_fields_step2'],
					'js_validation' => JS_VALIDATION_ON,
					'path_fields' => array (
						'path' => '/registration_flds.php',
						'key' => 'registration_templ_fields_step2',
					),
					'templs' => array(
						G_DEFAULT => $lKforTeml,
					),
				)
			);
		$reg->setKforVal('regstep', 2);
        break;
    case 3:
		$lDummy = '';

        $reg = new ctplkfor(
				array(
					'ctype' => 'ctplkfor',
					'method' => 'POST" id="registerfrm',
					'setformname' => 'registerfrm',
					'flds' => $gFormFields['registration_templ_fields_step3'],
					'js_validation' => JS_VALIDATION_ON,
					'path_fields' => array (
						'path' => '/registration_flds.php',
						'key' => 'registration_templ_fields_step3',
					),
					'templs' => array(
						G_DEFAULT => $lKforTeml,
					),
				)
			);
			$reg->setKforVal('regstep', 3);

			$lChronologicalTreeView = getRegTreeCategoriesByName('chronological_categories', true);
			$lTaxonTreeView = getRegTreeCategoriesByName('taxon_categories', true);
			$lSubjectsTreeView = getRegTreeCategoriesByName('subject_categories', true);
			$lGeographicalTreeView = getRegTreeCategoriesByName('geographical_categories', true);

			$reg->setKforVal('treealerts_subject_cats', $lSubjectsTreeView);
			$reg->setKforVal('treealerts_chronical_cats', $lChronologicalTreeView);
			$reg->setKforVal('treealerts_taxon_cats', $lTaxonTreeView);
			$reg->setKforVal('treealerts_geographical_cats', $lGeographicalTreeView);

        break;
}

if((int)$user->id) {
	$reg->setKforVal('userid', (int)$user->id);
} elseif(isset($_SESSION['tmpusrid'])) {
	$reg->setKforVal('userid', (int) $_SESSION['tmpusrid']);
}

if ($reg->KforAction() == 'register') {

	/*
		CHECK IF USER ALREADY EXISTS IN OLD DB
		START
	*/
	$lRegStep =  ($user->id ? $lRegstep : (int)$_SESSION['regstep']);

	if($lRegStep == 1 && !(int)$reg->getKforVal('userid')) { // Check for user in OLD PENSOFT DB
		$lCon = new DbCn(MYSQL_DBTYPE);
		$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
		$lCon->Execute('SELECT CID FROM CLIENTS WHERE EMAIL = \'' . $reg->getKforVal('email') . '\'');
		$lCon->MoveFirst();
		if((int)$lCon->mRs['CID']) {
			$reg->KforSetErr('email', getstr('This user already exists in our database!'));
		}
	}

	/*
		CHECK IF USER ALREADY EXISTS IN OLD DB
		END
	*/

	if ( $lRegStep == 1  && !checkIfPasswordIsSecure($reg->getKforVal('password'))) {
		$reg->KforSetErr('password1', getstr('regprof.pass_not_secure'));
	}

	if ($reg->getKforVal('password') != $reg->getKforVal('password2')) {
		$reg->KforSetErr('password2', getstr('regprof.pass_not_match'));
	}
}

if ($reg->KforAction() == 'showedit') {

	if ($reg->getKforVal('password') != $reg->getKforVal('password2')) {
		$reg->KforSetErr('password2', getstr('regprof.pass_not_match'));
	}
	if($lShowEdit) {
		$reg->setKforVal('editprof', 1);
		$reg->setProp('email', 'AddTags', array(
				'onfocus' => 'changeFocus(1, this)',
				'onblur'  => 'changeFocus(2, this)',
				'fldattr'  => '0',
				'readonly' => 'readonly',
				'id'  => 'P-RegFld-Email',
			));
	}
}

$reg->GetData();

if ($reg->KforAction() == 'register' && $reg->KforErrCnt() == 0) {
	$lRegStep =  ($user->id ? $lRegstep : (int)$_SESSION['regstep']);
	if($lRegStep == 1) {
		$confhash = GetConfHash($reg->getKforVal('email'), $reg->getKforVal('email'));
		$_SESSION['confhash'] = $confhash;
		$_SESSION['usermail'] = $reg->getKforVal('email');
		$cn = Con();
		$cn->Execute('SELECT * FROM SetConfHash (\'' . q($reg->getKforVal('email')) . '\', \'' . q($reg->getKforVal('email')) . '\', \'' . q($confhash) . '\')');

		if((int)$reg->getKforVal('userid') && $user->id){
			$cn = Con();
			$cn->Execute('SELECT oldpjs_cid FROM usr WHERE id = ' . (int)$reg->getKforVal('userid'));
			$cn->MoveFirst();
			$lOldPjsCid = (int)$cn->mRs['oldpjs_cid'];
			$cn->Close();

			$lCon = new DbCn(MYSQL_DBTYPE);
			$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
			$lCon->Execute('CALL spRegUsrStep1(' . ((int)$lOldPjsCid ? (int)$lOldPjsCid : 'NULL') . ', 1, \'' . q($reg->getKforVal('email')) . '\', \'' . q($reg->getKforVal('password')) . '\')');
			$lCon->MoveFirst();
			$lCon->Close();
		} else {
			/*
				INSERT NEW USER INTO OLD PENSOFT DATABASE FOR STEP 1
				START
			*/
			$lCon = new DbCn(MYSQL_DBTYPE);
			$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
			$lCon->Execute('CALL spRegUsrStep1(NULL, 1, \'' . q($reg->getKforVal('email')) . '\', \'' . q($reg->getKforVal('password')) . '\')');
			$lCon->MoveFirst();
			$lOldPjsCid = (int)$lCon->mRs['CID'];

			$lCon->Close();

			$cn->Execute('SELECT * FROM spSaveOldPJSId(\'' . q($reg->getKforVal('email')) . '\', ' . (int)$lOldPjsCid . ')');
			$cn->Close();

			/*
				INSERT NEW USER INTO OLD PENSOFT DATABASE FOR STEP 1
				END
			*/
		}

	}

	if((int)$reg->getKforVal('userid') && !$user->id)
		$_SESSION['tmpusrid'] = (int)$reg->getKforVal('userid');

	if($lRegStep == 2 && (int)$reg->getKforVal('userid')) {
		/*
			INSERT NEW USER INTO OLD PENSOFT DATABASE FOR STEP 2
			START
		*/
		$cn = Con();
		$cn->Execute('SELECT u.oldpjs_cid, ut.name as salut, ct.name as ctip, c.name as country
					FROM usr u
					LEFT JOIN usr_titles ut ON  ut.id = ' . (int)$reg->getKforVal('usrtitle') . '
					LEFT JOIN client_types ct ON ct.id = ' . (int)$reg->getKforVal('clienttype') . '
					LEFT JOIN countries c ON c.id = ' . (int)$reg->getKforVal('country') . '
					WHERE u.id = ' . (int)$reg->getKforVal('userid'));
		$cn->MoveFirst();



		$lCon = new DbCn(MYSQL_DBTYPE);
		$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
		$lCon->Execute('CALL spRegUsrStep2(
											' . (int)$cn->mRs['oldpjs_cid'] . ',
											1,
											\'' . q($reg->getKforVal('firstname')) . '\',
											\'' . q($reg->getKforVal('middlename')) . '\',
											\'' . q($reg->getKforVal('lastname')) . '\',
											\'' . q($cn->mRs['salut']) . '\',
											\'' . q($cn->mRs['ctip']) . '\',
											\'' . q($reg->getKforVal('affiliation')) . '\',
											\'' . q($reg->getKforVal('department')) . '\',
											\'' . q($reg->getKforVal('addrstreet')) . '\',
											\'' . q($reg->getKforVal('postalcode')) . '\',
											\'' . q($reg->getKforVal('city')) . '\',
											\'' . q($cn->mRs['country']) . '\',
											\'' . q($reg->getKforVal('phone')) . '\',
											\'' . q($reg->getKforVal('fax')) . '\',
											\'' . q($reg->getKforVal('vatnumber')) . '\',
											\'' . q($reg->getKforVal('website')) . '\')');
		$lCon->MoveFirst();
		$lCon->Close();
		$cn->Close();

		/*
			INSERT NEW USER INTO OLD PENSOFT DATABASE FOR STEP 2
			END
		*/
	}

	if($lRegStep < 3){
		$_SESSION['regstep'] = $lRegStep + 1;
	}

	if ($lRegStep == 3) {
		/*
			INSERT NEW USER INTO OLD PENSOFT DATABASE FOR STEP 3
			START
		*/
		$cn = Con();
		$cn->Execute('SELECT u.oldpjs_cid, af.name as emnot
					FROM usr u
					LEFT JOIN usr_alerts_frequency af ON  af.id = ' . (int)$reg->getKforVal('alertsfreq') . '
					WHERE u.id = ' . (int)$reg->getKforVal('userid'));
		$cn->MoveFirst();
		$lProductTypes = $reg->getKforVal('producttypes');


		$lCon = new DbCn(MYSQL_DBTYPE);
		$lCon->Open(MYSQL_DB_SRV, MYSQL_DB, MYSQL_USR, MYSQL_PASS);
		$lCon->Execute('CALL spRegUsrStep3(
											' . (int)$cn->mRs['oldpjs_cid'] . ',
											1,
											' . ((int)$lProductTypes[0] ? 1 : 0) . ',
											' . ((int)$lProductTypes[1] ? 1 : 0) . ',
											' . ((int)$lProductTypes[2] ? 1 : 0) . ',
											\'' . q($cn->mRs['emnot']) . '\')');
		$lCon->MoveFirst();
		$lCon->Close();
		$cn->Close();

		/*
			INSERT NEW USER INTO OLD PENSOFT DATABASE FOR STEP 3
			END
		*/

		if(!$user->id) {
			$lCon = new DbCn();
			$lCon->Open();
			$lCon->Execute('SELECT autolog_hash
							FROM public.usr u
							WHERE u.uname = \'' . q($_SESSION['usermail']) . '\'');
			$lAutoLogHash = $lCon->mRs['autolog_hash'];
			$lCon->Close();

		/* -- NE SE PRASHTA EMAIL

			$lCon = new DbCn();
			$lCon->Open();
			$lCon->Execute('SELECT coalesce(ut.name || \' \' || u.first_name || \' \' || u.last_name, u.uname) as fullname
							FROM public.usr u
							LEFT JOIN public.usr_titles ut ON ut.id = u.usr_title_id
							WHERE u.uname = \'' . q($_SESSION['usermail']) . '\'');
			$lUserFullName = $lCon->mRs['fullname'];
			$lCon->Close();

			$mespubdata = array(
				'confhash' => $_SESSION['confhash'],
				'siteurl' => SITE_URL,
				'user_fullname' => $lUserFullName,
				'mailsubject' => PENSOFT_MAILSUBJ_REGISTER,
				'mailto' => $_SESSION['usermail'],
				'charset' => 'UTF-8',
				'boundary' => '--_separator==_',
				'from' => array(
					'display' => PENSOFT_MAIL_DISPLAY,
					'email' => PENSOFT_MAIL_ADDR,
				),
				'templs' => array(
					G_DEFAULT => 'registerfrm.mailcontent',
				),
			);
			$msg = new cmessaging($mespubdata);
			$msg->Display();
		*/
		}

		unset($_SESSION['regstep']);
		unset($_SESSION['tmpusrid']);
		unset($_SESSION['usermail']);
		unset($_SESSION['confhash']);

		//~ echo '<script>ShowRegSuccess();</script>';

		// AUTOLOGIN
		echo '<script type="text/javascript">window.parent.location="/login.php?u_autolog_hash=' . $lAutoLogHash . '";</script>';
		exit;
	} else {
		if($user->id) {
			echo '<script type="text/javascript">LayerProfEditFrm(\'P-Registration-Content\', 2, ' . $lRegStep . ');</script>';
			exit;
		} else {
			echo '<script type="text/javascript">LayerRegFrm(\'P-Registration-Content\', 1, 1);</script>';
			exit;
		}

	}
} else {
	$reg->setKforVal('upass1', '');
	$reg->setKforVal('upass2', '');
}

//$reg->StopErrDisplay(1);

$lDefTempl = DefObjTempl();
if (!$lSuccess) {
	$c = new csimple(array(
		'ctype' => 'csimple',
		'templs' => array( G_DEFAULT => 'registerfrm.registerfrmwrapper' ) ,
		'form' => $reg->Display(),
		'errorholderclass' => ($reg->KforErrCnt() == 0 ) ? '' : 'loginErrors',
	));
} else {
	$c = new csimple(array(
		'ctype' => 'csimple',
		'templs' => array( G_DEFAULT => 'registerfrm.registerfrmwrappersuccess' ) ,
		'form' => $reg->Display(),
		'errorholderclass' => ($reg->KforErrCnt() == 0 ) ? '' : 'loginErrors',
	));
}


$t = array(
	'contents' => $c,
);

$inst = new cpage(array_merge($t, $lDefTempl), array(G_MAINBODY => 'global.registerpage'));
$inst->Display();

?>