<?php

class cInvite_Reviewers_Ajax_Srv extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		
		
		$lDocumentId = (int)$this->GetValueFromRequestWithoutChecks('document_id');
		$lUserId = (int)$this->GetValueFromRequestWithoutChecks('user_id');
		$lOper = (int)$this->GetValueFromRequestWithoutChecks('oper');
		$lInvitationId = (int)$this->GetValueFromRequestWithoutChecks('invitation_id');
		
		$lModel = new mDocuments_Model();
		if((int)$lOper == 1) { // Insert
			$lDocumentInfo = $lModel->GetDocumentInfoForReviewer((int)$lDocumentId);
			$lResult = $lModel->InviteDocumentReviewerByAuthor((int)$lOper, 0, (int)$lDocumentId, (int)$lUserId, (int)$lDocumentInfo['submitting_author_id'], (int)$lDocumentInfo['current_round_id'], 1);
		} elseif((int)$lOper == 2) { // Delete
			$lDocumentInfo = $lModel->GetDocumentInfoForReviewer((int)$lDocumentId);
			$lResult = $lModel->InviteDocumentReviewerByAuthor((int)$lOper, (int)$lInvitationId, (int)$lDocumentId, 0, (int)$lDocumentInfo['submitting_author_id'], 0, 0); // Delete
		}
		
		$this->m_pageView = new epPage_Json_View(array('html' => $lResult));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>