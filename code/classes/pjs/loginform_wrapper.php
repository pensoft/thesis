<?php

class LoginForm_Wrapper extends eForm_Wrapper{
	/**
	 * A reference to the page controller
	 * @var cLogin_Controller
	 */
	var $m_pageControllerInstance;
	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);

	}

	protected function PostActionProcessing(){
		if(!$this->m_formController->GetErrorCount() && $this->m_formController->GetCurrentAction() == 'login'){
			$lUserName = $this->m_formController->GetFieldValue('uname');
			$lPassword = $this->m_formController->GetFieldValue('upass');
			$lRedirUrl = $this->m_formController->GetFieldValue('redirurl');
			if(!$lRedirUrl){
				$lRedirUrl = '/index.php';
			}

			$lAutologinCookieUrl = '/set_pwt_cookie.php?redirurl=' . urlencode($lRedirUrl);
			$lLoginResult = $this->m_pageControllerInstance->ProcessLoginRequest($lUserName, $lPassword, true, $lAutologinCookieUrl);
			if($lLoginResult['err_cnt']){
				foreach ($lLoginResult['err_msgs'] as $lCurrentError) {
					$this->m_formController->SetError($lCurrentError['err_msg']);
				}
				if($_SESSION['wrong_login_attempts'] >= MAX_ALLOWED_WRONG_LOGIN_ATTEMPTS){
					$this->m_formController->m_useCaptcha = true;
				}
			}
		}
	}


}

?>