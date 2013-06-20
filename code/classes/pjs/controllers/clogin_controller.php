<?php

class cLogin_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$lSuccessValue = $this->GetValueFromRequestWithoutChecks('success');

		$lLogout = $this->GetValueFromRequestWithoutChecks('logout');

		$lAutologinHash = $this->GetValueFromRequestWithoutChecks('u_autolog_hash');

		$lRedirUrl = $this->GetValueFromRequestWithoutChecks('redirurl');

		$lDocumentId = $this->GetValueFromRequestWithoutChecks('document_id');
		$lRoleId = $this->GetValueFromRequestWithoutChecks('view_role');

		if(!$lRedirUrl){
			$lRedirUrl = '/index.php';
		}

		//If the user has requested to be logged out - perform logout
		if((int)$lLogout){
			global $COOKIE_DOMAIN;

			if(isset($_SESSION['regstep']))  unset($_SESSION['regstep']);
			if(isset($_SESSION['tmpusrid'])) unset($_SESSION['tmpusrid']);
			if(isset($_SESSION['usermail'])) unset($_SESSION['usermail']);
			if(isset($_SESSION['confhash'])) unset($_SESSION['confhash']);
			if(isset($_COOKIE['h_cookie'])) setcookie('h_cookie', '', time() - 3600, '/', $COOKIE_DOMAIN);

			$lAutologinCookieUrl = '/set_pwt_cookie.php?logout=1&redirurl=' . $lRedirUrl;
			$this->UnlogUser($lAutologinCookieUrl);
		}

		if((int)$this->GetValueFromRequestWithoutChecks('confirmed'))
			$lMailIsConfirmed = getstr('pjs.mail_is_confirmed');
		else
			$lMailIsConfirmed = '';

		//This page should be accessible only to non logged users;
		$lRedirLoggedUserUrl = '/index.php';
		if($lDocumentId && $lRoleId) {
			$lRedirLoggedUserUrl = '/view_document.php?id=' . $lDocumentId . '&view_role=' . $lRoleId;
		}
		$this->RedirectIfLogged($lRedirLoggedUserUrl);

		//Auto Login With Hash
		if($lAutologinHash) {
			if($lDocumentId && $lRoleId) {
				$lRedirUrl = urlencode('/view_document.php?id=' . $lDocumentId . '&view_role=' . $lRoleId);
			}
			$lAutologinCookieUrl = '/set_pwt_cookie.php?redirurl=' . $lRedirUrl;
			$lLoginResult = $this->ProcessAutoLoginRequest($lAutologinHash, true, $lAutologinCookieUrl);
		}
// 		var_dump((int) $_SESSION['wrong_login_attempts']);
		$lForm = array(
			'ctype' => 'LoginForm_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'login_form',
			'use_captcha' => (int) $_SESSION['wrong_login_attempts'] >= MAX_ALLOWED_WRONG_LOGIN_ATTEMPTS ? 1 : 0,
			'form_method' => 'POST',
			'form_name' => 'login_form',
			'dont_close_session' => true,
			'fields_metadata' => array(
				'uname' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.login.uname'),
					'AllowNulls' => false,
					'AddTags' => array(
						'class' => 'P-Input',
					)
				),
				'upass' => array(
					'CType' => 'password',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.login.upass'),
					'AllowNulls' => false,
					'AddTags' => array(
					)
				),
				'redirurl' => array(
					'CType' => 'hidden',
					'VType' => 'string',
					'AllowNulls' => false,
					'AddTags' => array(
						'class' => 'inputFld'
					)
				),
				'login' => array(
					'CType' => 'action',
					'ActionMask' => ACTION_CHECK | ACTION_SHOW,
					'SQL' => 'SELECT {uname} || \'1\' as name, {upass} || \'1\' as upass',
					'CheckCaptcha' => 1,
					'DisplayName' => getstr('pjs.login.btn'),
					'RedirUrl' => $rewrite->EncodeUrl('/index.php', true),
					'AddTags' => array(
						'class' => 'inputBtn'
					)
				)
			)
		);

		if((int)$_REQUEST['regsuccess'])
			$lRegSuccessMsg = getstr('pjs.registersuccessmsg');
		else
			$lRegSuccessMsg = '';

		$pViewPageObjectsDataArray['form'] = $lForm;
		$pViewPageObjectsDataArray['success_reg_message'] = $lRegSuccessMsg;
		$pViewPageObjectsDataArray['mail_confirmed_message'] = $lMailIsConfirmed;

		$this->m_pageView = new pLogin_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));

	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;

	}

}

?>