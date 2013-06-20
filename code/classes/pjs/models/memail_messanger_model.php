<?php

/**
 * A model class to handle task manipulation
 * @author viktorp
 *
 */
class mEmail_Messanger_Model extends emBase_Model {

	/**
	 * function GetTaskEmails
	 * 
	 * This function returns all task details(emails) that must be send
	 * 
	 * @return array() 
	 */
	function GetTaskEmails(){
		
		$lCon = $this->m_con;
		$lResult = array();

		/**
		 * Creating Task and it's details in transaction - that prevent Task without details related to it
		 * 
		 */
		$lSql = '
			SELECT
				u.uname as to,
				etd.id,
				etd.subject, 
				etd.template,
				etd.template_notes, 
				etd.cc, 
				etd.bcc 
			FROM pjs.email_task_details etd
			JOIN usr u ON u.id = etd.uid
			WHERE etd.state_id = ' . TASK_DETAIL_READY_STATE_ID . ' OR (etd.state_id = ' . TASK_DETAIL_NEW_STATE_ID . ' AND (etd.createdate + INTERVAL \'' . MANUAL_TASKS_EMAIL_OFFSET . '\') < now())
		';

 		$lCon->Execute($lSql);
		
		while(!$lCon->Eof()){
			$lResult[] = $lCon->mRs; 
			$lCon->MoveNext();
		}

		return $lResult;
	}
	
	function UpdateEmailTaskDetail($pTaskDetailId) {
		$lCon = $this->m_con;
		$lSql = 'UPDATE pjs.email_task_details SET state_id = ' . EMAIL_TASK_DETAIL_SENDED_STATE_ID . ' WHERE id = ' . $pTaskDetailId;
		$lCon->Execute($lSql);
	}
}

?>