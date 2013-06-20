<?php

/**
 * A model to implement journal sections functionality
 */
class mJournal_Group_Model extends emBase_Model {
	function GetJournalGroups($pJournalId) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT grp.id, (repeat(\' &nbsp; \', char_length(pos) - 2) || name) as title, description, journal_id, pos FROM pjs.journal_user_groups grp 
		WHERE show_in_sidebar
		ORDER BY pos ASC';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	function getSubGroups($pRootNode){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM pjs.journal_user_groups WHERE rootnode = '.$pRootNode . ' AND id <>'. $pRootNode;
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
}
?>