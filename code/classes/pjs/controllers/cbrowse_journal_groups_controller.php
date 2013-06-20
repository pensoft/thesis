<?php
class cBrowse_Journal_Groups_Controller extends cBase_Controller {
	var $m_Categories_Controller;
	
	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();

		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lGroupId = (int)$this->GetValueFromRequestWithoutChecks('grp_id');
		$lRoleId = (int)$this->GetValueFromRequestWithoutChecks('role_id');
		$lAction = $this->GetValueFromRequestWithoutChecks('tAction');
		$this->m_models['mJournal_Group_Model'] = new mJournal_Group_Model();
		$lJournalGroups = $this->m_models['mJournal_Group_Model']->GetJournalGroups($lJournalId);
		$lFirstGroupByPos = array();
		$lFirstGroupByPos = $lJournalGroups[0]['id'];
		$this->m_models['mEdit_Model'] = new mEdit_model();
		if(!$lGroupId && !$lRoleId && !$lAction == "Filter"){
			header('Location: browse_journal_groups.php?grp_id=' . $lFirstGroupByPos . '');
			$lGroupId = $lFirstGroupByPos;
		}
		$this->m_Categories_Controller = new cCategories_Controller();

		$lFieldsMetadataTempl = array(
			'journal_id' => array(
				'CType' => 'hidden',
				'VType' => 'int',
				'DefValue' => $lJournalId,
				'AllowNulls' => true,
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
				'DisplayName' => '',
				'AddTags' => array(
					'id' => 'alerts_taxon_cats_autocomplete',
				),
			),
			'alerts_geographical_cats' => array(
				'CType' => 'text',
				'VType' => 'string',
				'AllowNulls' => true,
				'DisplayName' => '',
				'AddTags' => array(
					'id' => 'alerts_geographical_cats_autocomplete',
				),
			),
			'funding_agency' => array(
				'CType' => 'text',
				'VType' => 'string',
				'AllowNulls' => true,
				'DisplayName' => '',
			),
			'Filter' => array(
				'CType' => 'action',
				// 'SQL' => 'SELECT * FROM pjs.journal_users jusr left JOIN pjs.journal_users_expertises exp ON jusr.id = exp.journal_usr_id WHERE jusr.role_id = 2',
				'DisplayName' => getstr('pjs.filter'),
				'ActionMask' =>  ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW
			),
			'showedit' => array(
				'CType' => 'action',
				'SQL' => '',
				'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
			)
		);
		
		$lFilterArticlesFormArr = array(
			'ctype' => 'Browse_Groups_Form_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'browse_groups_form_templ',
			'use_captcha' => 0,
			'form_method' => 'POST',
			'form_action' => '/browse_journal_groups?role_id=3',
			'js_validation' => 0,
			'form_name' => 'filter_groups',
			'dont_close_session' => true,
			'fields_metadata' => $lFieldsMetadataTempl,
			'htmlformid' => 'filter_groups',
		);		
		$lTrees = array_merge( 
			$this->m_Categories_Controller->getCategoriesAndAutocomplete('subjects_tree', 'subjects_tree_script', 'alerts_subject_cats', 'subject_categories'),
			$this->m_Categories_Controller->getCategoriesAndAutocomplete('taxon_tree', 'taxon_tree_script', 'alerts_taxon_cats', 'taxon_categories'),
			$this->m_Categories_Controller->getCategoriesAndAutocomplete('geographical_tree', 'geographical_tree_script', 'alerts_geographical_cats', 'geographical_categories')
		);
		
		$lForm = new Browse_Groups_Form_Wrapper(array_merge($lFilterArticlesFormArr, $lTrees));
		$this->m_models['mUsers'] = new mUsers();
		
		for ($i = 0; $i<count($lJournalGroups); $i++)
			$lJournalGroups[$i]['title'] = str_replace (' - ', '&nbsp;', $lJournalGroups[$i]['title']);
		$pViewPageObjectsDataArray['leftcol'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'leftcol',
			'search_form' => $lForm,
			'journal_id' => $lJournalId,
			'grp_id' => $lGroupId,
			'role_id' => $lRoleId,
			'taction' => $lAction,
			'controller_data' => $lJournalGroups,
		);
		if ($lRoleId || $lAction == "Filter") { // Subject Editors list
			$name_in_viewobject = 'browse_group_se_list_templs';
			$lControllerData = $this->m_models['mUsers']->GetUsersByCategories(
																			$lJournalId, (int)$this->GetValueFromRequestWithoutChecks('p'),
																			$lForm->wGetFieldValue('alerts_taxon_cats'),
																			$lForm->wGetFieldValue('alerts_subject_cats'),
																			$lForm->wGetFieldValue('alerts_geographical_cats')
																		);
		} else {
			$name_in_viewobject = 'browse_group_list_templs';	
			$lControllerData = $this->m_models['mUsers']->GetUsersByGroup($lGroupId, $this->GetValueFromRequestWithoutChecks('p'));
		}
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => $name_in_viewobject,
			'controller_data' => $lControllerData,
			'journal_id' => $lJournalId,
			'default_page_size' => DEFAULT_PAGE_SIZE,
			'groupstep' => 15,
			'page_parameter_name' => 'p',
		);
		
		$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pBrowse_Journal_Groups_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>