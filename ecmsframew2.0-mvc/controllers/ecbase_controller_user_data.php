<?php

class ecBase_Controller_User_Data extends ecBase_Controller {
	/**
	 *
	 * @var mUser
	 */
	protected $m_user;

	protected $m_userId;

	function __construct() {
		parent::__construct();
		$this->InitUserData();
	}

	protected function InitUserData() {
		$this->m_userId = 0;
		$this->m_user = unserialize($_SESSION['suser']);
// 		var_dump($this->m_user);
		if($this->m_user){
			$this->m_userId = $this->m_user->id;
		}

	}

	function GetUserId() {
		//return 8;
		return $this->m_userId;
	}

	function RedirectIfNotLogged($pRedirectUri = '/index.php') {
		if(! $this->GetUserId()){
			$this->Redirect($pRedirectUri);
		}
	}

	function RedirectIfLogged($pRedirectUri = '/index.php') {
		if($this->GetUserId()){
			$this->Redirect($pRedirectUri);
		}
	}

	function UnlogUser($pRedirUrl = '/index.php') {
		global $COOKIE_DOMAIN;
		if((int) $this->GetUserId()){
			unset($_SESSION['suser']);
			setcookie('rememberme', '', mktime(), '/', $COOKIE_DOMAIN);
		}
		$this->Redirect($pRedirUrl);
	}

	// @formatter->off
	/**
	 * Process a login request
	 *
	 * @param $pUname string
	 * @param $pPassword string
	 * @param $pRedirOnSuccess boolean
	 *       	 - whether to redir on success
	 * @param $pRedirUrl string
	 *       	 - the url to redirect to (on success)
	 *
	 *       	 returns an array with the following format (
	 *       	 err_cnt => number of errors
	 *       	 err_msgs => an array containing the error msgs (an array
	 *       	 containing arrays with the following format
	 *       	 err_msg => the msg of the current error
	 *       	 )
	 */
	// @formatter->on
	function ProcessLoginRequest($pUname, $pPassword, $pRedirOnSuccess = true, $pRedirUrl = '/index.php') {
		global $user;
		$lLoginModel = new mLogin_Model();
		$lResult = $lLoginModel->HandleLoginRequestWithUsernameAndPassword($pUname, $pPassword, $_SERVER['REMOTE_ADDR']);



		if((int) $lResult['err_cnt']){
			if(!isset($_SESSION['wrong_login_attempts'])){
				$_SESSION['wrong_login_attempts'] = 0;
			}
			$_SESSION['wrong_login_attempts']++;
// 			var_dump('s' . $_SESSION['wrong_login_attempts']);
			return $lResult;
		}
		$_SESSION['wrong_login_attempts'] = 0;
		$user = new mUser($lResult);
		$_SESSION['suser'] = serialize($user);
		$this->m_user = $user;

		if($pRedirOnSuccess){
			$this->Redirect($pRedirUrl);
		}
		return array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);

	}

	// @formatter->on
	function ProcessAutoLoginRequest($pHash, $pRedirOnSuccess = true, $pRedirUrl = '/index.php') {
		global $user;
		$lLoginModel = new mLogin_Model();
		$lResult = $lLoginModel->HandleAutoLoginRequestWithHash($pHash, $_SERVER['REMOTE_ADDR']);

		if((int) $lResult['err_cnt']){
			return $lResult;
		}
		$user = new mUser($lResult);
		$_SESSION['suser'] = serialize($user);
		$this->m_user = $user;

		if($pRedirOnSuccess){
			$this->Redirect($pRedirUrl);
		}

		return array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);

	}

	function InitUserModel(){
		if(! isset($this->m_models['user_model'])){
			$this->m_models['user_model'] = new mUser_Model();
		}
	}

	function ChangeCurrentUserPicId($pPicId) {
		if((int) $this->m_userId){
			$this->InitUserModel();
			return $this->m_models['user_model']->ChangeUserPreviewPic($this->m_userId, $pPicId);
		}
		return array(
			'err_cnt' => 1,
			'err_msgs' => array(
				array(
					'err_msg' => getstr('global.onlyLoggedUsersCanPerformThisAction')
				)
			)
		);
	}

	function RemoveCurrentUserPic() {
		if((int) $this->m_userId){
			$this->InitUserModel();
			return $this->m_models['user_model']->RemoveUserPreviewPic($this->m_userId);
		}
		return array(
			'err_cnt' => 1,
			'err_msgs' => array(
				array(
					'err_msg' => getstr('global.onlyLoggedUsersCanPerformThisAction')
				)
			)
		);
	}

	/**
	 * Updates the data about the current user (both in the session and in the
	 * user object)
	 */
	function UpdateCurrentUserData() {
		global $user;
		if(! (int) $this->m_userId){
			return array(
				'err_cnt' => 1,
				'err_msgs' => array(
					array(
						'err_msg' => getstr('global.onlyLoggedUsersCanPerformThisAction')
					)
				)
			);
		}
		$this->InitUserModel();
		$lUserData = $this->m_models['user_model']->GetUserData($this->m_userId);
		$user = new mUser($lUserData);
		$_SESSION['suser'] = serialize($user);
		$this->m_user = $user;
	}

}

?>