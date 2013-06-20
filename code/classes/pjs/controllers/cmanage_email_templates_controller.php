<?php

class cManage_Email_Templates_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$this->InitViewingModeData();
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lError = $this->GetValueFromRequestWithoutChecks('error');
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId) || 
			!$this->GetUserId() || !$this->m_models['mEdit_Model']->CheckJournalRights($this->GetUserId(), $lJournalId)){
			header('Location: /index.php');
		}
		
		$this->m_models['mTask_Model'] = new mTask_Model();
		
		$lControllerData = $this->m_models['mTask_Model']->GetEmailTemplates();
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'email_default_templates_list',
			'controller_data' => $lControllerData,
			'journal_id' => $lJournalId,
			'error' => $lError,
		);
		
		$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pManage_Email_Templates_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		
		$this->InitLeftcolObjects();
	}
}
?>
