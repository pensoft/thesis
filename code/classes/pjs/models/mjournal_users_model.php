<?php

/**
 * A model to implement journal users functionality
 */
class mJournal_Users_Model extends emBase_Model {
	function GetJournalUsers($pJournalId) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = "SELECT first_name || ' ' || last_name as user_names,
					array_to_string(array_agg(DISTINCT ju.role_id), ',') as user_roles, ju.uid
					FROM pjs.journal_users ju
					JOIN usr u ON u.id = ju.uid
					WHERE ju.journal_id = $pJournalId
					GROUP BY ju.uid, u.last_name, u.first_name
					ORDER BY u.first_name, u.last_name
					";
		
		$lCon->Execute($lSql);
		
		while(!$lCon->Eof()){
			$lRow = array();
			$lRow['user_names'] = $lCon->mRs['user_names'];
			$lRow['id'] = $lCon->mRs['uid'];
			$lUserRoles = explode(',', $lCon->mRs['user_roles']);
			
			if(in_array(JOURNAL_MANAGER_ROLE, $lUserRoles))
				$lRow['jm'] = 1;
			else
				$lRow['jm'] = 0;
			
			if(in_array(JOURNAL_EDITOR_ROLE, $lUserRoles))
				$lRow['e'] = 1;
			else
				$lRow['e'] = 0;
			
			if(in_array(SE_ROLE, $lUserRoles))
				$lRow['se'] = 1;
			else
				$lRow['se'] = 0;
			
			if(in_array(LE_ROLE, $lUserRoles))
				$lRow['le'] = 1;
			else
				$lRow['le'] = 0;
			
			if(in_array(CE_ROLE, $lUserRoles))
				$lRow['ce'] = 1;
			else
				$lRow['ce'] = 0;
			
			if(in_array(DEDICATED_REVIEWER_ROLE, $lUserRoles))
				$lRow['r'] = 1;
			else
				$lRow['r'] = 0;
			
			if(in_array(AUTHOR_ROLE, $lUserRoles))
				$lRow['a'] = 1;
			else
				$lRow['a'] = 0;
			
			$lResult[] = $lRow;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function UpdateUserRoles($pJournalId, $pUserId, $pJm, $pE, $pSe, $pLe, $pCe) {
		$pJm = (int)$pJm ? JOURNAL_MANAGER_ROLE : 0;
		$pE = (int)$pE ? JOURNAL_EDITOR_ROLE : 0;
		$pSe = (int)$pSe ? SE_ROLE : 0;
		$pLe = (int)$pLe ? LE_ROLE : 0;
		$pCe = (int)$pCe ? CE_ROLE : 0;
		
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spUpdateUserRoles(' . (int)$pJournalId . ', ' . (int)$pUserId . ', ARRAY[' . (int)$pJm . ',' . (int)$pE . ',' . (int)$pSe . ',' . (int)$pLe . ',' . (int)$pCe . ']) result';
		$lCon->Execute($lSql);
		
		return (int)$lCon->mRs['result'];
	}
}
?>