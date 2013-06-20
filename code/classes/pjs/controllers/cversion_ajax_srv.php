<?php
// Disable error reporting because it can break the json output
ini_set('error_reporting', 'off');

class cVersion_Ajax_Srv extends cBase_Controller {
	var $m_errCnt = 0;
	var $m_errMsgs = array();
	var $m_action;
	var $m_action_result;
	function __construct() {
		parent::__construct();
		$pViewPageObjectsDataArray = array();
		$this->m_action = $this->GetValueFromRequestWithoutChecks('action');
		switch ($this->m_action) {
			default :
				$this->m_errCnt ++;
				$this->m_errMsgs[] = array(
					'err_msg' => getstr('pjs.unrecognizedAction')
				);
				break;
			case 'save_version_change' :
				$this->SaveVersionChange();
				break;
			case 'accept_all_changes' :
				$this->AcceptAllChanges();
				break;
			case 'reject_all_changes' :
				$this->RejectAllChanges();
				break;
			case 'get_version_user_display_names':
				$this->GetVersionUserDisplayNames();
				break;
		}

		$lResultArr = array_merge($this->m_action_result, array(
			'err_cnt' => $this->m_errCnt,
			'err_msgs' => $this->m_errMsgs
		));
		$this->m_pageView = new epPage_Json_View($lResultArr);
	}

	function SaveVersionChange() {
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lVersionId = (int) $this->GetValueFromRequestWithoutChecks('version_id');
			$lFieldId = (int) $this->GetValueFromRequestWithoutChecks('field_id');
			$lInstanceId = (int) $this->GetValueFromRequestWithoutChecks('instance_id');
			$lContent = trim($this->GetValueFromRequestWithoutChecks('content'));

			$lVersionModel = new mVersions();

			$this->m_action_result = $lVersionModel->SavePwtVersionChange($lVersionId, $lFieldId, $lInstanceId, $lContent, $this->GetUserId());

			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function AcceptAllChanges(){
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lVersionId = (int) $this->GetValueFromRequestWithoutChecks('version_id');

			$lVersionModel = new mVersions();

			$this->m_action_result = $lVersionModel->PwtVersionAcceptAllChanges($lVersionId, $this->GetUserId());

			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function RejectAllChanges(){
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lVersionId = (int) $this->GetValueFromRequestWithoutChecks('version_id');
			$lVersionModel = new mVersions();

			$this->m_action_result = $lVersionModel->PwtVersionRejectAllChanges($lVersionId, $this->GetUserId());

			if($this->m_action_result['err_cnt']){
				$this->m_errCnt = $this->m_action_result['err_cnt'];
				$this->m_errMsgs = $this->m_action_result['err_msgs'];
			}

		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}

	function GetVersionUserDisplayNames(){
		try{
			if(! $this->GetUserId()){
				throw new Exception(getstr('pjs.onlyLoggedUsersCanPerformThisAction'));
			}
			$lVersionId = (int) $this->GetValueFromRequestWithoutChecks('version_id');
			$lVersionModel = new mVersions();
			$lCurrentUserId = (int)$this->GetUserId();

			$lDocumentInfo = $lVersionModel->GetVersionDocumentInfo($lVersionId);
			$lDocumentId = $lDocumentInfo['document_id'];
			$lHasEditorPermissions =  $lVersionModel->CheckUserSpecificRole($this->GetUserId(), (int)$lDocumentId) ? 1 : 0;

			$lResult = $lVersionModel->GetVersionUserDisplayNames($lVersionId);
			$lProcessedResult = array();

			foreach ($lResult as $lCurrentUser){
				$lId = (int)$lCurrentUser['id'];
				$lRealUserId = (int)$lCurrentUser['undisclosed_real_usr_id'];
				$lUndisclosedUserName = $lCurrentUser['undisclosed_user_fullname'];
				$lRealUserName = $lCurrentUser['name'];
				$lIsDisclosed = (int)$lCurrentUser['is_disclosed'];
				$lNameToDisplay = $lUndisclosedUserName;
				if($lIsDisclosed || $lHasEditorPermissions || $lCurrentUserId == $lRealUserId){
					$lNameToDisplay = $lRealUserName;
				}
				$lProcessedResult[$lId] = $lNameToDisplay;
			}

			$this->m_action_result = array(
				'result' => $lProcessedResult
			);



		}catch(Exception $lException){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => $lException->getMessage()
			);
		}
	}
}

?>