<?php

class cBrowse_Journal_Issue_Documents_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		//$this->InitViewingModeData();
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lIssueId   = (int)$this->GetValueFromRequestWithoutChecks('issue_id');
		$lIssueNum  = (int)$this->GetValueFromRequestWithoutChecks('issue_num'); // volume num
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId)){
			header('Location: /index.php');
		}
		
		$this->m_models['mJournal_Issues_Model'] = new mJournal_Issues_Model();
		
		
		$lIssue = $this->m_models['mJournal_Issues_Model']->GetIssueInfo($lJournalId, $lIssueId, $lIssueNum);
		
		$pViewPageObjectsDataArray['leftcol'] = new evSimple_Block_Display(array(
			'name_in_viewobject' => 'leftcol',
			'journal_id' => $lJournalId,
			'issue_name' => $lIssue['issue_name'],
			'issue_desc' => $lIssue['issue_description'],
			'issue_id' => $lIssue['issue_id'],
			'issue_num' => $lIssue['issue_num'],
			'next_issue_id' => $lIssue['next_issue_id'],
			'prev_issue_id' => $lIssue['prev_issue_id'],
			'min_issue_num' => $lIssue['min_issue_num'],
			'max_issue_num' => $lIssue['max_issue_num'],
			'issue_price' => $lIssue['issue_price'],
			'count_documents' => $lIssue['count_documents'],
			'count_pages' => $lIssue['count_pages'],
			'count_color_pages' => $lIssue['count_color_pages']
		));
		
		$lJournalIssues = $this->m_models['mJournal_Issues_Model']->GetJournalIssueDocuments($lJournalId, (int)$lIssue['issue_id']);
		
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'journal_issue_documents_list_templs',
			'controller_data' => $lJournalIssues,
			'journal_id' => $lJournalId
		);
		
		$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pBrowse_Journal_Issue_Documents_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>