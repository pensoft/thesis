<?php

class cManage_Journal_Issues_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$this->InitViewingModeData();
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lBackIssue = (int)$this->GetValueFromRequestWithoutChecks('back_issue');
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId) || 
			!$this->GetUserId() || !$this->m_models['mEdit_Model']->CheckJournalIssueRights($this->GetUserId(), $lJournalId)){
			header('Location: /index.php');
		}
		
		$lIssuesModel = new mJournal_Issues_Model();
		
		if((int)$lBackIssue){
			$lIssuesListData = $lIssuesModel->GetJournalBackIssues($lJournalId);
			$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.manage_journal_back_issues');
			$lTemplates = 'journal_back_issues_list_templs';
		}else{
			$lIssuesListData = $lIssuesModel->GetJournalFutureIssues($lJournalId);
			$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.manage_journal_future_issues');
			$lTemplates = 'journal_issues_list_templs';
		}
		
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => $lTemplates,
			'controller_data' => $lIssuesListData,
			'journal_id' => $lJournalId,
			'back_issue' => (int)$lBackIssue
		);
		
		$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pManage_Journal_Issues_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>