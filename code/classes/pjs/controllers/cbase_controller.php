<?php

/**

 * A base controller class for this project.
 *
 * It contains the definitions for the common objects for all the pages
 * @author peterg
 *
 */
class cBase_Controller extends  ecBase_Controller_User_Data{
	/**
	 * The journal id of the journal whose dashboard the user is viewint
	 *
	 * @var int
	 */
	var $m_journalId;
	/**
	 * The viewing mode of the user (author, section editor, etc)
	 *
	 * @var int
	 */
	var $m_viewingMode;
	/**
	 * An array containing the allowed viewieng modes for the specified user
	 * for the specified journal dashboard.
	 * The format of the array is
	 * role_id => an array containing all the viewing roles for the specific
	 * role
	 *
	 * @var array
	 */
	var $m_allowedViewingModes;

	/**
	 * The role of the user
	 *
	 * @var int
	 */
	var $m_viewingRole;
	
	function __construct(){
		/*error_reporting(E_ALL);
		ini_set("display_errors", 1);*/
		parent::__construct();
		$this->IncludeInHead();
		
		if(isset($_COOKIE['h_cookie']) && !$this->GetUserId()){
			$lCurrentPHP = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
			$lLoginResult = $this->ProcessAutoLoginRequest($_COOKIE['h_cookie'], true, $lCurrentPHP);
		}
		
		//~ $this->m_models['stories_model'] = new mStories_Model();
		$this->m_models['menu_model'] = new mMenu_Model();
		$this->m_models['mBrowse_Model'] = new mBrowse_model();

		$this->m_commonObjectsDefinitions['mainmenu'] = array(
				'ctype' => 'evList_Recursive_Display',
				'recursivecolumn'=>'parentid',
				'name_in_viewobject' => 'mainmenu',
				'templadd'=>'type',
				'controller_data' => $this->m_models['menu_model']->GetMenuContentsList(MAIN_MENU_ID, getlang()),
		);
		$this->m_commonObjectsDefinitions['aof_mainmenu'] = array(
				'ctype' => 'evList_Recursive_Display',
				'recursivecolumn'=>'parentid',
				'name_in_viewobject' => 'mainmenu',
				'templadd'=>'type',
				'controller_data' => $this->m_models['menu_model']->GetMenuContentsList(AOF_MAIN_MENU_ID, getlang()),
		);
		global $user;
		$lPreviewpicid = max((int)$this->m_user->photo_id, 
		                     (int)$user->photo_id);
		if($user->id && $user->staff){
		
			$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
			$lFieldsMetadataTempl = array(
				'stext' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => true,
				),
				'search_in' => array(
					'VType' => 'int',
					'CType' => 'radio',
					'SrcValues' => array(
						0 => 'All',
						1 => 'Author',
						2 => 'Title',
					),
					'AllowNulls' => true,
				),
				'sortby' => array(
					'CType' => 'hidden',
					'VType' => 'int',
					'AllowNulls' => true,
					'DefValue' => (int)$_REQUEST['sortby'],
					'AddTags' => array(
						'id' => 'article_search_sortby',
					),
				),
				'search' => array(
					'CType' => 'action',
					'DisplayName' => '',
					'ActionMask' =>  ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW
				),
			);
			
			$this->m_commonObjectsDefinitions['article_search'] = array(
				'ctype' => 'eForm_Wrapper',
				'page_controller_instance' => $this,
				'name_in_viewobject' => 'article_search_form_templ',
				'use_captcha' => 0,
				'form_method' => 'POST',
				'form_action' => '/browse_journal_articles?journal_id=' . (int)$lJournalId,
				'js_validation' => 0,
				'form_name' => 'article_search',
				'dont_close_session' => true,
				'fields_metadata' => $lFieldsMetadataTempl,
				'htmlformid' => 'article_search',
			);
		}
		if($this->GetUserId()){
			$this->m_commonObjectsDefinitions['login_register_or_profile'] = array(
				'ctype' => 'evSimple_Block_Display',
				'name_in_viewobject' => 'profile_template',
				'fullname' => $this->m_user->fullname,
				'previewpicid' => $lPreviewpicid,
				'controller_data' => '',
			);
		}else{
			$this->m_commonObjectsDefinitions['login_register_or_profile'] = array(
				'ctype' => 'evSimple_Block_Display',
				'name_in_viewobject' => 'login_or_register',
			);
		}
		
	}

	/**
	 * Returns an array containing all the allowed viewing modes. The resulting array has the following format
	 * 		role_id => an array containing all the allowed modes for the specific role
	 *
	 * @param $pJournalId int
	 */
	function GetUserJournalDashboardAllowedViewingModes($pJournalId) {
		if(! $this->GetUserId()){
			return array();
		}
		$this->InitUserModel();

		/**
		 * A reference to the user model
		 *
		 * @var mUser_Model
		 */
		$lUserModel = $this->m_models['user_model'];
		$lUserModel = new mUser_Model();
		$lAllowedRoles = $lUserModel->GetUserJournalAllowedRoles($this->GetUserId(), $pJournalId);
		//print_r($lAllowedRoles);
		$lResult = array();
		if(in_array(AUTHOR_ROLE, $lAllowedRoles)){

			$lResult[AUTHOR_ROLE]= array(
				DASHBOARD_AUTHOR_PENDING_VIEWMODE,
				DASHBOARD_AUTHOR_PUBLISHED_VIEWMODE,
				DASHBOARD_AUTHOR_REJECTED_VIEWMODE,
				DASHBOARD_AUTHOR_INCOMPLETE_VIEWMODE,
			);
		}

		if(in_array(JOURNAL_EDITOR_ROLE, $lAllowedRoles)){
			$lResult[JOURNAL_EDITOR_ROLE]= array(
			    DASHBOARD_EDITOR_PENDING_ALL_VIEWMODE,
				DASHBOARD_EDITOR_PENDING_UNASSIGNED_VIEWMODE,
				DASHBOARD_EDITOR_PENDING_IN_REVIEW_VIEWMODE,
				DASHBOARD_EDITOR_PENDING_IN_COPY_EDIT_VIEWMODE,
				DASHBOARD_EDITOR_PENDING_IN_LAYOUT_VIEWMODE,
				DASHBOARD_EDITOR_PENDING_READY_FOR_PUBLISHING_VIEWMODE,
				DASHBOARD_EDITOR_PUBLISHED_VIEWMODE,
				DASHBOARD_EDITOR_REJECTED_VIEWMODE,
			);
		}

		if(in_array(SE_ROLE, $lAllowedRoles)){
			$lResult[SE_ROLE] = array(
				DASHBOARD_SE_IN_REVIEW_VIEWMODE,
				DASHBOARD_SE_IN_PRODUCTION_VIEWMODE,
				DASHBOARD_SE_PUBLISHED_VIEWMODE,
				DASHBOARD_SE_REJECTED_VIEWMODE,
			);
		}

		if(in_array(DEDICATED_REVIEWER_ROLE, $lAllowedRoles)){
			$lResult[DEDICATED_REVIEWER_ROLE] = array(
				//DASHBOARD_DEDICATED_REVIEWER_REQUESTS_VIEWMODE,
				DASHBOARD_DEDICATED_REVIEWER_PENDING_VIEWMODE,
				DASHBOARD_DEDICATED_REVIEWER_PENDING_ARCHIVED_VIEWMODE,
			);
		}

		if(in_array(CE_ROLE, $lAllowedRoles)){
			$lResult[CE_ROLE] = array(
				DASHBOARD_COPY_EDITOR_PENDING_VIEWMODE,
				DASHBOARD_COPY_EDITOR_ARCHIVED_VIEWMODE,
			);
		}		
		
		if(in_array(LE_ROLE, $lAllowedRoles)){
			$lResult[LE_ROLE] = array(
				DASHBOARD_LAYOUT_PENDING_VIEWMODE,
				DASHBOARD_LAYOUT_READY_VIEWMODE,
				DASHBOARD_LAYOUT_PUBLISHED_VIEWMODE,
				DASHBOARD_LAYOUT_STATISTICS_VIEWMODE,
			);
		}
		if(in_array(JOURNAL_MANAGER_ROLE, $lAllowedRoles)){
			$lResult[JOURNAL_MANAGER_ROLE] = array(
			);
		}	

		return $lResult;
	}

	/**
	 * Checks if the current user can view the specified document(and version if
	 * specified) in the specified mode
	 *
	 * @param $pDocumentId unknown_type
	 * @param $pViewingRole unknown_type
	 * @param $pVersionId unknown_type
	 */
	function CheckIfUserCanViewDocument($pDocumentId, $pViewingRole) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		if(! $this->GetUserId()){
			$lResult['err_cnt'] ++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr('pjs.onlyLoggedUsersCanViewThisDocument')
			);
		}
		$this->InitUserModel();
		/**
		 *
		 * @var mUser_Model
		 */
		$lUserModel = $this->m_models['user_model'];
		$lUserModel = new mUser_Model();
		$lModelResult = $lUserModel->CheckIfUserCanViewDocument($this->GetUserId(), $pDocumentId, $pViewingRole);

		return $lModelResult;
	}

	function InitLeftcolObjects() {
		$lLeftCol = new evSimple_Block_Display(array(
			'name_in_viewobject' => 'dashboard_leftcol',
			'journal_id' => $this->m_journalId
		));
		
		$lYourTasks = new evSimple_Block_Display(array(
				'controller_data' => array(),
				'name_in_viewobject' => 'your_tasks_leftcol',
				'journal_id' => $this->m_journalId
			));
		$lLeftCol->SetVal('your_tasks', $lYourTasks);
		foreach (array('author', 'journal_manager', 'journal_editor', 'dedicated_reviewer', 'se', 'ce', 'le') as $key => $value) {
			$lLeftCol->SetVal($value, '');
		}
		
		$this->m_models['dashboard'] = new mDashboard();
		
		$lViewModeCounts = $this->m_models['dashboard']->GetViewModeCounts($this->m_journalId, array_keys($this->m_allowedViewingModes), $this->GetUserId());
		$new_button =  '+';// '<img src="http://test.pwt.pensoft.eu/i/add_icon.png" alt="new" title="$newtitle">';
		foreach($this->m_allowedViewingModes as $lRole => $lAllowedModes){
			$lLabel = '';
			$lControlerData = array();
			$header = 'aham';
			switch ($lRole) {
				case JOURNAL_MANAGER_ROLE:
					$lLabel = 'journal_manager';
					$header = 'pjs.dashboards.MyJournalWideTasks';
					array_push($lControlerData, array(
							'href'  => '/manage_journal_prices',
							'href2' => '/manage_journal_prices',
							'text'  => getstr('Prices'), 
							'text2' => '', ));
					array_push($lControlerData, array(
							'href'  => '/manage_journal_users',
							'text'  => getstr('Users'), 
							'href2' => '/create_user',
							'text2' => $new_button /*getstr('New User'), )); */	));
					array_push($lControlerData, array(
							'href'  => '/manage_journal_sections',
							'text'  => getstr('Sections'),	
							'href2' => '/edit_journal_section?tAction=showedit',
							'text2' => $new_button));
					array_push($lControlerData, array( 
							'href'  => '/manage_journal_about_pages',
							'text'  => getstr('About pages'), 
							'href2' => '/edit?tAction=showedit',
							'text2' => $new_button));
					array_push($lControlerData, array( 
							'href'  => 'manage_journal_documents',
							'href2' => 'manage_journal_documents',
							'text'  => getstr('Articles'),  
							'text2' => ''));
					array_push($lControlerData, array( 
							'href'  => 'manage_journal_groups',
							'href2' => 'edit_journal_group?tAction=showedit',
							'text'  => getstr('pjs.editorial_team'),  
							'text2' => $new_button));
					array_push($lControlerData, array( 
							'href'  => 'email_templates',
							'href2' => 'email_templates',
							'text'  => getstr('Email Templates'),  
							'text2' => ''));
					break;	
				case AUTHOR_ROLE :
					$lLabel = 'author';
					$header = 'pjs.dashboards.MyManuscripts';
					break;
				case JOURNAL_EDITOR_ROLE :
					$lLabel = 'journal_editor';
					$header = 'pjs.dashboards.SubmissionsE';
					array_push($lControlerData, array(
						'href'  => '/manage_journal_issues.php?journal_id=' . $this->m_journalId,
						'text'  => getstr('pjs.dashboards.e.FutureIssues'),
						'href2' => '/edit_journal_issue.php?journal_id='.$this->m_journalId,
						'text2' => $new_button // getstr('pjs.dashboards.e.CreateIssue'),
					));
					array_push($lControlerData, array(
						'href'  => '/manage_journal_issues.php?back_issue=1&amp;journal_id=' . $this->m_journalId,
						'href2' => '/manage_journal_issues.php?back_issue=1&amp;journal_id=' . $this->m_journalId,
						'text'  => getstr('pjs.dashboards.e.PastIssues'),
						'text2' => '',
					));
					break;
				case SE_ROLE :
					$lLabel = 'se';
					$header = 'pjs.dashboards.SubmissionsSE';
					break;
				case DEDICATED_REVIEWER_ROLE :
					$lLabel = 'dedicated_reviewer';
					$header = 'pjs.dashboards.MyReviews';
					break;
				case CE_ROLE:
					$lLabel = 'ce';
					$header = 'pjs.dashboards.MyCopyEdits';
					break;
				case LE_ROLE:
					$lLabel = 'le';
					$header = 'pjs.dashboards.MyLayouts';
					break;
			}
			$lNameInViewObject = $lLabel . '_leftcol';

			foreach ($lAllowedModes as $m => $viewMode) {
				$href = "/dashboard?view_mode=$viewMode";
				array_push($lControlerData,
					array(
						'href' => $href,
						'text' => getstr('pjs.dashboards.viewmode'. $viewMode),
						'href2' => $href,
						'text2' => array_key_exists($viewMode, $lViewModeCounts) ? $lViewModeCounts[$viewMode]: 0 
					));
			}
			
			$lObject = new evList_Display(array(
				'controller_data' => $lControlerData,
				'header' => $header,
				'name_in_viewobject' => $lNameInViewObject,
				'journal_id' => $this->m_journalId
			));

			$lLeftCol->SetVal($lLabel, $lObject);

		}
		$this->m_pageView->SetVal('leftcol', $lLeftCol);
	}
	
	function InitViewingModeData() {
		$lJournalIdData = $this->GetValueFromRequest('journal_id', 'GET', 'int', false, false);
		$lJournalId = $lJournalIdData['value'];
		if($lJournalIdData['err_cnt'] || ! $lJournalId){
			$this->Redirect('/journals.php');
		}
		$this->m_journalId = $lJournalId;
		$this->m_allowedViewingModes = $this->GetUserJournalDashboardAllowedViewingModes($this->m_journalId);

		if(! count($this->m_allowedViewingModes)){
			$this->Redirect('/login.php');
		}

		$this->m_viewingMode = (int) $this->GetValueFromRequestWithoutChecks('view_mode');

		foreach($this->m_allowedViewingModes as $lRoleId => $lRoleViewingModes){
			if(in_array($this->m_viewingMode, $lRoleViewingModes)){
				$this->m_viewingRole = $lRoleId;
				return;
			}
		}
// 		var_dump($this->m_allowedViewingModes);

		// The current viewing mode is not allowed - get the first available
		// role and mode
		$lRoles = array_keys($this->m_allowedViewingModes);
		$this->m_viewingRole = $lRoles[0];
		$this->m_viewingMode = DASHBOARD_YOUR_TASKS_VIEWMODE; //$this->m_allowedViewingModes[$this->m_viewingRole][0];

	}

	function AddJournalObjects($pJournalId){
		if((int)$pJournalId){
			$lYourTasks = 0;
			//Show your tasks?
			if($this->GetUserId()){
				$this->InitUserModel();
				$lAllowedRoles = $this->m_models['user_model']->GetUserJournalAllowedRoles($this->GetUserId(), $pJournalId);
				if(count($lAllowedRoles) > 0){
					$lYourTasks = 1;
				}
			}
			
			$this->m_commonObjectsDefinitions['journal_header'] = array(
				'ctype' => 'evSimple_Block_Display',
				'name_in_viewobject' => 'journal_header_templ',
				'journal_menu' => array(
					'ctype' => 'evList_Recursive_Display',
					'recursivecolumn'=>'parentid',
					'name_in_viewobject' => 'journal_menu',
					'templadd'=>'type',
					'controller_data' => $this->m_models['menu_model']->GetJournalMenuContentList((int)$pJournalId, getlang()),
				),
				'article_search' => $this->m_commonObjectsDefinitions['article_search'],
				'show_your_tasks' => $lYourTasks,
				'journal_id' => $pJournalId,
				'controller_data' => '',
			);
		}
	}
	function head_CSS_files(){ return array('def', 'ui.dynatree', 'ui-lightness/jquery-ui-1.8.24.custom');}
	function head_JS_files(){
		return array(	'js/jquery', 
						'js/jquery_ui', 
						'js/jquery.tinyscrollbar.min',
						'js/jquery.dynatree.min', 
						'js/jquery.simplemodal', 
						'js/jquery_form', 
						'js/jquery.tokeninput', 
						'js/jquery.dragsort', 
						'js/ajaxupload.3.5', 
						'js/def', 
						'ckeditor/ckeditor', 
						'ckeditor/adapters/jquery', 
						'js/taskspopup'
						);
	}

	function GetShareMetaTags(){ return '';}
	
	function IncludeInHead()
	{
		$CSS = '';
		$JS = '';
		foreach ($this->head_CSS_files() as $CSS_file)
			$CSS .= css_tag($CSS_file);
		foreach ($this->head_JS_files() as $JS_file)
			$JS  .= js_tag($JS_file);
		$this->m_commonObjectsDefinitions['share_metadata'] = $this->GetShareMetaTags();
		$this->m_commonObjectsDefinitions['CSS'] = $CSS;
		$this->m_commonObjectsDefinitions['JS'] = $JS;
	}
	
	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
	
	function Display() {

		return $this->m_pageView->Display();

	}

}?>