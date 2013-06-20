<?php

class cInvite_Users_To_Group_Ajax_Srv extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		
		
		$lGroupId = (int)$this->GetValueFromRequestWithoutChecks('group_id');
		$lUserId = (int)$this->GetValueFromRequestWithoutChecks('user_id');
		$lOper = (int)$this->GetValueFromRequestWithoutChecks('oper');
		
		$lModel = new mUsers();
		if((int)$lOper == 1) { // Insert
			$lResult = $lModel->InviteUserToGroup($lOper, $lUserId, $lGroupId);
		} elseif((int)$lOper == 2) { // Delete
			$lResult = $lModel->InviteUserToGroup($lOper, $lUserId, $lGroupId);
			header('Location:'. $_SERVER['HTTP_REFERER']);
		}
		
		$this->m_pageView = new epPage_Json_View(array('html' => $lResult));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>