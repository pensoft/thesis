<?php

class cEdit_Journal_Issue_Documents_Controller extends cBase_Controller {
	
	function __construct() {
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();
		$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.manage_journal_issue_documents');
		$this->InitViewingModeData();
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lIssueId = (int)$this->GetValueFromRequestWithoutChecks('issue_id');
		
		if(!$lJournalId || !$this->GetUserId() || 
			!$this->m_models['mEdit_Model']->CheckJournalIssueRights($this->GetUserId(), $lJournalId)){
			header('Location: /index.php');
		}
		
		$this->m_models['mJournal_Issues_Model'] = new mJournal_Issues_Model();
		
		$lAction = $this->GetValueFromRequestWithoutChecks('tAction');
		
		if($lAction == 'remove'){
			$this->m_models['mJournal_Issues_Model']->RemoveDocumentFromIssue( $lJournalId, $lIssueId, 
																	(int)$this->GetValueFromRequestWithoutChecks('document_id')
																);
		}elseif($lAction == 'move'){
			$this->m_models['mJournal_Issues_Model']->MoveDocumentUpDown( $lJournalId, $lIssueId, 
																	(int)$this->GetValueFromRequestWithoutChecks('document_id'),
																	(int)$this->GetValueFromRequestWithoutChecks('direction')
																);
		}elseif($lAction == 'save'){
			//~ $this->m_models['mJournal_Issues_Model']->SaveDocumentPages($lJournalId, $lIssueId, 
																	//~ (int)$this->GetValueFromRequestWithoutChecks('document_id'),
																	//~ (int)$this->GetValueFromRequestWithoutChecks('range_start'),
																	//~ (int)$this->GetValueFromRequestWithoutChecks('range_end'),
																	//~ (int)$this->GetValueFromRequestWithoutChecks('color_pages')
																//~ );
		}
		
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'issue_documents_list_templs',
			'controller_data' => $this->m_models['mJournal_Issues_Model']->GetEditIssueDocuments($lJournalId, $lIssueId),
			'journal_id' => $lJournalId
		);
		
		
		$this->AddJournalObjects($lJournalId);
		$this->m_pageView = new pEdit_Issue_Documents_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}
	
	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}

?>