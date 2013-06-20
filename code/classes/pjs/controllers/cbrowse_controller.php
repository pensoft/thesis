<?php

class cBrowse_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
				$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.dashboards.MyJournalWideTasks'). ' ' . strtolower(getstr('About pages'));
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
	
		if(!$lJournalId || !$this->GetUserId() || !$this->m_models['mEdit_Model']->CheckJournalRights($this->GetUserId(), $lJournalId) ){
			header('Location: /index.php');
		}
		
		$this->InitViewingModeData();
		
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'stories_tree_templates_edit',
			'controller_data' => $this->m_models['mBrowse_Model']->GetStoriesByJournal($lJournalId),
			'journal_id' => $lJournalId
		);
		
		$this->AddJournalObjects($lJournalId);
			
		$this->m_pageView = new pBrowse_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>