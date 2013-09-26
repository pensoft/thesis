<?php
// Disable error reporting because it can break the json output
//ini_set('error_reporting', 'off');

class cReview_Ajax_Srv extends cBase_Controller {
	var $m_errCnt = 0;
	var $m_errMsgs = array();
	var $m_action;
	var $m_action_result;
	var $m_eventsParamString = '';
	function __construct() {
		parent::__construct();
		$pViewPageObjectsDataArray = array();
		
		$lReviewTypeId = $this->GetValueFromRequestWithoutChecks('data');
		$lReviewTypeId = explode('review_type=', $lReviewTypeId);
		$lReviewTypeId = $lReviewTypeId[1];
		$lDocumentId = (int)$this->GetValueFromRequestWithoutChecks('document_id');  

		$this->updateDocumentReviewType($lDocumentId, $lReviewTypeId);
		
		$lResultArr = array_merge($this->m_action_result, array(
			'err_cnt' => $this->m_errCnt,
			'err_msgs' => $this->m_errMsgs,
			'url_params' => $this->m_eventsParamString, 
		));
		$this->m_pageView = new epPage_Json_View($lResultArr);
	}
	function updateDocumentReviewType($pDocumentId, $pReviewTypeId) {
		$lDocumentsModel = new mDocuments_Model();
		$this->m_action_result = $lDocumentsModel->UpdateDocumentReviewType($pDocumentId, $pReviewTypeId);
	}	
}

?>