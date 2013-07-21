<?php

/**
 * A model class to handle task manipulation
 * @author viktorp
 *
 */
class mTask_Model extends emBase_Model {

	function CreateTask($pEventId, $pTaskDefinitionId, $pArrStringUsers, $pArrStringTemplates, $pArrStringUsersRole, $pIsAutomate, $pArrStringSubjects, $pCC){
		$lCon = $this->m_con;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);

		/**
		 * Creating Task and it's details in transaction - that prevent Task without details related to it
		 * 
		 */
		$lSql = '
			BEGIN;
				INSERT INTO pjs.email_tasks(task_definition_id, event_id, state_id, createdate) VALUES(' . $pTaskDefinitionId . ', ' . $pEventId . ', 1, now());
				SELECT * FROM spCreateTaskDetail(' . $pArrStringTemplates . ', ' . $pArrStringUsers . ', ' . $pArrStringUsersRole . ', currval(\'pjs.email_tasks_id_seq\'), ' . $pIsAutomate . ', ' . $pArrStringSubjects . ', \'' . q($pCC) . '\');
			COMMIT;
		';

 		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}

		return $lResult;
	}
	
	/**
	 * This function returns event data
	 * @param bigint $pEventId
	 */
	function GetEventData($pEventId){
		$lCon = $this->m_con;

		$lSql = '
		SELECT ed.*, edvt.column_name 
		FROM pjs.event_data ed
		JOIN pjs.event_data_types edt ON edt.id = ed.event_data_type_id
		JOIN pjs.event_data_value_types edvt ON edvt.id = edt.value_type 
		WHERE event_id = ' . $pEventId;
		
		//var_dump($lSql);
		//exit;
		//trigger_error('SQL: ' . $lSql, E_USER_NOTICE);
		
		$lResult = array();
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			if($lCon->mRs['event_data_type_id'] == EVENT_JOURNAL_ID_DATA_TYPE) {
				$lKeyName = 'journal_id';
			}
			if($lCon->mRs['event_data_type_id'] == EVENT_DOCUMENT_ID_DATA_TYPE) {
				$lKeyName = 'document_id';
			}
			if($lCon->mRs['event_data_type_id'] == EVENT_USER_ID_DATA_TYPE) {
				$lKeyName = 'uid';
			}
			if($lCon->mRs['event_data_type_id'] == EVENT_USER_EVENT_TO_ID_DATA_TYPE) {
				$lKeyName = 'ueventtoid';
			}
			if($lCon->mRs['event_data_type_id'] == EVENT_USER_EVENT_ROLE_ID_DATA_TYPE) {
				$lKeyName = 'role_id';
			}
			$lResult[$lKeyName] = $lCon->mRs[$lCon->mRs['column_name']];
			
			$lCon->MoveNext();
		}

		return $lResult;
	}
	
	/**
	 * This function returns event data
	 * @param bigint $pEventId
	 */
	function GetTaskDefinition($pDocumentId, $pEventId, $pJournalId = 0){
		$lCon = $this->m_con;
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
		);

		$lSql = 'SELECT * FROM spGetTaskDefinition(' . ((int)$pJournalId  ? (int)$pJournalId : 'NULL') . ', ' . ((int)$pDocumentId ? (int)$pDocumentId : 'NULL') . ', ' . (int)$pEventId . ');';
		
		//trigger_error('GetTaskDefinition: ' . $lSql, E_USER_NOTICE);
		//var_dump($lSql);
		//exit;
 
		if(!$lCon->Execute($lSql)){
			$lResult['err_cnt']++;
			$lResult['err_msgs'][] = array('err_msg' => $lCon->GetLastError());
		}else{
			while(!$lCon->Eof()){
				$lResult['data'][] = $lCon->mRs;
				$lCon->MoveNext();
			}
		}

		return $lResult;
	}
	
	/**
	 * function that gets the recipients for the email tasks
	 * 
	 * @param $pRecipientsId string
	 * @param $pDocumentId int
	 * @param $pJournalId int
	 * 
	 * return array()
	 * 
	 */
	function GetRecipientsData($pRecipientsId, $pDocumentId, $pJournalId, $pUid, $pRoleId){
		$lCon = $this->m_con;
		$lResult = array();
		$lRecipientsResult = array();
		$pRecipientsId = str_replace(array('{', '}'), array('',''), $pRecipientsId);
		$lSql = 'SELECT sql FROM pjs.email_groups WHERE id IN(' . $pRecipientsId . ')';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		
		foreach ($lResult as $key => $value) {
			$lSql = str_replace(array('{document_id}', '{journal_id}', '{uid}', '{role_id}'), array($pDocumentId, $pJournalId, $pUid, $pRoleId), $value['sql']);
			//trigger_error('RECIPIENTS SQL: ' . $lSql, E_USER_NOTICE);
			$lCon->Execute($lSql);
			$lCnt = 0;
			while(!$lCon->Eof()){
				$lRecipientsResult[$lCnt] = $lCon->mRs;
				$lCon->MoveNext();
				$lCnt++;
			}
		}
		
		return array_filter($lRecipientsResult);
	}
	
	/**
	 * This function gets the data that need to be replaced in email task template
	 * 
	 * @param $pUid integer
	 * @param $pDocumentId integer
	 * 
	 * @return array()
	 */
	function GetTemplateValuesForReplace($pUid, $pDocumentId, $pEventTypeId, $pUsrIdEventTo, $pUsrRoleEventTo, $pJournalID) {
		$lCon = $this->m_con;
		$lResult = array();
		
		$lSql = '
			SELECT * 
			FROM pjs."spGetTemplateValuesForReplace"(
				' . (int)$pUid . ', 
				' . (int)$pDocumentId . ', 
				' . (int)$pEventTypeId . ', 
				' . (int)$pUsrIdEventTo . ', 
				' . (int)$pUsrRoleEventTo . ',
				' . (int)$pJournalID . '
			)
		';
		$lCon->Execute($lSql);
		$lResult = $lCon->mRs;
		
		return $lResult; 
	}
	
	function GetEmailTemplates($pTemplateId = ''){
		$lAnd = '';
		if ($pTemplateId){
			$lAnd .= 'AND t.id = '.$pTemplateId;
		}
		
		$lCon = $this->m_con;
		$lResult = array();
		
		$lSql = 'SELECT t.id, t.name as tmpname, t.subject, t.content_template as body, e.name as eventtype,
					(case when is_automated = true THEN \'automated\' ELSE \'controlled\' END) as is_automated
					FROM pjs.email_task_definitions t
					left join pjs.event_types e ON t.event_type_id = e.id WHERE journal_id is null '.$lAnd;
		$lCon->Execute($lSql);
		if ($pTemplateId){
			$lResult = $lCon->mRs;
		} else {
			while(!$lCon->Eof()){
				$lResult[] = $lCon->mRs;
				$lCon->MoveNext();
			}
		}
		return $lResult; 
	}
	
	/**
	 * This function get task details data
	 *
	 *
	 */
	function getFirstTaskDetailData($pEventIds, $pTaskDetailId) {
		$lCon = $this->m_con;
		$lResult = array();
		
		$lSql = 'SELECT etd.*
			FROM pjs.email_task_details etd
			JOIN pjs.email_tasks et ON et.id = etd.email_task_id
			JOIN pjs.email_task_definitions etdef ON etdef.id = et.task_definition_id --AND is_automated = FALSE
			WHERE et.event_id IN(' . implode(',', $pEventIds) . ')
				AND etd.state_id IN (' . TASK_DETAIL_SKIP_STATE_ID . ', ' . TASK_DETAIL_NEW_STATE_ID . ')
				' . ($pTaskDetailId ? ' AND etd.id = ' . $pTaskDetailId : '') . '
			ORDER BY etd.id
			LIMIT 1';

		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}

		return $lResult;
	}
	
	function getTaskDetailDataList($pEventIds) {
		$lCon = $this->m_con;
		$lResult = array();
		
		$lSql = 'SELECT etd.*, u.photo_id, (u.first_name || \' \' ||u.last_name) as name, urt.name as role_name
			FROM pjs.email_task_details etd
			JOIN pjs.email_tasks et ON et.id = etd.email_task_id
			JOIN pjs.email_task_definitions etdef ON etdef.id = et.task_definition_id AND is_automated = FALSE
			JOIN pjs.user_role_types urt ON urt.id = etd.role_id
			JOIN usr u ON u.id = etd.uid
			WHERE et.event_id IN(' . implode(',', $pEventIds) . ')
				AND etd.state_id IN (' . TASK_DETAIL_SKIP_STATE_ID . ', ' . TASK_DETAIL_NEW_STATE_ID . ')
			ORDER BY etd.id';

		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		
		return $lResult;
	}
	
	/**
	 * cancel reviewer invitatation in pop email and delete it's task
	 */
	function CancelReviewerInvitation($pTaskID, $pUID, $pDocumentID) {
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM pjs."spEmailCancelReviewerInvitation"(' . (int)$pTaskID . ', ' . (int)$pUID . ', ' . (int)$pDocumentID . ')';
		$lCon->Execute($lSql);
	}
	
	function SkipTaskDetail($pTaskDetailId, $pSkipOper) {
		$lCon = $this->m_con;
		$lSql = 'UPDATE pjs.email_task_details SET state_id = ' . ((int)$pSkipOper == 2 ? TASK_DETAIL_SKIP_STATE_ID : TASK_DETAIL_NEW_STATE_ID) . ' WHERE id = ' . (int)$pTaskDetailId;
		$lCon->Execute($lSql);
	}
	
	function SaveField($pEmailTaskDetailId, $pFieldName, $pFieldValue) {
		$lCon = $this->m_con;
		$lSql = 'UPDATE pjs.email_task_details 
			SET ' . $pFieldName . ' = \'' . q($pFieldValue) . '\' 
			WHERE id = ' . (int)$pEmailTaskDetailId . ' 
				AND state_id = ' . TASK_DETAIL_NEW_STATE_ID;
		$lCon->Execute($lSql);
	}
	
	function SendJustOneEmail($pTaskDetailId, $pTemplateNotes, $pCC) {
		$lCon = $this->m_con;
		$lSql = '
			UPDATE pjs.email_task_details SET 
				state_id = ' . (int)TASK_DETAIL_READY_STATE_ID . ', 
				cc = \'' . $pCC . '\', 
				template_notes = \'' . $pTemplateNotes . '\' 
			WHERE id = ' . $pTaskDetailId . ' AND state_id = ' . (int)TASK_DETAIL_NEW_STATE_ID;
		$lCon->Execute($lSql);
	}
}

?>