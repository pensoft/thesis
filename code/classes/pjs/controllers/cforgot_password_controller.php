<?php

class cForgot_Password_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$lSuccessValue = $this->GetValueFromRequestWithoutChecks('success');
		//This page should be accessible only to non logged users;
		$this->RedirectIfLogged();

		if ($lSuccessValue != 1) {
			$lForm = new FPass_Wrapper(array(
				'ctype' => 'FPass_Wrapper',
				'page_controller_instance' => $this,
				'name_in_viewobject' => 'fpass_form',
				'use_captcha' => 1,
				'form_method' => 'post',
				'form_name' => 'fpass_form',
				'dont_close_session' => true,
				'js_validation' => JS_VALIDATION,
				'fields_metadata' => 'loginform.fpass', 
			));
		} else {
			$lSuccessMsg = getstr('fpass.success');
			$lForm = array(
				'ctype' => 'evSimple_Block_Display',
				'name_in_viewobject' => 'fpass_success_content',
				'success_msg' => $lSuccessMsg
			);
		}
		$pViewPageObjectsDataArray['form'] = $lForm;

		$this->m_pageView = new pFPass_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));

	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}

}

?>