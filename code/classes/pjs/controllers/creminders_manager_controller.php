<?php
/**
 * A controller used to manage Reminders
 * @author viktorp
 *
 * @version bug-free version
 */
class cReminders_Manager_Controller extends cBase_Controller {
	
	/**
	 * Reminders model
	 * 
	 * @var object
	 * 
	 */
	var $m_RemindersModel;
	
	function __construct($pFieldTempl) {
		parent::__construct();
		$this->m_RemindersModel = new mReminders_Model();
	}
	
	function Display() {
		$lRemindersData = $this->m_RemindersModel->GetRemindersData();
		foreach ($lRemindersData as $key => $value) {
			$lSql = str_replace(array('{offset}', '{journal_id}'), array($value['offset'], $value['journal_id']), $value['condition_sql']);
			//trigger_error('$lConditionSqlData: ' . $lSql, E_USER_NOTICE);
			$lConditionSqlData = $this->m_RemindersModel->GetConditionSqlData($lSql);
			
			foreach ($lConditionSqlData as $condition_key => $condition_value) {
				$lAdditionalParams = array();
				$lEventIdData = $this->m_RemindersModel->CreateEvent(
					$value['event_type_id'], 
					$condition_value['document_id'],
					$condition_value['uid'], 
					$value['journal_id'],
					$condition_value['uid_event_to'],
					$condition_value['uid_event_to_role_id'] 
				);
				$lEventId = $lEventIdData['event_id'];
				echo "\n\n" . 'Document ID: ' . $condition_value['document_id'];
				echo "\n" . 'Event Type ID: ' . $value['event_type_id'];
				if($lEventId) {
					if($condition_value['invitation_id']) {
						$lAdditionalParams['invitation_id'] = (int)$condition_value['invitation_id'];
					}
					//trigger_error('EVENT: ' . $lEventId, E_USER_NOTICE);
					echo "\n" . 'Event ID: ' . (int)$lEventId;
					$lTaskObj = new cTask_Manager(array(
						'event_id' => (int)$lEventId,
						'additional_params' => $lAdditionalParams,
					));
					$lTaskObj->Display();
				} else {
					echo "\n\n" . '!!!ERROR - ' . 'No Event Created. EventTypeId: ' . $value['event_type_id'] . ' DocumentId: ' . $condition_value['document_id'];
					//trigger_error('No Event Created. EventTypeId: ' . $value['event_type_id'] . ' DocumentId: ' . $condition_value['document_id'], E_USER_NOTICE);
				}
			}
		}
		
	}

}

?>