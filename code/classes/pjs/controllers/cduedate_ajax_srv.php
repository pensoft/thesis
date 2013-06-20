<?php
// Disable error reporting because it can break the json output
//ini_set('error_reporting', 'off');

class cDueDate_Ajax_Srv extends cBase_Controller {
	var $m_errCnt = 0;
	var $m_errMsgs = array();
	var $m_action;
	var $m_action_result;
	var $m_eventsParamString = '';
	function __construct() {
		parent::__construct();
		$pViewPageObjectsDataArray = array();
		$this->m_action = $this->GetValueFromRequestWithoutChecks('action');
		$lRoundId = $this->GetValueFromRequestWithoutChecks('roundid');
		$lRoundUserId = $this->GetValueFromRequestWithoutChecks('rounduserid');
		$lDueDate = $this->GetValueFromRequestWithoutChecks('duedate');
		$lDueDate = explode('duedate=', $lDueDate);
		$lDueDate = explode('+', $lDueDate[1]);
		$lDueDate = str_replace('%', '-', $lDueDate[0]);
		$lDueDate = str_replace('2F', '', $lDueDate);
		switch ($this->m_action) {
			default :
				$this->m_errCnt ++;
				$this->m_errMsgs[] = array(
					'err_msg' => getstr('pjs.unrecognizedAction')
				);
				break;
			case 'user_invitation' :
				$this->UpdateReviwerInvitationDueDate($lRoundId, $lRoundUserId, $lDueDate);
				break;
			case 'user_decision' :
				$this->UpdateUserDecisionDueDate($lRoundId, $lRoundUserId, $lDueDate);
				break;
			case 'round_duedate' :
				$this->UpdateRoundDueDate($lRoundId, $lDueDate);
				break;
			case 'reviewers_assignment' :
				$this->UpdateReviwersAssignment($lRoundId, $lRoundUserId, $lDueDate);
				break;

		}
		
		$lResultArr = array_merge($this->m_action_result, array(
			'err_cnt' => $this->m_errCnt,
			'err_msgs' => $this->m_errMsgs,
			'url_params' => $this->m_eventsParamString, 
		));
		$this->m_pageView = new epPage_Json_View($lResultArr);
	}
	function UpdateReviwerInvitationDueDate($pRoundId, $pRoundUserId, $pDueDate) {
		// poper 3
		$lDocumentsModel = new mDocuments_Model();
		$this->m_action_result = $lDocumentsModel->UpdateDocumentDueDates(3, $pRoundId, $pRoundUserId, $pDueDate);
	}
	function UpdateUserDecisionDueDate($pRoundId, $pRoundUserId, $pDueDate) {
		// poper 1 roundid = null
		$lDocumentsModel = new mDocuments_Model();
		$this->m_action_result = $lDocumentsModel->UpdateDocumentDueDates(1, $pRoundId, $pRoundUserId, $pDueDate);
		
	}
	function UpdateRoundDueDate($pRoundId, $pDueDate) {
		// poper 1
		$lDocumentsModel = new mDocuments_Model();
		$this->m_action_result = $lDocumentsModel->UpdateDocumentDueDates(1, $pRoundId, null, $pDueDate);
		
	}
	function UpdateReviwersAssignment($pRoundId, $pRoundUserId, $pDueDate) {
		// poper 2
		$lDocumentsModel = new mDocuments_Model();
		$this->m_action_result = $lDocumentsModel->UpdateDocumentDueDates(2, $pRoundId, $pRoundUserId, $pDueDate);
		
	}
	
}

?>