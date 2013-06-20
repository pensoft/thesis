<?php

class cBrowse_Journal_Issues_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		
		$lJournalId    = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lOnlySpecials = (int)$this->GetValueFromRequestWithoutChecks('special_issues');
		$lIssuesYear   = (int)$this->GetValueFromRequestWithoutChecks('year');
		$lIssueVolume  = (int)$this->GetValueFromRequestWithoutChecks('issue_volume');
		//~ echo $lOnlySpecials;
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId)){
			header('Location: /index.php');
		}
		
		$this->m_models['mJournal_Issues_Model'] = new mJournal_Issues_Model();
		
		$lIssue = $this->m_models['mJournal_Issues_Model']->GetIssueInfo($lJournalId, 0, $lIssueVolume);
		
		if(is_array($lIssue) && (int)$lIssue['issue_id']){
			/*$pViewPageObjectsDataArray['issue_name']    = $lIssue['issue_name'];
			$pViewPageObjectsDataArray['issue_desc']    = $lIssue['issue_description'];
			$pViewPageObjectsDataArray['issue_id']      = (int)$lIssue['issue_id'];
			$pViewPageObjectsDataArray['issue_num']     = (int)$lIssue['issue_num'];
			$pViewPageObjectsDataArray['next_issue_id'] = (int)$lIssue['next_issue_id'];
			$pViewPageObjectsDataArray['prev_issue_id'] = (int)$lIssue['prev_issue_id'];
			$pViewPageObjectsDataArray['min_issue_num'] = (int)$lIssue['min_issue_num'];
			$pViewPageObjectsDataArray['max_issue_num'] = (int)$lIssue['max_issue_num'];
			$pViewPageObjectsDataArray['issue_price'] 	= (int)$lIssue['issue_price'];
			$pViewPageObjectsDataArray['count_documents'] = (int)$lIssue['count_documents'];
			$pViewPageObjectsDataArray['count_pages'] 	= (int)$lIssue['count_pages'];
			$pViewPageObjectsDataArray['count_color_pages'] = (int)$lIssue['count_color_pages'];*/
			
			$lMinVolume = (int)$lIssue['min_issue_num'];
			$lMaxVolume = (int)$lIssue['max_issue_num'];
		}
		
		$lControllerData =  $this->m_models['mJournal_Issues_Model']->GetJournalIssues($lJournalId, $lOnlySpecials, $lIssuesYear, $lIssueVolume,
																						(int)$this->GetValueFromRequestWithoutChecks('p'));
		//~ var_dump($lControllerData);
		//~ exit;
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'browse_journal_issues_list_templs',
			'controller_data' => $lControllerData,
			'journal_id' => $lJournalId,
			'issuespecial' => $lOnlySpecials,
			'issueyear' => $lIssuesYear,
			'default_page_size' => DEFAULT_PAGE_SIZE,
			'page_parameter_name' => 'p',
			'purl' => '/browse_journal_issues.php?journal_id=' . $lJournalId . 
												'&special_issues=' . $lOnlySpecials . 
												'&year=' . $lIssuesYear . 
												'&issue_volume=' . $lIssueVolume,
		);
		
		$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		$pViewPageObjectsDataArray['issue_year'] = $lIssuesYear;
		$pViewPageObjectsDataArray['special_issues'] = $lOnlySpecials;
		
		$pViewPageObjectsDataArray['leftcol'] = new evSimple_Block_Display(array(
			'name_in_viewobject' => 'leftcol',
			'journal_id' => $lJournalId,
			'issue_year' => $lIssuesYear,
			'special_issues' => $lOnlySpecials,
			'min_volume' => (int)$lMinVolume,
			'max_volume' => (int)$lMaxVolume,
			
		));
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pBrowse_Journal_Issues_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>