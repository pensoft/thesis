<?php

/**
 * A model class to handle task manipulation
 * @author viktorp
 *
 */
class mReminders_Model extends emBase_Model {
		
	function GetRemindersData() {
		$lCon = $this->m_con;
		$lResult = array();
		
		$lSql = '
			SELECT 
				er.event_type_id, 
				er.condition_sql, 
				er.journal_id,
				ef.offset
			FROM pjs.event_reminders er
			JOIN pjs.event_offset ef ON ef.event_type_id = er.event_type_id AND er.journal_id = ef.journal_id
			ORDER BY er.journal_id
		';

		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		
		return $lResult;
	}
		
	function GetConditionSqlData($pSql) {
		$lCon = $this->m_con;
		$lResult = array();
		
		//$lCon->Execute($pSql);
		if(!$lCon->Execute($pSql)){
			echo "\n\n" . '!!!ERROR!!!: ' . "\n" . $pSql . "\n\n";
		} else {
			while(!$lCon->Eof()){
				$lResult[] = $lCon->mRs;
				$lCon->MoveNext();
			}
		}
		
		return $lResult;
	}
	
	function CreateEvent($pEventTypeId, $pDocumentId, $pUid, $pJournal_id, $pUsrEventTo = null, $pUsrRoleId = null) {
		$lCon = $this->m_con;
		$lResult = array();
		
		$lSql = 'SELECT event_id FROM spCreateEvent(
			' . (int)$pEventTypeId . ', 
			' . (int)$pDocumentId . ', 
			' . (int)$pUid . ', 
			' . (int)$pJournal_id . ', 
			' . ((int)$pUsrEventTo ? (int)$pUsrEventTo : 'null') . ', 
			' . ((int)$pUsrRoleId ? (int)$pUsrRoleId : 'null') . '
		);';
		//var_dump($lSql);
		$lCon->Execute($lSql);
		$lResult['event_id'] = $lCon->mRs['event_id'];
		
		return $lResult;
	}
		
}

?>