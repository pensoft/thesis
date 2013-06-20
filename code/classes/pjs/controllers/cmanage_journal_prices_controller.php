<?php

class cManage_Journal_Prices_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.dashboards.MyJournalWideTasks'). ' ' . strtolower(getstr('Prices'));
		$this->InitViewingModeData();
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		
		if(!(int)$lJournalId || !$this->GetUserId() ||
			!$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId) || 
			!$this->m_models['mEdit_Model']->CheckJournalRights($this->GetUserId(), $lJournalId) ){
			header('Location: /index.php');
		}
		
		$this->m_models['mJournal_Prices_Model'] = new mJournal_Prices_Model();
		
		$lAction = $this->GetValueFromRequestWithoutChecks('tAction');
		
		if(isset($lAction) && $lAction == 'save'){
			$this->m_models['mJournal_Prices_Model']->SaveJournalPrices( $lJournalId,
														$this->GetValueFromRequestWithoutChecks('range_start'),
														$this->GetValueFromRequestWithoutChecks('range_end'),
														$this->GetValueFromRequestWithoutChecks('price')
													);
		}
		
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'prices_list_templs',
			'controller_data' => $this->m_models['mJournal_Prices_Model']->GetJournalPrices($lJournalId),
			'journal_id' => $lJournalId
		);
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pJournal_Prices_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>