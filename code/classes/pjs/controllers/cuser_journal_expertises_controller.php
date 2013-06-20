<?php

class cUser_Journal_Expertises_Controller extends cBase_Controller {
	var $m_Categories_Controller;
	
	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.manage_user_journal_expertises');
		$this->InitViewingModeData();
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		$this->m_Categories_Controller = new cCategories_Controller();
		$_POST['alerts_subject_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_subject_cats']);
		$_POST['alerts_taxon_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_taxon_cats']);
		$_POST['alerts_geographical_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_geographical_cats']);

		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lUserForEditId = (int)$this->GetValueFromRequestWithoutChecks('user_id');
		
		// Osven proverkata na tekushto lognatiq user dali e Journal Manager, 
		// proverqvame dali usera koito se opitvame da editnem e sys SE_ROLE za syotvetniq journal
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckUserIsJournalManager($this->GetUserId(), $lJournalId) || 
			!$this->m_models['mEdit_Model']->CheckUserRight($lUserForEditId, $lJournalId, SE_ROLE)){
			header('Location: /index.php');
		}
		
		$lFieldsMetadataTempl = array(
			'user_id' => array(
					'CType' => 'hidden',
					'VType' => 'int',
					'AllowNulls' => true,
					'AddTags' => array(
							'id'  => 'userid',
					),
			),
			'journal_id' => array(
				'CType' => 'hidden',
				'VType' => 'int',
				'AllowNulls' => true,
				'DefValue' => 0,
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
			'save' => array(
				'CType' => 'action',
				'SQL' => 'SELECT * FROM spSaveUsrJournalExpertises({user_id}, {journal_id}, {alerts_subject_cats}, {alerts_taxon_cats}, {alerts_geographical_cats})',
				'ActionMask' =>  ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
			),
			'showedit' => array(
				'CType' => 'action',
				'SQL' => 'SELECT ju.uid as user_id, 
								e.subject_categories as alerts_subject_cats, 
								e.taxon_categories as alerts_taxon_cats,
								e.geographical_categories as alerts_geographical_cats
							FROM pjs.journal_users ju
							JOIN pjs.journal_users_expertises e ON e.journal_usr_id = ju.id
							WHERE ju.uid = {user_id} 
								AND ju.journal_id = {journal_id} 
								AND ju.role_id = ' . SE_ROLE,
				'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
			)
		);
		
		$lBaseFormArr = array(
			'ctype' => 'ExpertisesForm_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'user_journal_expertises_form',
			'use_captcha' => 0,
			'form_method' => 'POST',
			'js_validation' => 0,
			'form_name' => 'user_expertises',
			'dont_close_session' => true,
			'fields_metadata' => $lFieldsMetadataTempl
		);
		
		$lSubjectTree = $this->m_Categories_Controller->getCategoriesAndAutocomplete('subjects_tree', 'subjects_tree_script', 'alerts_subject_cats', 'subject_categories');
		$lTaxonTree   = $this->m_Categories_Controller->getCategoriesAndAutocomplete('taxon_tree', 'taxon_tree_script', 'alerts_taxon_cats', 'taxon_categories');
		$lGeographical = $this->m_Categories_Controller->getCategoriesAndAutocomplete('geographical_tree', 'geographical_tree_script', 'alerts_geographical_cats', 'geographical_categories');
		//~ var_dump($lSubjectTree);
		//~ exit;
		//~ var_dump($lGeographical);
		//~ exit;
		$lForm = new ExpertisesForm_Wrapper(array_merge($lBaseFormArr, $lSubjectTree, $lTaxonTree, $lGeographical));
		
		$pViewPageObjectsDataArray['contents'] 	 = $lForm;
	
		$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		
		$this->AddJournalObjects($lJournalId);
		$this->m_pageView = new pUser_Journal_Expertises_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>