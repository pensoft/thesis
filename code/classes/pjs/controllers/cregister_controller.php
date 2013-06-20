<?php

class cRegister_Controller extends cBase_Controller {
	var $m_MyExpertise;
	var $m_Categories_Controller;

	function __construct() {
		parent::__construct();
		$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.pensoft_account');
		$lTrees = array();

		$this->m_MyExpertise = (int)$this->GetValueFromRequestWithoutChecks('my_expertise');

		$this->m_Categories_Controller = new cCategories_Controller();
		$_POST['alerts_subject_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_subject_cats']);
		$_POST['alerts_taxon_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_taxon_cats']);
		$_POST['alerts_geographical_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_geographical_cats']);
		$_POST['alerts_chronical_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_chronical_cats']);

		if((int)$this->m_MyExpertise && (int)$this->GetUserId()){
			$pViewPageObjectsDataArray['title.suffix'] = getstr('pjs.account.expertises');
			$lFieldsMetadataTempl = array(
				'userid' => array(
					'CType' => 'hidden',
					'VType' => 'int',
					'AllowNulls' => true,
					'AddTags' => array(
						'id'  => 'userid',
					),
				),
				'my_expertise' => array(
					'CType' => 'hidden',
					'VType' => 'int',
					'AllowNulls' => true,
					'DefValue' => 0,
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
				'alerts_chronical_cats' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => true,
					'DisplayName' => getstr('regprof.exp_alerts_chronical_cats'),
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
				'register' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM spSaveUsrExpertise(' . (int)$this->GetUserId() . ', {alerts_subject_cats}, {alerts_chronical_cats}, {alerts_taxon_cats}, {alerts_geographical_cats})',
					'ActionMask' =>  ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
				'showedit' => array(
					'CType' => 'action',
					'SQL' => 'SELECT id as userid, expertise_subject_categories as alerts_subject_cats,
									expertise_chronological_categories as alerts_chronical_cats,
									expertise_taxon_categories as alerts_taxon_cats,
									expertise_geographical_categories as alerts_geographical_cats
								FROM usr
								WHERE id = ' . (int)$this->GetUserId(),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				)
			);
			$lJsValidation = 0;
			$lSuccessMsg = getstr('pjs.my_expertise_chanched_success');
			$lNameInViewObject = 'my_expertise_form';
			$lTrees = array_merge(
				$this->m_Categories_Controller->getCategoriesAndAutocomplete('subjects_tree', 'subjects_tree_script', 'alerts_subject_cats', 'subject_categories', 'tree_list', 'tree_script_reg', 2, 0, 1, 0),
				$this->m_Categories_Controller->getCategoriesAndAutocomplete('chronological_tree', 'chronological_tree_script', 'alerts_chronical_cats', 'chronological_categories', 'tree_list', 'tree_script_reg', 2, 0, 1, 0),
				$this->m_Categories_Controller->getCategoriesAndAutocomplete('taxon_tree', 'taxon_tree_script', 'alerts_taxon_cats', 'taxon_categories', 'tree_list', 'tree_script_reg', 2, 0, 1, 0),
				$this->m_Categories_Controller->getCategoriesAndAutocomplete('geographical_tree', 'geographical_tree_script', 'alerts_geographical_cats', 'geographical_categories', 'tree_list', 'tree_script_reg', 2, 0, 1, 0)
			);
		}else{
			$this->GetRegisterStep();

			switch($_SESSION['regstep']){
				case 2:
					$lFieldsMetadataTempl = 'profile.step2';
					$lJsValidation = JS_VALIDATION;
					$lSuccessMsg = getstr('pjs.profile_info_change_success');
					$pViewPageObjectsDataArray['title.suffix'] = getstr('pjs.account.profile');
					break;
				case 3:
					$lFieldsMetadataTempl = 'profile.step3';
					$lJsValidation = 0;
					$lTrees = array_merge(
						$this->m_Categories_Controller->getCategoriesAndAutocomplete('subjects_tree', 'subjects_tree_script', 'alerts_subject_cats', 'subject_categories', 'tree_list', 'tree_script_reg', 2, 0, 1, 0),
						$this->m_Categories_Controller->getCategoriesAndAutocomplete('chronological_tree', 'chronological_tree_script', 'alerts_chronical_cats', 'chronological_categories', 'tree_list', 'tree_script_reg', 2, 0, 1, 0),
						$this->m_Categories_Controller->getCategoriesAndAutocomplete('taxon_tree', 'taxon_tree_script', 'alerts_taxon_cats', 'taxon_categories', 'tree_list', 'tree_script_reg', 2, 0, 1, 0),
						$this->m_Categories_Controller->getCategoriesAndAutocomplete('geographical_tree', 'geographical_tree_script', 'alerts_geographical_cats', 'geographical_categories', 'tree_list', 'tree_script_reg', 2, 0, 1, 0)
					);
					$lSuccessMsg = getstr('pjs.subscriptions_changed_success');
					$pViewPageObjectsDataArray['title.suffix'] = getstr('pjs.account.subscriptions');
					break;
				default:
					$lFieldsMetadataTempl = 'profile.step1';
					$lJsValidation = 0;
					$lSuccessMsg = getstr('pjs.password_changed_success');
					$pViewPageObjectsDataArray['title.suffix'] = getstr('pjs.account.settings');
					break;
			}
			$lNameInViewObject = 'register_form_' . $_SESSION['regstep'];
		}
		$lBaseRegFormArr = array(
			'ctype' => 'RegisterForm_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => $lNameInViewObject,
			'use_captcha' => 0,
			'form_method' => 'POST',
			'js_validation' => $lJsValidation,
			'form_name' => 'registerfrm',
			'dont_close_session' => true,
			'fields_metadata' => $lFieldsMetadataTempl
		);

		$lForm = new RegisterForm_Wrapper(array_merge($lBaseRegFormArr, $lTrees));

		$pViewPageObjectsDataArray['form'] = $lForm;

		if((int)$this->GetValueFromRequestWithoutChecks('editprofile') || (int)$this->GetUserId() || (int)$this->m_MyExpertise){
			$this->m_pageView = new pEdit_Profile_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		}else{
			$this->m_pageView = new pRegister_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		}
	}

	protected function GetRegisterStep(){
		if(!isset($_SESSION['regstep']))
			$_SESSION['regstep'] = 1;
		if(isset($_REQUEST['step'])){
			$lStep = (int)$this->GetValueFromRequestWithoutChecks('step');
			if($lStep < 1 || $lStep > 3){
				$_SESSION['regstep'] = 1;
			}else{
				$_SESSION['regstep'] = (int)$lStep;
			}
		}
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}

?>