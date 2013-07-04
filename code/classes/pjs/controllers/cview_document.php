<?php
/**
 * A controller used to display the view document page
 * @author peterg
 *
 */
class cView_Document extends cBase_Controller {
	/**
	 * The journal id of the journal whose dashboard the user is viewint
	 *
	 * @var int
	 */
	var $m_journalId;
	/**
	 * The viewing roles of the user (author, section editor, etc)
	 *
	 * @var int
	 */
	var $m_viewingRole;
	/**
	 * The id of the document which is being viewed
	 *
	 * @var int
	 */
	var $m_documentId;

	var $m_documentData;
	/**
	 * The document state id
	 *
	 * @var int
	 */
	var $m_documentState;
	var $m_errMsgs = array();
	var $m_errCnt = 0;

	/**
	 * A reference to the controller for the specific role
	 * @var cView_Document
	 */
	var $m_innerController;
	
	/**
	 * 'Content' object
	 * @var object
	 */
	var $m_contentObject;
	
	/**
	 * Which tab is opened (Current status, Metadata, Submitted files etc...)
	 * @var int
	 */
	 var $m_section;
	 
	 /**
	 * 'tabs' object
	 * @var object
	 */
	var $m_tabsObject;
	
	/**
	 * 'document_info' object
	 * @var object
	 */
	var $m_documentInfoObject;
	
	/**
	 * this is variable that contains History tab data
	 * @var array()
	 */
	var $m_HistoryData = array();
	
	/**
	 * has history flag
	 * @var integer
	 */
	var $m_HasHistory = 0;
	
	function __construct() {
		parent::__construct();
		$this->RedirectIfNotLogged();
		
		// events
		$lEventIdData = $this->GetValueFromRequest('event_id', 'GET', 'int', false, false);
		$this->m_commonObjectsDefinitions['event_ids'] = $lEventIdData['value'];
		
		// Editor Redirect
		$lERedirect = $this->GetValueFromRequest('e_redirect', 'GET', 'int', false, false);
		$lDocumentId = $this->GetValueFromRequest('id', 'GET', 'int', false, false);
		
		$lRoleRedirect = $this->GetValueFromRequest('role_redirect', 'GET', 'int', false, false);
		
		if((int)$lERedirect['value']){
			$this->m_commonObjectsDefinitions['url_redirect'] = '/view_document.php?id=' . $lDocumentId['value'] . '&view_role=' . JOURNAL_EDITOR_ROLE;
		} elseif((int)$lRoleRedirect['value']) {
			$this->m_commonObjectsDefinitions['url_redirect'] = '/view_document.php?id=' . $lDocumentId['value'] . '&view_role=' . (int)$lRoleRedirect['value'];
		} else {
			$this->m_commonObjectsDefinitions['url_redirect'] = '';
		}
		
		$this->InitObjects();
		
	}

	function InitObjects(){
		$this->InitData();
		
		if($this->m_commonObjectsDefinitions['url_redirect'] == '' && in_array($this->m_viewingRole, array(PUBLIC_REVIEWER_ROLE, COMMUNITY_REVIEWER_ROLE))) {
			header('Location: /view_document.php?id=' . $this->m_documentId . '&view_role=' . DEDICATED_REVIEWER_ROLE);
			exit;
		}
		
		$pViewPageObjectsDataArray = array();
		
		if($this->m_errCnt){
			$pViewPageObjectsDataArray['contents'] = new evList_Display(array(
				'name_in_viewobject' => 'errors',
				'controller_data' => $this->m_errMsgs
			));
		}
		
		$this->m_pageView = new pView_Document(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		
		if(! $this->m_errCnt){
			switch ($this->m_viewingRole) {
				default:
				case (int) AUTHOR_ROLE :
					$this->m_innerController = new cView_Document_Author();
					// 					$this->InitAuthorObjects();
					break;
				case (int) JOURNAL_EDITOR_ROLE :
					$this->m_innerController = new cView_Document_Editor();
					// 					$this->InitEditorObjects();
					break;
				case (int) SE_ROLE :
					$this->m_innerController = new cView_Document_SE();
					// 					$this->InitSEObjects();
					break;
				case (int) LE_ROLE :
					$this->m_innerController = new cView_Document_LE();
					// 					$this->InitLEObjects();
					break;
				case (int) CE_ROLE :
					$this->m_innerController = new cView_Document_CE();
					// 					$this->InitCEObjects();
					break;
				case (int) DEDICATED_REVIEWER_ROLE :
					$this->m_innerController = new cView_Document_Dedicated_Reviewer();
					// 					$this->InitDedicatedReviewerObjects();
					break;
			}
		}
	}

	function InitData() {
		$lDocumentIdData = $this->GetValueFromRequest('id', 'GET', 'int', false, false);
		$lDocumentId = $lDocumentIdData['value'];
		if($lDocumentIdData['err_cnt'] || ! $lDocumentId){
			$this->Redirect('/index.php');
		}
		$this->m_documentId = (int) $lDocumentId;

		$this->m_viewingRole = (int) $this->GetValueFromRequestWithoutChecks('view_role');
		$lViewData = $this->CheckIfUserCanViewDocument($this->m_documentId, $this->m_viewingRole);
		
		$this->m_errCnt = $lViewData['err_cnt'];
		$this->m_errMsgs = $lViewData['err_msgs'];
	}
	
	/**
		* function GetMetadata
		* This is common Method that creates $this->m_contentObject (Metadata tab)
		*
		* @return void
	*/
	function GetMetadata() {
		$lDocumentModel = new mDocuments_Model();
		
		$lSubmittedDateObj = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => 'submitted_date_obj',
		));
		
		$lAuthorsList = new evList_Display(array(
			'controller_data' => $lDocumentModel->GetDocumentAuthorsList($this->m_documentId),
			'name_in_viewobject' => 'authors_list',
		));
		
		$lAbstractKeyWordsData = $lDocumentModel->GetDocumentAbstractAndKeyWords($this->m_documentId);
	
		if($lAbstractKeyWordsData['abstract']) {
			$lAbstarctObj = new evSimple_Block_Display(array(
				'controller_data' => $lAbstractKeyWordsData,
				'name_in_viewobject' => 'abstract',
			));
		}
		
		if($lAbstractKeyWordsData['keywords']) {
			$lKeyWordsObj = new evSimple_Block_Display(array(
				'controller_data' => $lAbstractKeyWordsData,
				'name_in_viewobject' => 'keywords',
			));
		}
		
		$lHasAbstractKeyWordsData = 0;
		if($lAbstractKeyWordsData['abstract'] || $lAbstractKeyWordsData['keywords']) {
			$lHasAbstractKeyWordsData = 1;
			$lAbstarctAndKeywordsObj = new evSimple_Block_Display(array(
				'controller_data' => array(),
				'name_in_viewobject' => 'abstract_keywords',
				'abstract_obj' => $lAbstarctObj,
				'keywords_obj' => $lKeyWordsObj,
			));
		}
		
		//var_dump($lDocumentModel->GetDocumentIndexedTerms($this->m_documentId));
		
		$IndexedTermsObj = new evSimple_Block_Display(array(
			'controller_data' => $lDocumentModel->GetDocumentIndexedTerms($this->m_documentId),
			'name_in_viewobject' => 'indexed_terms',
			'abstract_obj' => $lAbstarctObj,
			'keywords_obj' => $lKeyWordsObj,
			'has_abstractkeyworddata' => $lHasAbstractKeyWordsData,
		));
		
		$this->m_contentObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => 'metadata_section',
			'document_id' => $this->m_documentId,
			'tabs' => $this->m_tabsObject,
			'document_info' => $this->m_documentInfoObject,
			'submitted_date_obj' => $lSubmittedDateObj,
			'has_tabs' => 1,
			'authors_list' => $lAuthorsList,
			'abstract_keywords' => $lAbstarctAndKeywordsObj,
			'indexed_terms' => $IndexedTermsObj,
		));
	}
	
	/**
		* function GetHistory
		* This is common Method that creates $this->m_contentObject object (History tab).
	 	* If there must be common history objects, just add them here. 
		*
		* @return void
	*/
	function GetHistory() {
		$this->m_contentObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => 'history_section',
			'document_id' => $this->m_documentId,
			'tabs' => $this->m_tabsObject,
			'document_info' => $this->m_documentInfoObject,
			'has_tabs' => 1,
		));
	}

	/**
		* function GetHistory
		* This is common Method that creates $this->m_contentObject object (History tab)
		*
		* @return void
	*/
	function GetSubmittedFiles() {
		$lDocumentModel = new mDocuments_Model();
	
		/**
		 * Ended document review rounds
		 */
		$lSubmittedFiles = new evList_Display(array(
			'controller_data' => $lDocumentModel->GetDocumentSubmittedFilesList($this->m_documentId),
			'name_in_viewobject' => 'submitted_files_list',
		));	
	
		$this->m_contentObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => 'submitted_files_section',
			'document_id' => $this->m_documentId,
			'tabs' => $this->m_tabsObject,
			'has_tabs' => 1,
			'document_info' => $this->m_documentInfoObject,
			'submitted_files_list' => $lSubmittedFiles,
		));
	}

	/**
	* Tabs object
	*/
	function GetSectionTabsObjByRole($pRole) {
		global $user;
		
		$lNameInViewObject = '';
		$lDocumentModel = new mDocuments_Model();
		
		switch ($pRole) {
			default:
			case (int) AUTHOR_ROLE :
				$lNameInViewObject = 'author_tabs';
				// get author history data
				$this->m_HistoryData = $lDocumentModel->GetDocumentReviewRounds($this->m_documentId);
				if(count($this->m_HistoryData)) {
					$this->m_HasHistory = 1;
				}
				break;
			case (int) JOURNAL_EDITOR_ROLE :
				$lNameInViewObject = 'e_tabs';
				break;
			case (int) SE_ROLE :
				$lNameInViewObject = 'se_tabs';
				$this->m_HistoryData = $lDocumentModel->GetDocumentReviewRounds($this->m_documentId);
				if(count($this->m_HistoryData)) {
					$this->m_HasHistory = 1;
				}
				break;
			case (int) LE_ROLE :
				$lNameInViewObject = 'le_tabs';
				break;
			case (int) CE_ROLE :
				$lNameInViewObject = 'ce_tabs';
				$this->m_HistoryData = $lDocumentModel->GetCEDataList($this->m_documentId, $this->GetUserId());
				if(count($this->m_HistoryData) > 1) {
					$this->m_HasHistory = 1;
				}
				break;
			case (int) DEDICATED_REVIEWER_ROLE :
				$this->m_HistoryData = $lDocumentModel->GetReviewerData($this->m_documentId, $user->id);
				if(count($this->m_HistoryData)) {
					$this->m_HasHistory = 1;
				}
				$lNameInViewObject = 'r_tabs';
				break;
		}
		 
		$this->m_tabsObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => $lNameInViewObject,
			'view_role' => $pRole,
			'active_tab' => (int)$this->m_section,
			'has_history' => (int)$this->m_HasHistory,
		));
	} 
	
	/**
	 * document_info object content
	 * */
	function GetDocumentInfoObject() {
		$this->m_documentInfoObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => 'document_info',
			'state_id' => $this->m_documentState,
			'role' => $this->m_viewingRole,
		));
	}

	protected function GetDocumentInfo(){
		$lDocumentIdData = $this->GetValueFromRequest('id', 'GET', 'int', false, false);
		$this->m_documentId = (int) $lDocumentIdData['value'];
		$lDocumentModel = new mDocuments_Model();
		$this->m_documentData = $lDocumentModel->GetDocumentInfo($this->m_documentId, $this->m_viewingRole);
		$this->m_documentState = $this->m_documentData['state_id'];
		$this->m_journalId = $this->m_documentData['journal_id'];
	}

	protected function GetActiveSection() {
		$this->m_section = (int)$this->GetValueFromRequestWithoutChecks('section');
		
		$lTabsArr = array();
		
		switch ($this->m_viewingRole) {
			default:
			case (int) AUTHOR_ROLE :
				$lTabsArr = array(
					GET_CURSTATE_MANUSCRIPT_SECTION,
					GET_METADATA_SECTION,
					GET_SUBMITTED_FILES_SECTION,
					GET_HISTORY_SECTION
				);
				break;
			case (int) JOURNAL_EDITOR_ROLE :
				$lTabsArr = array(
					GET_CURSTATE_MANUSCRIPT_SECTION,
					GET_METADATA_SECTION,
					GET_SUBMITTED_FILES_SECTION,
					GET_DISCOUNTS_SECTION,
					GET_SCHEDULING_SECTION
				);
				break;
			case (int) SE_ROLE :
				$lTabsArr = array(
					GET_CURSTATE_MANUSCRIPT_SECTION,
					GET_METADATA_SECTION,
					GET_SUBMITTED_FILES_SECTION,
					GET_HISTORY_SECTION
				);
				break;
			case (int) LE_ROLE :
				$lTabsArr = array(
					GET_CURSTATE_MANUSCRIPT_SECTION,
					GET_METADATA_SECTION,
					GET_SUBMITTED_FILES_SECTION,
					GET_DISCOUNTS_SECTION,
					GET_SCHEDULING_SECTION,
				);
				break;
			case (int) CE_ROLE :
				$lTabsArr = array(
					GET_CURSTATE_MANUSCRIPT_SECTION,
					GET_METADATA_SECTION,
					GET_HISTORY_SECTION
				);
				break;
			case (int) DEDICATED_REVIEWER_ROLE :
				$lTabsArr = array(
					GET_CURSTATE_MANUSCRIPT_SECTION,
					GET_METADATA_SECTION,
					GET_HISTORY_SECTION
				);
				break;
		}
		
		$this->m_section = (in_array($this->m_section, $lTabsArr) ? $this->m_section : GET_CURSTATE_MANUSCRIPT_SECTION); 
	}

	/**
	 * CheckWaitingReviewers
	 * This function checks if the current round conditions are fullfilled from document round reviewers
	 * 
	 * @param $pData - reviewers data
	 * @param $pRoundNumber - round number
	 * @return true/false
	 */
	function CheckWaitingReviewers($pData, $pRoundNumber) {
		$lWaitingReviewerFlag = false;
		$lHasFinishedReview = false;
		
		foreach ($pData as $key => $value) {
			if((int)$value['decision_id']) {
				$lHasFinishedReview = true;
			}
			if(
				!(int)$value['decision_id'] && 
				(!in_array((int)$value['invitation_state'], array(REVIEWER_CANCELLED_STATE, REVIEWER_CANCELLED_BY_SE_STATE))) &&
				((int)$value['usr_state'] != REVIEWER_REMOVED)
			) {
				$lWaitingReviewerFlag = true;
			}
			
			/**
			 * break foreach - there is no point in doing any checks for the other reviewers if there is waiting reviewer
			 */
			if($lWaitingReviewerFlag) {
				break;
			}
		}
		
		if($pRoundNumber == REVIEW_ROUND_ONE) {
			$lReviewersCheck = (($lHasFinishedReview && !$lWaitingReviewerFlag) ? true : false);
			return $lReviewersCheck;	
		} elseif ($pRoundNumber == REVIEW_ROUND_TWO) {
			$lReviewersCheck = (!$lWaitingReviewerFlag ? true : false);
			
			if(count($pData)) {
				if($lReviewersCheck) {
					return true;
				} else {
					return false;
				}
			}
			return true;
			/*if(count($pData) && $lReviewersCheck) {
				return true;
			}
			return false;*/
		}
		return true;
	}

	/**
	 * If there are errors - we display them.
	 *
	 */
	function Display(){
		if($this->m_errCnt){
			return parent::Display();
		}
// 		var_dump($this->m_innerController->Display());
		return $this->m_innerController->Display();
	}
}

?>