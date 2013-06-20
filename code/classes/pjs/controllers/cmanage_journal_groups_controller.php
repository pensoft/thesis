<?php

class cManage_Journal_Groups_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
				$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.dashboards.MyJournalWideTasks'). ' ' . strtolower(getstr('sections'));
		$this->InitViewingModeData();
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lError = $this->GetValueFromRequestWithoutChecks('error');

		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId) || 
			!$this->GetUserId() || !$this->m_models['mEdit_Model']->CheckJournalRights($this->GetUserId(), $lJournalId)){
			header('Location: /index.php');
		}
		
		$this->m_models['mJournal_Group_Model'] = new mJournal_Group_Model();
		
		$lControllerData = $this->m_models['mJournal_Group_Model']->GetJournalGroups($lJournalId);
		
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'journal_sections_list_templs',
			'controller_data' => $lControllerData,
			'journal_id' => $lJournalId,
			'error' => $lError,
		);
		
		$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pManage_Journal_Groups_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		
		$this->InitLeftcolObjects();
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>