<?php

class cConfirm_Email_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();
		
		$lHash = $this->GetValueFromRequestWithoutChecks('hash');
		
		if ($lHash) {
			$lRegisterModel = new mRegister_model();
			if(!$lRegisterModel->ConfirmUserEmail($lHash)){
				header('Location: /');
				exit;
			}else{
				header('Location: /login.php?confirmed=1');
			}
		} else {
			header('Location: /');
			exit;
		}
	}
}

?>