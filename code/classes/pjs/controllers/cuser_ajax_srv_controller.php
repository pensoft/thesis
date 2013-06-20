<?php

class cUser_Ajax_Srv_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();
		
		$lUsersModel = new mUsers();
		/// secure me!!!!!
		$lAction     	= $this->GetValueFromRequestWithoutChecks('action');
		
		switch($lAction){
			case 'get_subject_editors':
				/// secure me!!!!!
				$lSearchTerm 	= $this->GetValueFromRequestWithoutChecks('term');
				$pViewPageObjectsDataArray = $lUsersModel->get_users_mark_ses($lSearchTerm);
				break;
			case 'get_reviewers':
				$lSearchTerm 	= $this->GetValueFromRequestWithoutChecks('term');
				$pViewPageObjectsDataArray = $lUsersModel->get_users($lSearchTerm);
				break;
			case 'is_se':
				
				$usrData = $this->GetValueFromRequest('usr', 'GET', 'int', false, false);
				if($usrData['err_cnt'])
					exit('usr_id: '. $usrData["err_msgs"][0]['err_msg']);
				
				$journalData	= $this->GetValueFromRequest('journal', 'GET', 'int', false, false);
				if($journalData['err_cnt'])
					exit('journal_id:' . $journalData["err_msgs"][0]['err_msg']);
				
				$pViewPageObjectsDataArray = $lUsersModel->is_se($usrData['value'], $journalData['value']);
				break;
			case 'get_users':
				$lSearchTerm 	= $this->GetValueFromRequestWithoutChecks('term');
				$pViewPageObjectsDataArray = $lUsersModel->get_users($lSearchTerm);
				break;
		}
		
		$this->m_pageView = new epPage_Json_View($pViewPageObjectsDataArray);
	}
}

?>