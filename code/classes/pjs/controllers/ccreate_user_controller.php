<?php

class cCreate_User_Controller extends cBase_Controller {
	var $m_Categories_Controller;
	var $m_user_perm;
	
	function __construct() {
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$this->InitViewingModeData();

		$this->m_models['mEdit_Model'] = new mEdit_model();
		$this->m_Categories_Controller = new cCategories_Controller();
		$_POST['alerts_subject_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_subject_cats']);
		$_POST['alerts_taxon_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_taxon_cats']);
		$_POST['alerts_geographical_cats'] = $this->m_Categories_Controller->ClearExpertiseValues($_POST['alerts_geographical_cats']);
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lAjax = (int)$this->GetValueFromRequestWithoutChecks('ajax');
		$lMode = (int)$this->GetValueFromRequestWithoutChecks('mode');
		$lDocumentId = (int)$this->GetValueFromRequestWithoutChecks('document_id');
		$lRoundId = (int)$this->GetValueFromRequestWithoutChecks('round_id');
		$lRole = (int)$this->GetValueFromRequestWithoutChecks('role');
		$lRedirUrl = '/create_user.php?journal_id=' . $lJournalId;
		$lRightCol = 0;

		// assign reviwer to document InviteReviewerAsGhost(uid, doc_id, round_id)
		if((int)$this->GetValueFromRequestWithoutChecks('guid')){
			$lAllowNulls = true;
		}else{
			$lAllowNulls = false;
		}
		// 5 nominated reviwer
		global $user;
		
		
		
		switch ($lMode){
			case SE_ROLE:
				if (!in_array($lMode, $user->journals[$lJournalId])) {
					header('Location: /index.php');
					exit;
				} else {
					if($lRole == SE_ROLE) {
						$lRedirUrl = '/view_document.php?id=' . $lDocumentId . '&view_role=' . E_ROLE . '&mode=1&suggested=1';
					} else {
						$lRedirUrl = '/view_document.php?id=' . $lDocumentId . '&view_role=' . SE_ROLE . '&mode=1';
					}
				}
			break;
			case DEDICATED_REVIEWER_ROLE:
				if (!in_array($lMode, $user->journals[$lJournalId])) {
					header('Location: /index.php');
					exit;
				}
			break;
			case AUTHOR_ROLE:
				if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckDocumentAuthor($lDocumentId, $this->GetUserId())) {
					header('Location: /index.php');
					exit;
				} else {
					$lRedirUrl = '/document_bdj_submission.php?document_id=' . $lDocumentId;
				}
			break;
			default:
				if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckUserIsJournalManager($this->GetUserId(), $lJournalId)) {
					header('Location: /index.php');
					exit;
				}
			break;
		}
		
		// Permissions Check START
		$lCheckRole = $lMode;
		$lDocumentsModel = new mDocuments_Model();
		
		// bugfix for Editor - this must be changed when we have time
		if(($lRole == SE_ROLE && $lMode == SE_ROLE) || (!$lRole && !$lMode)) {
			$lCheckRole = E_ROLE;
		}
		$this->m_user_perm = $lDocumentsModel->CheckDocumentUserForSpecificRole($user->id, $lCheckRole, (int)$lJournalId, (int)$lDocumentId);
		if(!$this->m_user_perm) {
			header('Location: /index.php');
			exit;
		}
		// Permissions Check END
		
		// wich template to use (global.dashboard or global.big_right_col)
		if($lCheckRole == E_ROLE) {
			$lRightCol = 1;
		}
		
		switch ($lRole){
			case SE_ROLE:
				$lDisplayRoles = 'checkbox';
				$lRoles = array(
					SE_ROLE => '', // getstr('pjs.section_editor'),
				);
				$lTags = 'checked';
				$lDefValue = 3;
				break;
			case DEDICATED_REVIEWER_ROLE:
				$lRoles = array(
					DEDICATED_REVIEWER_ROLE => getstr('pjs.section_editor'),
				);
			break;
			default:
				if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckUserIsJournalManager($this->GetUserId(), $lJournalId))
					header('Location: /index.php');
				
				$lDisplayRoles = 'checkbox';
				$lRoles = array(	// Options for roles checkboxes
					JOURNAL_MANAGER_ROLE => getstr('pjs.journal_manager'),
					JOURNAL_EDITOR_ROLE => getstr('pjs.editor'),
					SE_ROLE => getstr('pjs.section_editor'),
					LE_ROLE => getstr('pjs.layout_editor'),
					CE_ROLE => getstr('pjs.copy_editor')
				);
		}
		$lBaseFormArr = array(
			'ctype' => 'createUser_Form_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'create_user_form_templ',
			'use_captcha' => 0,
			//'debug' => 1,
			'form_method' => 'POST',
			//'js_validation' => $lJsValidation,
			'form_name' => 'create_user_form',
			'dont_close_session' => true,
			'fields_metadata' => array(
				'mode' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'DefValue' => $lMode,
					'DisplayName' => '',
					'AllowNulls' => true,
					'AddTags' => array(
						'id' => 'mode'
					)
				),
				'event_id' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'DisplayName' => '',
					'AllowNulls' => true,
				),
				'upass' => array (
					'VType' => 'string' ,
					'CType' => 'hidden',
					'DisplayName' => '',
					'AllowNulls' => true,
				),
				'role' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'DefValue' => $lRole,
					'DisplayName' => '',
					'AllowNulls' => true,
					'AddTags' => array(
						'id' => 'role',
					)
				),
				'documentid' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'DefValue' => $lDocumentId,
					'DisplayName' => '',
					'AllowNulls' => true,
				),
				'roundid' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'DefValue' => $lRoundId,
					'DisplayName' => '',
					'AllowNulls' => true,
				),
				'guid' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'DisplayName' => '',
					'AllowNulls' => true,
				),
				'journal_id' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'DisplayName' => '',
					'AllowNulls' => true,
				),
				'email' => array(
						'CType' => 'text',
						'VType' => 'string',
						'DisplayName' => getstr('regprof.email'),
						'AllowNulls' => $lAllowNulls,
						'Checks' => array(
							'checkEmailAddr({email})',
						),
						'AddTags' => array(
							'onblur'  => 'checkIfUserExist(this, ' . (int)$lJournalId . ', ' . $lMode . ', ' . $lDocumentId . ', ' . $lRoundId . ', ' . $lRole . ');',
						),
				),
				'usrtitle' => array(
					'VType' => 'int' ,
					'CType' => 'select' ,
					'DisplayName' => getstr('regprof.usrtitle'),
					'SrcValues' => 'SELECT id as id, name as name FROM usr_titles',
					'AllowNulls' => $lAllowNulls,
				),
				'firstname' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => $lAllowNulls,
					'DisplayName' => getstr('regprof.firstname'),
				),
				'lastname' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => $lAllowNulls,
					'DisplayName' => getstr('regprof.lastname'),
				),
				'affiliation' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => $lAllowNulls,
					'DisplayName' => getstr('regprof.affiliation'),
				),
				'city' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => $lAllowNulls,
					'DisplayName' => getstr('regprof.city'),
				),
				'country' => array (
					'VType' => 'int' ,
					'CType' => 'select' ,
					'DisplayName' => getstr('regprof.country'),
					'SrcValues' => 'SELECT a.id, a.name
											FROM (
													SELECT null as id, \'-- select country --\' as name, 1 as ord
													UNION
													SELECT left(name, 1)::varchar as id, left(name, 1) as name, 2 as ord
													FROM countries
													GROUP BY left(name, 1)
													UNION
													SELECT id::varchar as id, name as name, 2 as ord
													FROM countries
											) as a
											ORDER BY ord, name',
					'AllowNulls' => $lAllowNulls,
					'AddTags' => array(
						'onchange' => 'HideOptionWithoutValue(this)',
						'id' => 'countries',
					),
				),
				'user_roles' => array(
					'VType' => 'string',
					'CType' => $lDisplayRoles,
					'DefValue' => 0,
					'AllowNulls' => true,
					'DisplayName' => getstr('pjs.user_journal_roles'),
					'TransType' => MANY_TO_SQL_ARRAY,
					'DefValue' => $lDefValue,
					'SrcValues' => $lRoles,
					'AddTags' => array(
						//'id' => 'user_roles_checkbox',
						'checked' => $lTags,
						'class' => 'roles'
					),
				),
				'roles' => array(
					'VType' => 'string',
					'CType' => 'hidden',
					'DefValue' => 0,
					'AllowNulls' => true,
					'DisplayName' => getstr('pjs.user_journal_roles'),
					'TransType' => MANY_TO_SQL_ARRAY,
					'SrcValues' => array(	// Options for roles checkboxes
						JOURNAL_MANAGER_ROLE => getstr('pjs.journal_manager'),
						JOURNAL_EDITOR_ROLE => getstr('pjs.editor'),
						SE_ROLE => getstr('pjs.section_editor'),
						LE_ROLE => getstr('pjs.layout_editor'),
						CE_ROLE => getstr('pjs.copy_editor')
					),
					'AddTags' => array(
						//'id' => 'user_roles_checkbox',
						'checked' => $lTags,
						'class' => 'roles'
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
				'alerts_geographical_cats' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => true,
					'DisplayName' => getstr('regprof.exp_alerts_geographical_cats'),
					'AddTags' => array(
						'id' => 'alerts_geographical_cats_autocomplete',
					),
				),
				'showedit' => array(
					'CType' => 'action',
					/*'SQL' => 'SELECT u.uname as email, u.id as guid, u.usr_title_id as usrtitle, 
									u.first_name as firstname, u.last_name as lastname, u.affiliation, 
									u.addr_city as city, u.country_id as country, array_agg(ju.role_id) as roles,
									je.subject_categories as alerts_subject_cats, je.taxon_categories as alerts_taxon_cats
								FROM usr u
								LEFT JOIN pjs.journal_users ju ON ju.uid = u.id AND ju.journal_id = ' . (int)$lJournalId . '
								LEFT JOIN pjs.journal_users_expertises je ON je.journal_usr_id = ju.id
								WHERE u.uname = {email}
								GROUP BY u.id, je.subject_categories, je.taxon_categories',*/
					'SQL' => 'SELECT * FROM pjs."spGetJournalUserData"(' . (int)$lJournalId . ', {email}, {mode}, {role})',
					'CheckCaptcha' => 0,
					'DisplayName' => '',
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
				'cancel' => array(
					'CType' => 'action',
					'DisplayName' => 'Cancel',
					'RedirUrl' => $lRedirUrl,
					'ActionMask' => ACTION_REDIRECT,
				),
				'save' => array(
					'CType' => 'action',
					'SQL' => '
						SELECT 
							(CASE WHEN new_user_id IS NOT NULL THEN new_user_id ELSE user_exists END) as guid, 
							upass, 
							event_id 
						FROM spSaveNewJournalUser(
							{guid},
							{journal_id},
							{email},
							{usrtitle},
							{firstname},
							{lastname},
							{affiliation},
							{city},
							{country},
							(CASE WHEN {user_roles} IS NOT NULL THEN {user_roles}::int[] END), 
							{alerts_subject_cats}, 
							{alerts_taxon_cats}, 
							{alerts_geographical_cats},
							{mode},
							' . $this->GetUserId() . ',
							{role}
						)', 
					'CheckCaptcha' => 0,
					'DisplayName' => 'save',
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH,// | ACTION_REDIRECT,
					'AddTags' => array(
						'onclick' => 'enableAllInputs(\'create_user_form\');',
					),
				)
			)
		);
		
		$lSubject = $this->m_Categories_Controller->getCategoriesAndAutocomplete('subjects_tree', 'subjects_tree_script', 'alerts_subject_cats', 'subject_categories');
		$lTaxon   = $this->m_Categories_Controller->getCategoriesAndAutocomplete('taxon_tree', 'taxon_tree_script', 'alerts_taxon_cats', 'taxon_categories');
		$lGeographical = $this->m_Categories_Controller->getCategoriesAndAutocomplete('geographical_tree', 'geographical_tree_script', 'alerts_geographical_cats', 'geographical_categories');

		$lForm = new createUser_Form_Wrapper(array_merge($lBaseFormArr, $lSubject, $lTaxon, $lGeographical));
		$pViewPageObjectsDataArray['contents'] = $lForm;
		$pViewPageObjectsDataArray['big_right_col'] = $lRightCol;
		if((int)$lAjax){
			$lAjaxResult = new pCreate_User_Ajax_Page_View($pViewPageObjectsDataArray);
			$this->m_pageView = new epPage_Json_View(array('html' => $lAjaxResult->Display()));
		}else{
			$this->AddJournalObjects($lJournalId);
			$this->m_pageView = new pCreate_User_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
			$this->InitLeftcolObjects();
		}
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
	
}

?>