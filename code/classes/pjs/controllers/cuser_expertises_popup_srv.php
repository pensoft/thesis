<?php

class cUser_Expertises_PopUp_Srv extends cBase_Controller {
	var $m_form;
	var $m_taskModel;
	var $m_Categories_Controller;
	
	function __construct() {
		parent::__construct();
		$this->m_Categories_Controller = new cCategories_Controller();
		
		$_POST['alerts_subject_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_subject_cats']);
		$_POST['alerts_taxon_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_taxon_cats']);
		$_POST['alerts_geographical_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_geographical_cats']);
		
		$this->getTaskForm();
		$pViewPageObjectsDataArray['form'] = $this->m_form;
		$this->m_pageView = new pUser_Expertises_PopUp_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	private function getTaskForm() {
		$lFieldsTempl = array(
			'se_uid' => array(
				'VType' => 'int' ,
				'CType' => 'hidden',
				'AllowNulls' => true,
				'DisplayName' => '',
			),
			'journal_id' => array(
				'VType' => 'int' ,
				'CType' => 'hidden',
				'AllowNulls' => true,
				'DisplayName' => '',
			),
			'document_id' => array(
				'VType' => 'int' ,
				'CType' => 'hidden',
				'AllowNulls' => true,
				'DisplayName' => '',
			),
			'alerts_subject_cats' => array(
				'CType' => 'text',
				'VType' => 'string',
				'AllowNulls' => true,
				'DisplayName' => getstr('regprof.exp_alerts_subject_cats'),
				'AddTags' => array(
					'id' => 'alerts_subject_cats_autocomplete',
				),
			),
			'alerts_taxon_cats' => array(
				'CType' => 'text',
				'VType' => 'string',
				'AllowNulls' => true,
				'DisplayName' => getstr('regprof.exp_alerts_taxon_cats'),
				'AddTags' => array(
					'id' => 'alerts_taxon_cats_autocomplete',
				),
			),
			'alerts_geographical_cats' => array(
				'CType' => 'text',
				'VType' => 'string',
				'AllowNulls' => true,
				'DisplayName' => getstr('regprof.exp_alerts_geographical_cats'),
				'AddTags' => array(
					'id' => 'alerts_geographical_cats_autocomplete',
				),
			),
			'new' => array(
				'CType' => 'action',
				'SQL' => '/*{document_id}{se_uid}{journal_id}{alerts_subject_cats}{alerts_taxon_cats}{alerts_geographical_cats}*/',
				'CheckCaptcha' => 0,
				'DisplayName' => '',
				'ActionMask' => ACTION_CHECK | ACTION_FETCH | ACTION_SHOW,
			),
			'save' => array(
				'CType' => 'action',
				'SQL' => '
					SELECT * FROM spSaveUserExpertises(
						{se_uid},
						{journal_id}, 
						{alerts_subject_cats}, 
						{alerts_taxon_cats}, 
						{alerts_geographical_cats}
					)
					/*{document_id}*/
				', 	
				'CheckCaptcha' => 0,
				'DisplayName' => 'save',
				'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH,// | ACTION_REDIRECT,
				'AddTags' => array(
					'onclick' => 'enableAllInputs(\'create_user_form\');',
				),
			)
		);
		$lJsValidation = 0;
		$lNameInViewObject = 'userexpertisesfrm';	
		
		$lBaseFormArr = array(
			'ctype' => 'UserExpertisesForm_Wrapper',
			'page_controller_instance' => $this,
			'dont_close_session' => true,
			'name_in_viewobject' => $lNameInViewObject,
			'form_method' => 'POST',
			'js_validation' => $lJsValidation,
			'form_name' => 'user_expertises',
			'fields_metadata' => $lFieldsTempl,
		);
		
		$lSubject = $this->m_Categories_Controller->getCategoriesAndAutocomplete('subjects_tree', 'subjects_tree_script', 'alerts_subject_cats', 'subject_categories');
		$lTaxon   = $this->m_Categories_Controller->getCategoriesAndAutocomplete('taxon_tree', 'taxon_tree_script', 'alerts_taxon_cats', 'taxon_categories');
		$lGeographical = $this->m_Categories_Controller->getCategoriesAndAutocomplete('geographical_tree', 'geographical_tree_script', 'alerts_geographical_cats', 'geographical_categories');
		
		$this->m_form = new UserExpertisesForm_Wrapper(array_merge($lBaseFormArr, $lSubject, $lTaxon, $lGeographical));
		
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}

}

?>