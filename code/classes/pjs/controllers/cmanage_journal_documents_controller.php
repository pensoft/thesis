<?php

class cManage_Journal_Documents_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.manage_journal_documents');
		$this->InitViewingModeData();
		
		$lJournalId  = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lDocumentId = (int)$this->GetValueFromRequestWithoutChecks('document_id');
		$lDeleteDoc  = (int)$this->GetValueFromRequestWithoutChecks('delete');
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckUserIsJournalManager($this->GetUserId(), $lJournalId)){
			header('Location: /index.php');
		}
		
		$this->m_models['mJournal_Documents_Model'] = new mJournal_Documents_Model();
		
		if((int)$lDeleteDoc == 1 && $this->m_models['mJournal_Documents_Model']->CheckJournalDocument($lJournalId, $lDocumentId)){
			$this->m_models['mJournal_Documents_Model']->DeleteDocument($lDocumentId);
		}
		
		$lJournalDocuments = $this->m_models['mJournal_Documents_Model']->GetJournalDocuments($lJournalId);
		
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'journal_documents_list_templs',
			'controller_data' => $lJournalDocuments,
			'journal_id' => $lJournalId
		);
		
		$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pManage_Journal_Documents_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>