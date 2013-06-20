<?php

class cManage_Journal_Users_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.manage_journal_users');
		
		$lJournalId   = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lUpdateRoles = (int)$this->GetValueFromRequestWithoutChecks('update_roles');
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckUserIsJournalManager($this->GetUserId(), $lJournalId)){
			header('Location: /index.php');
		}
		
		$this->m_models['mJournal_Users_Model'] = new mJournal_Users_Model();
		
		if((int)$lUpdateRoles){
			$lResult = $this->m_models['mJournal_Users_Model']->UpdateUserRoles($lJournalId, 
																				(int)$this->GetValueFromRequestWithoutChecks('user_id'),
																				(int)$this->GetValueFromRequestWithoutChecks('jm'),
																				(int)$this->GetValueFromRequestWithoutChecks('e'),
																				(int)$this->GetValueFromRequestWithoutChecks('se'),
																				(int)$this->GetValueFromRequestWithoutChecks('le'),
																				(int)$this->GetValueFromRequestWithoutChecks('ce')
																			);
			$this->m_pageView = new epPage_Json_View(array('result' => $lResult));
		}else{
			$this->InitViewingModeData();
			
			$lJournalUsers = $this->m_models['mJournal_Users_Model']->GetJournalUsers($lJournalId);
			
			$pViewPageObjectsDataArray['contents'] = array(
				'ctype' => 'evList_Display',
				'name_in_viewobject' => 'journal_users_list_templs',
				'controller_data' => $lJournalUsers,
				'journal_id' => $lJournalId
			);
			
			$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
			
			$this->AddJournalObjects($lJournalId);
			
			$this->m_pageView = new pManage_Journal_Users_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
			$this->InitLeftcolObjects();
		}
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>