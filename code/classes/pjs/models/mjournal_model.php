<?php

/**
 * A model to implement journals functionality
 */
class mJournal_model extends emBase_Model {
	function GetJournals($pTableName, $pRootNode = false, $pKey = 0) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM journals';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[$lCon->mRs['id']] = array(
				'id' => (int)$lCon->mRs['id'],
				'name' => $lCon->mRs['name'],
				'description' => $lCon->mRs['description'],
			); 
			$lCon->MoveNext();
		}
		return $lResult;
	}
}
?>