<?php
/**
 * A model to implement journal authors functionality
 */
class mJournal_Authors_Model extends emBase_Model {
	
	function GetJournalAuthors($pJournalId, $pAuthorLetter, $pAffiliation, $pPage){
		$lResult = array();
		$lWhereAnd = '';
		
		if(strlen($pAuthorLetter))
			$lWhereAnd .= ' AND lower(u.last_name) LIKE \'' . q(strtolower($pAuthorLetter)) . '%\'';
		
		if(strlen($pAffiliation))
			$lWhereAnd .= ' AND lower(u.affiliation) LIKE \'%' . q(strtolower($pAffiliation)) . '%\'';
		
		$lSql = 'SELECT u.last_name || \', \' || u.first_name as author_names, 
						u.photo_id as previewpicid, u.affiliation, u.id
					FROM pjs.journal_users ju
					JOIN usr u ON u.id = ju.uid
					WHERE ju.journal_id = ' . (int)$pJournalId . '
							AND ju.role_id = ' . AUTHOR_ROLE . '
						' . $lWhereAnd . '
					';
		
		$lCon = $this->m_con;
		$lCon->SetFetchReturnType(PGSQL_ASSOC);
		$lCon->Execute($lSql);
		$lCon->SetPage(DEFAULT_PAGE_SIZE, $pPage);
		while(!$lCon->Eof()){
			$lResultData[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		$lResult = new emResults( array(
			'controller_data' => $lResultData,
			'pagesize' => DEFAULT_PAGE_SIZE, // not necessary, but easier this way
			'page_num' => $lCon->mPageNum,
			'record_count' => $lCon->RecordCount(),
		));
		
		return $lResult;
	}
}
?>