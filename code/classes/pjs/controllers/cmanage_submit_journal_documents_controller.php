<?php

class cManage_Submit_Journal_Documents_Controller extends cBase_Controller {

	var $m_documentId;
	var $m_documentType;
	var $m_journalId;
	var $m_step;
	var $m_errCnt = 0;
	var $m_errMsgs = array();

	var $m_formNameInViewobject = '';
	var $m_formWrapperClass = 'eForm_Wrapper';
	var $m_formFieldsMetadata = array();

	function __construct() {
		global $rewrite;
		parent::__construct();

		$this->RedirectIfNotLogged();
		$this->InitDocumentInfo();

		if(!(int)$this->m_documentType){
// 			var_dump($this->m_errMsgs);
// 			exit;
			$this->Redirect('/index.php');
		}

		$this->ManageSubmittedDocumentType();

		//~ echo $this->m_documentType;
		//~ exit;

		$pViewPageObjectsDataArray = array();

	}
	function SetError($pErrMsg){
		$this->m_errCnt++;
		$this->m_errMsgs[] = $pErrMsg;
	}

	private function InitDocumentInfo() {
		$lDocumentIdData = $this->GetValueFromRequest('document_id', 'GET', 'int', false, false);
		if($lDocumentIdData['err_cnt']){
			$this->Redirect('/index.php');
		}
		$this->m_documentId = $lDocumentIdData['value'];

		$lModel = new mDocuments_Model();
		$lModelResponse = $lModel->GetDocumentInfo($this->m_documentId);

		//~ print_r($lModelResponse);
		//~ exit;

		if($lModelResponse['err_cnt']){
			$this->m_errCnt = $lModelResponse['err_cnt'];
			$this->m_errMsgs = $lModelResponse['err_msgs'];
			return;
		}
		if(! $lModelResponse['document_id']){
			$this->SetError(getstr('pjs.noSuchDocument'));
			return;
		}
		if($lModelResponse['createuid'] != $this->GetUserId()){
			$this->SetError(getstr('pjs.thisDocumentBelongsToAnotherUser'));
			return;
		}
		if($lModelResponse['state_id'] != (int) DOCUMENT_INCOMPLETE_STATE){
			$this->SetError(getstr('pjs.thisDocumentHasPassedThisSteps'));
			return;
		}

		$this->m_documentType = $lModelResponse['document_source_id'];
		$this->m_journalId = $lModelResponse['journal_id'];

		$this->m_step = (int) $this->GetValueFromRequestWithoutChecks('step', 'GET');
		if($this->m_step <= 0 || $this->m_step > $lModelResponse['creation_step']){
			$this->m_step = (int) $lModelResponse['creation_step'];
		}
	}

	private function ManageSubmittedDocumentType() {
		switch((int)$this->m_journalId) {
			case 1: {
						$this->Redirect('/document_bdj_submission.php?document_id=' . (int)$this->m_documentId);

					}
					break;
			case 2:
					break;
			default:
					$this->Redirect('/index.php');
					break;
		}
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>