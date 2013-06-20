<?php

class cBrowse_Journal_Special_Issues_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId)){
			header('Location: /index.php');
		}
		
		$lIssuesModel = new mJournal_Issues_Model();
		
		$pViewPageObjectsDataArray['journal_special_issues_list'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'journal_special_issues_list_templs',
			'controller_data' => $lIssuesModel->GetJournalSpecialIssues($lJournalId),
			'journal_id' => $lJournalId,
			'back_issue' => (int)$lBackIssue
		);
		
		$pViewPageObjectsDataArray['journal_features'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'journal_features_templates',
			'controller_data' => $this->m_models['mBrowse_Model']->GetJournalFeatures($lJournalId),
			'journal_id' => $lJournalId
		);
		
		$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pBrowse_Journal_Special_Issues_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>