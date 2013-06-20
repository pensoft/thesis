<?php

class cAjaxFormValidate_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$lField = $this->GetValueFromRequest('check_field');
		$lFieldValue = $lField['value'];
		$lFieldsTemlName = $this->GetValueFromRequest('fields_templ_name');
		$lFieldsTemlValue = $lFieldsTemlName['value'];
		
		$this->m_pageView = new pForm_Check_Page_View(array());
		$lForm = new eForm_Wrapper(array(
			'ctype' => 'eForm_Wrapper',
			'name_in_viewobject' => 'ajax_validate_form',
			'controller_instance' => $this,
			'use_captcha' => 1,
			'form_method' => 'get',
			'form_name' => 'ajax_validate_form',
			'fields_metadata' => $lFieldsTemlValue,			
		));
		$lForm->setViewObject($this->m_pageView);
		$this->m_pageView->SetPubData($lForm->GetFormValidationRespond($lFieldValue));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}

?>