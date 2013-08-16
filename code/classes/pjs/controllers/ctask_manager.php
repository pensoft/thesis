<?php
/**
 * A controller used to manage Tasks 
 * Tasks are created based on events
 * 
 * @author victorp
 * @version 1.2.1
 * 
 */
class cTask_Manager extends cBase_Controller {
	
	/**
	 * Event Id
	 * 
	 * @var int
	 * 
	 */
	var $m_eventId;
	
	/**
	 * Error Count
	 * 
	 * @var int
	 * 
	 */
	var $m_errCnt = 0;
	
	/**
	 * Error message 
	 * 
	 * @var array()
	 * 
	 */
	var $m_errMsgs = array();
	
	/**
	 * Task Model Object
	 * 
	 * @var object
	 * 
	 */
	var $m_taskModel;
	
	/**
	 * user password (on registration)
	 * 
	 * @var string
	 * 
	 * */
	var $m_uPass;
	
	/**
	 * Journal ID
	 * 
	 * @var int
	 * 
	 * */
	var $m_JournalId;
	
	/**
	 * Event Data
	 * 
	 * @var array()
	 */
	var $m_eventDataArr = array();
	
	/**
	 * Event actions array
	 * 
	 * @var array() 
	 */
	var $m_eventActionsArr = array();
	
	/**
	 * additional parameters used for events actions
	 * 
	 * @var array()
	 */
	var $m_AdditionalParams = array();
	
	function __construct($pFieldTempl) {
		parent::__construct();
		
		$this->m_eventId = $pFieldTempl['event_id'];
		$this->m_uPass = $pFieldTempl['upass'];
		$this->m_JournalId = (array_key_exists('journal_id', $pFieldTempl) ? $pFieldTempl['journal_id'] : 0);
		$this->m_AdditionalParams = (is_array($pFieldTempl['additional_params']) ? $pFieldTempl['additional_params'] : array());
		
		//trigger_error('EVENT ID: ' . $pFieldTempl['event_id'], E_USER_NOTICE);
		if(!(int)$this->m_eventId) {
			$this->m_errCnt++;
			$this->m_errMsgs[] = getstr('pjs.noEventId');
		}
		
		$this->m_taskModel = new mTask_Model();
	}

	/**
	 * private function CreateTask
	 * This method creates task and it's details for Current Event ($this->m_eventId)
	 * 
	 * @param $pTaskDefinitions array() - definitions for the current event
	 * 
	 * @return void()
	 * 
	 */
	private function CreateTask($pTaskDefinitions) {
		$lTaskData = array();
		// trigger_error('CreateTask() INNER DEBUG POINT', E_USER_NOTICE);
		foreach ($pTaskDefinitions as $key => $value) {
			// trigger_error('RECIPIENTS: ' . $value['recipients'], E_USER_NOTICE);
			// trigger_error('document_id: ' . $value['document_id'], E_USER_NOTICE);
			//trigger_error('document_journal_id: ' . $value['document_journal_id'], E_USER_NOTICE);
			$lRecipientsData = $this->GetTaskRecipientsData($value['recipients'], $value['document_id'], $value['document_journal_id']);
			$lUsersArr = array();
			$lUsersRoleArr = array();
			$lUserTemplArr = array();
			$lUserSubjArr = array();
			$lTemplate = $value['content_template'];
			$lSubject = $value['subject'];
			
			foreach ($lRecipientsData as $key1 => $value1) {
				$lDataValuesToReplace = $this->m_taskModel->GetTemplateValuesForReplace(
					(int)$value1['uid'], 
					(int)$value['document_id'], 
					$value['event_type_id'], 
					$this->m_eventDataArr['ueventtoid'], 
					$this->m_eventDataArr['role_id'],
					((int)$this->m_JournalId ? (int)$this->m_JournalId : $value['document_journal_id'])
				);
				$lDataValuesToReplace['user_role'] = (int)$value1['role_id'];
				
				$lTempl = $this->ReplaceEmailTaskTemplate($lTemplate, $lDataValuesToReplace);
				$lSubj = $this->ReplaceEmailTaskTemplate($lSubject, $lDataValuesToReplace);
				
				$lUsersArr[] = (int)$value1['uid'];
				$lUsersRoleArr[] = (int)$value1['role_id'];
				$lUserTemplArr[] = '\'' . q($lTempl) . '\'';
				//$lUserSubjArr[] = '\'' . q($lSubj  . ' ' . ($this->m_uPass ? 'JournalID:' . (int)$this->m_JournalId : $value['document_id'])) . '\'';
				$lUserSubjArr[] = '\'' . q($lSubj) . '\'';
			}
			
			/**
			 * Building users array and template array for postgresql
			 */
			$lUsersArrString = 'ARRAY[' . implode(",", $lUsersArr) . ']';
			$lUsersRoleArrString = 'ARRAY[' . implode(",", $lUsersRoleArr) . ']';
			$lUserTemplArrString = 'ARRAY[' . implode(",", $lUserTemplArr) . ']';
			$lUserSubjArrString = 'ARRAY[' . implode(",", $lUserSubjArr) . ']';
			
			// trigger_error('$lUsersArrString: ' . $lUsersArrString, E_USER_NOTICE);
			// trigger_error('$lUsersRoleArrString: ' . $lUsersRoleArrString, E_USER_NOTICE);
			// trigger_error('$lUserTemplArrString: ' . $lUserTemplArrString, E_USER_NOTICE);
			
			$lTaskData = $this->m_taskModel->CreateTask(
				(int)$this->m_eventId, 
				(int)$value['id'], 
				$lUsersArrString, 
				$lUserTemplArrString, 
				$lUsersRoleArrString, 
				$value['is_automated'], 
				$lUserSubjArrString,
				$value['cc']
			);
		}
	} 

	/**
	 * private function GetEventInfo
	 * This method returns event data
	 * 
	 * @return array()
	 * 
	 */
	private function GetEventData() {
		$lEventData = array();
		$lEventData = $this->m_taskModel->GetEventData((int)$this->m_eventId);
		return $lEventData;
	}

	/**
	 * private function GetTaskRecipients
	 * This method returns email task recipients
	 * 
	 * @return array()
	 * 
	 */
	private function GetTaskRecipientsData($pRecipients, $pDocumentId, $pJournalId) {
		$lRecipientsData = $this->m_taskModel->GetRecipientsData($pRecipients, (int)$pDocumentId, (int)$pJournalId, $this->m_eventDataArr['ueventtoid'], $this->m_eventDataArr['role_id']);
		return $lRecipientsData;
	}

	/**
	 * private function GetTaskDefinition
	 * This method returns email task definition/s
	 * 
	 * @param $pDocumentId - Document Id
	 * 
	 * @return array()
	 * 
	 */
	private function GetTaskDefinition($pDocumentId, $pJournalId = 0){
		$lTaskDefinitionData = array();
		$lTaskDefinitionData = $this->m_taskModel->GetTaskDefinition($pDocumentId, (int)$this->m_eventId, $pJournalId);
		if((int)$lTaskDefinitionData['err_cnt']) {
			$this->m_errCnt++;
			$this->m_errMsgs = $lTaskDefinitionData['err_msgs']; 
		}
		
		return $lTaskDefinitionData['data'];
	}

	/**
	 * private function ReplaceEmailTaskTemplate
	 * This method replace defined objects in task template
	 * 
	 * @param $pTemplate string - task template
	 * @param $pDataToFromReplace - values which must be replaced in the template
	 * 
	 * @return string
	 * 
	 */
	private function ReplaceEmailTaskTemplate($pTemplate, $pDataToFromReplace) {
		$doc_id = (int)$pDataToFromReplace['document_id'];
		$role   = (int)$pDataToFromReplace['user_role'];
		$u 		= SITE_URL; //JOURNAL_URL; OK until there is only 1 journal.
		$a 		= '<a target="_blank" href="' . $u;
		
		$lDict = array(
			'{site_url}' 			=> $u,
			'{upass}'				=> $this->m_uPass,
			'{first_name}'  		=> $pDataToFromReplace['first_name'], 
			'{last_name}'  			=> $pDataToFromReplace['last_name'], 
			'{document_id}' 		=> $pDataToFromReplace['document_id'], 
			'{document_title}' 		=> trim($pDataToFromReplace['document_title']),
			'{usr_title}'			=> $pDataToFromReplace['usr_title'],
			'{user_name}'			=> $pDataToFromReplace['user_name'],
			'{author_list}'			=> $pDataToFromReplace['author_list'],
			'{review_type_name}'	=> $pDataToFromReplace['review_type_name'],
			'{due_date}'			=> $pDataToFromReplace['due_date'],
			'{due_date_days}' 		=> $pDataToFromReplace['due_date_days'],
			'{journal_name}' 		=> $pDataToFromReplace['journal_name'],
			'{journal_email}' 		=> $pDataToFromReplace['journal_email'],
			'{journal_signature}' 	=> $pDataToFromReplace['journal_signature'],
			'{user_role}' 			=> $pDataToFromReplace['user_role'],
			'{journal_id}' 			=> $pDataToFromReplace['journal_id'],
			'{SE_first_name}' 		=> $pDataToFromReplace['se_first_name'],
			'{SE_last_name}' 		=> $pDataToFromReplace['se_last_name'],
			'{SE_usr_title}' 		=> $pDataToFromReplace['se_usr_title'],
			'{SE_email}' 			=> $pDataToFromReplace['se_email'],
			'{R_first_name}' 		=> $pDataToFromReplace['r_first_name'],
			'{R_last_name}' 		=> $pDataToFromReplace['r_last_name'],
			'{R_usr_title}' 		=> $pDataToFromReplace['r_usr_title'],
			'{SE_tax_expertize}' 	=> $pDataToFromReplace['se_tax_expertize'],
			'{SE_geo_expertize}' 	=> $pDataToFromReplace['se_geo_expertize'],
			'{SE_sub_expertize}' 	=> $pDataToFromReplace['se_sub_expertize'],
			'{SE_createusr_tax_expertize}' 	=> $pDataToFromReplace['se_createusr_tax_expertize'],
			'{SE_createusr_geo_expertize}' 	=> $pDataToFromReplace['se_createusr_geo_expertize'],
			'{SE_createusr_sub_expertize}' 	=> $pDataToFromReplace['se_createusr_sub_expertize'],
			'{NomReview_due_days}'	=> $pDataToFromReplace['nomreview_due_days'],
			'{NomReview_due_date}'	=> $pDataToFromReplace['nomreview_due_date'],
			'{PanReview_due_days}'	=> $pDataToFromReplace['panreview_due_days'],
			'{PanReview_due_date}'	=> $pDataToFromReplace['panreview_due_date'],
			'{SE_invite_reviewers_days}'	=> $pDataToFromReplace['se_invite_reviewers_days'],
			'{SE_can_take_decision_days}'	=> $pDataToFromReplace['se_can_take_decision_days'],
			'{site_href}' 			=> $a . '">'. $u .'</a>',
			'{tasks_href}' 			=> $a . 'dashboard">Your tasks</a>',
			'{document_editor_href}'=> $a . 'view_document.php?id=' . $doc_id . '&view_role=' . (int)JOURNAL_EDITOR_ROLE . '">' . $pDataToFromReplace['document_title'] . '</a>',
			'{autologging_href}' 	=> $a . 'login.php?u_autolog_hash=' . $pDataToFromReplace['autolog_hash'] . '&document_id=' . $doc_id . '&view_role=' . $role . '"  class="' . HIDDEN_EMAIL_ELEMENT . '">login</a>',
			'{autologging_doc_link}'=> $u . 'login.php?u_autolog_hash=' . $pDataToFromReplace['autolog_hash'] . '&document_id=' . $doc_id . '&view_role=' . $role,
			'{autologging_link}' 	=> $u . 'login.php?u_autolog_hash=' . $pDataToFromReplace['autolog_hash'],		
			'{document_link}' 		=> $u . 'view_document.php?id=' . $doc_id . '&view_role=' . $role,
		);
		$lTempl = strtr($pTemplate, $lDict);
		return $lTempl;
	}

	/**
	 * private function ExecEventActions()
	 * 
	 * This method evals event action code
	 * @return void()
	 */
	private function ExecEventActions(){
		$this->m_eventActionsArr = $this->m_taskModel->GetEventActions($this->m_eventId, $this->m_eventDataArr['journal_id']);
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		
		foreach ($this->m_eventActionsArr as $key => $value) {
			//trigger_error('!!!!!!!!!!!!!!invitation_id!!!!!!!!!!: ' . (int)$this->m_AdditionalParams['invitation_id'], E_USER_NOTICE);
			if($value['eval_code']) {
				//trigger_error('CODE: ' . $value['eval_code'], E_USER_NOTICE);
				try{
					eval($value['eval_code']);
				}catch(Exception $pException){
					$lResult['err_cnt'] ++;
					$lResult['err_msgs'][] = array(
						'err_msg' => $pException->getMessage()
					);
				}
			}
		}
		
		//trigger_error('ERRORS: ' . var_export($lResult, 1), E_USER_NOTICE);
		
		return $lResult;
	}

	/**
	 * If there are errors - we display them.
	 *
	 */
	function Display(){
		//trigger_error('CREATING TASK DEBUG POINT', E_USER_NOTICE);
		
		$this->m_eventDataArr = $this->GetEventData();
		
		// exec event actions here 
		$this->ExecEventActions();
		
		//trigger_error('ERRORS1: ' . $this->m_errCnt, E_USER_NOTICE);
		//trigger_error('GetEventData() DEBUG POINT', E_USER_NOTICE);
		
		$lTaskDefinition = $this->GetTaskDefinition((int)$this->m_eventDataArr['document_id'], (int)$this->m_JournalId);
		
		//trigger_error('GetTaskDefinition() DEBUG POINT', E_USER_NOTICE);
		//trigger_error('ERRORS2: ' . $this->m_errCnt, E_USER_NOTICE);
		
		if($this->m_errCnt){
			return $this->m_errMsgs;
		}
		
		$this->CreateTask($lTaskDefinition);
		
		//trigger_error('CreateTask() DEBUG POINT', E_USER_NOTICE);
		
		if($this->m_errCnt){
			return $this->m_errMsgs;
		}
	}
}

?>