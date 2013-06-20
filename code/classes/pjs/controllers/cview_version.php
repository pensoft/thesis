<?php
/**
 * A controller used to display the view version page
 * @author peterg
 *
 */
class cView_Version extends cBase_Controller {
	/**
	 * The version id which is currently being displayed
	 *
	 * @var int
	 */
	var $m_versionId;
		/**
	 * The id of the document which is being viewed
	 *
	 * @var int
	 */
	var $m_documentId;
	/**
	 * The type of the document - pwt/files
	 * @var unknown_type
	 */
	var $m_documentSrcType;

	var $m_errMsgs = array();
	var $m_errCnt = 0;

	var $m_versionModel;

	var $m_viewingRole;
	var $m_roundId;
	var $m_DocumentsModel;
	var $m_Result = array();
	var $m_versionType;
	var $m_versionUID;
	var $m_versionUserRoundId;

	/**
	 * A reference to the controller for the specific document type (pwt versions are different than the file versions)
	 * @var cView_Version
	 */
	var $m_innerController;
	function __construct() {
		parent::__construct();
		$this->RedirectIfNotLogged();
		
		$this->m_DocumentsModel = new mDocuments_Model();
		$this->m_versionModel = new mVersions();
		$this->GetVersionInfo();
		$this->GetDocumentInfo();
		
		if(
			(int)$this->m_documentData['merge_flag'] && 
			($this->m_documentData['review_lock'] == 'false' || $this->m_documentData['review_lock'] == 'f') && 
			($this->m_viewingRole == SE_ROLE || $this->m_viewingRole == JOURNAL_EDITOR_ROLE)
		) { // ако трябва правим merge
			$this->MergeReviewersVersions();
		}
		
		if(($this->m_documentData['review_lock'] == 'false' || $this->m_documentData['review_lock'] == 'f') && ($this->m_viewingRole == SE_ROLE || $this->m_viewingRole == JOURNAL_EDITOR_ROLE)) {
			$this->m_DocumentsModel->DisableInvitingUsersForRound((int)$this->m_roundId, (int)$this->m_documentId);
		}
		
		$this->InitObjects();
	}
	
	protected function GetDocumentInfo(){
		$this->m_documentData = $this->m_DocumentsModel->GetDocumentInfo($this->m_documentId, $this->m_viewingRole);
		$lSEDocumentRoundUserIdData = $this->m_DocumentsModel->GetSEDocumentCurrentRoundNumberAndUserId($this->m_documentId, $this->GetUserId());
		$this->m_roundId = $lSEDocumentRoundUserIdData['current_round_id'];
	}
	
	function MergeReviewersVersions(){
		try{
			if ($this->m_roundId){ // Merge versions
				$lMversions = new mVersions();
				if(!$lMversions->CreatePwtEditorVersionFromReviewerVersions($this->m_roundId)){
					$this->m_action_result['err_cnt'] = 0;
					$this->m_errCnt++;
				}	
			}
		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}
	
	function InitObjects(){
		$this->InitData();
		
		$pViewPageObjectsDataArray = array();

		if($this->m_errCnt){
			// var_dump($this->m_errMsgs);
			$pViewPageObjectsDataArray['contents'] = new evList_Display(array(
				'name_in_viewobject' => 'errors',
				'controller_data' => $this->m_errMsgs
			));
		}

		$this->m_pageView = new pView_Version(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));

		if(! $this->m_errCnt){
			switch ($this->m_documentSrcType) {
				default:
				case (int) PWT_DOCUMENT_TYPE :
					$this->m_innerController = new cView_Version_Pwt();
					break;
			}
		}
	}

	function InitData() {
		if(!$this->m_versionId){
			$this->Redirect('/index.php');
		}


		$lPermCheck = $this->CheckIfUserCanViewDocumentVersion();
		if(!$lPermCheck) {
			$this->m_errCnt++;
			$this->m_errMsgs = getstr('pjs.noperms_opening_current_version');	
		}
	}

	function CheckIfUserCanViewDocumentVersion(){
		$lViewData = $this->m_versionModel->CheckIfUserCanViewDocumentVersion($this->m_documentId, $this->m_versionId, $this->GetUserId());
		return (int)$lViewData['result']; 
	}

	/**
	 * GetVersionInfo()
	 * This method gets some document and version information. 
	 * Also checks if the user that opens this version is panel reviewer and if it is, 
	 * then add a new version for him (panel reviewer has no version at the begining...)  
	 * 
	 * 
	 */
	protected function GetVersionInfo(){
		$lVersionIdData = $this->GetValueFromRequest('version_id', 'GET', 'int', false, false);
		$this->m_versionId = (int) $lVersionIdData['value'];
		
		/* This is for panel reviewers
		 * We add them a version and then redirect to their version
		 * 
		 * */
		$lPanelData = $this->GetValueFromRequest('panel', 'GET', 'int', false, false);
		$lPanel = (int)$lPanelData['value'];
		$lDocumentIdData = $this->GetValueFromRequest('id', 'GET', 'int', false, false);
		$lDocumentId = (int)$lDocumentIdData['value'];

		if((int)$lDocumentId && !$this->m_versionId) {
			$lPanelReviewerData = $this->m_DocumentsModel->CheckAndAddPanelReviewer($lDocumentId, $this->GetUserId());
			if($lPanelReviewerData['version_id']){
				$this->Redirect('/view_version.php?version_id=' . $lPanelReviewerData['version_id']);
				exit;
			}
		}

		$lDocumentVersionData = $this->m_versionModel->GetDocumentVersionInfo($this->m_versionId);
		$this->m_documentSrcType = $lDocumentVersionData['document_source_id'];
		$this->m_documentId = $lDocumentVersionData['document_id'];
		$this->m_versionType = (int)$lDocumentVersionData['version_type_id'];
		$this->m_versionUID = (int)$lDocumentVersionData['uid'];
		$this->m_versionUserRoundId = (int)$lDocumentVersionData['version_round_id'];
		//var_dump($this->m_versionUID);
		switch ((int)$this->m_versionType) {
			case DOCUMENT_VERSION_AUTHOR_SUBMIT_TYPE:
				$this->m_viewingRole = AUTHOR_ROLE;
				break;
			case DOCUMENT_VERSION_REVIEWER_TYPE:
			case COMMUNITY_REVIEWER_ROLE:
				$this->m_viewingRole = DEDICATED_REVIEWER_ROLE;
				break;
			case DOCUMENT_VERSION_SE_TYPE:
				$this->m_viewingRole = SE_ROLE;
				break;
			case DOCUMENT_VERSION_LE_TYPE:
				$this->m_viewingRole = LE_ROLE;
			case DOCUMENT_VERSION_CE_TYPE:
				$this->m_viewingRole = CE_ROLE;
				break;
			case DOCUMENT_VERSION_E_TYPE:
				$this->m_viewingRole = E_ROLE;
				break;
			case DOCUMENT_VERSION_PUBLIC_REVIEWER_TYPE:
				$this->m_viewingRole = PUBLIC_ROLE;
				break;
		}
	}

	/**
	 * If there are errors - we display them.
	 *
	 */
	function Display(){
		if($this->m_errCnt){
			return parent::Display();
		}
		
		return $this->m_innerController->Display();
	}
}

?>