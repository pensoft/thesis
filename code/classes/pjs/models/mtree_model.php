<?php

/**
 * A model to implement tree functionality
 */
class mTree_Model extends emBase_Model {
	
	function getRegTreeCategoriesByName($pTableName, $pRootNode = false, $pKey = 0, $pJournalId = 1, $pIsByJournalId = 1) {
		$lRet = array();
		
		$lWhere = ' ';
		if((int)$pIsByJournalId) {
		
			if($pRootNode)
				$lWhere = ' AND char_length(tbj.pos) = 2 AND tbj.journal_id = ' . (int)$pJournalId ;
			if($pKey)
				$lWhere = ' AND tbj.rootnode = ' . $pKey . ' AND tbj.journal_id = ' . (int)$pJournalId ;
			
			$lSql = '
				SELECT tbj.id as id, tbj.pos as pos, t.name , tbj.root, t.parentnode
				FROM ' . q($pTableName . '_byjournal') . ' tbj
				JOIN ' . q($pTableName) . ' t ON t.id = tbj.id
				' .  $lWhere . ' 
				ORDER BY t.name';
		} else {
			if($pRootNode)
				$lWhere = ' WHERE rootnode = 0 ';
			if($pKey) {
				$lCharCount = strlen($pKey);
				$lWhere = ' WHERE pos like \'' . $pKey . '%\' AND char_length(pos) = ' . $lCharCount . ' + 2 ';
			}
			$lSql = 'SELECT id, name, rootnode, pos FROM ' . q($pTableName) . ' ' .  $lWhere . ' ORDER BY name';
		}
		
		$lCon = $this->m_con;
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		$lRet[] = $lCon->mRs;
		while($lCon->MoveNext()){
			$lRet[] = $lCon->mRs;
		}	
		return $lRet;
	}
	
	function getRegTreeAutocompleteItems($pTableName, $pRootNode = false, $pKey = 0, $pFilterByDocumentJournal = 0, $pJournalId = 0) {
		$lWhere = ' ';
		if($pRootNode)
			$lWhere = ' WHERE n.parentnode = 0 AND r.journal_id = ' . (int)$pJournalId ;
		if($pKey && $pFilterByDocumentJournal){
			$lCharCount = strlen($pKey);
			$lWhere = ' WHERE r.pos like \'' . $pKey . '%\' AND char_length(r.pos) = ' . $lCharCount . ' + 2 AND r.journal_id = ' . (int)$pJournalId ;
		} elseif ($pKey) {
			$lCharCount = strlen($pKey);
			$lWhere = ' WHERE pos like \'' . $pKey . '%\' AND char_length(pos) = ' . $lCharCount . ' + 2';
		
		}
		$lCon = $this->m_con;
		$lCon->SetFetchReturnType(PGSQL_ASSOC);
		if((int)$pFilterByDocumentJournal /*&& $pTableName == TAXON_NOMENCLATURE_TABLE_NAME*/ && (int)$pJournalId){
			$pQuery = 'SELECT r.id as key, n.name as title, r.pos as pos
				FROM ' . q($pTableName) . ' n
				JOIN ' . q($pTableName) . '_byjournal r ON r.id = n.id
				' . $lWhere . '
				ORDER BY n.name';
		}else{
			$pQuery = 'SELECT n.id as key, n.name as title, n.pos as pos FROM ' . q($pTableName) . ' n ' .  $lWhere . ' ORDER BY name';
		}
		$lCon->Execute($pQuery);
		$lCon->MoveFirst();
		$lSrcValues = array();
		while(!$lCon->Eof()) {
			$lCurrentRow = array();
			foreach ($lCon->mRs as $key => $value) {
				if($key == "key") {
					 $value = $pTableName[0] . $value;
				}
					$lCurrentRow[$key] = $value;
					$lCurrentRow['isLazy'] = true;
				
			}
			$lSrcValues[] = $lCurrentRow;
			$lCon->MoveNext();
		}
		return $lSrcValues;
	}
	
	function getRegFieldAutocompleteItems($pTerm, $pTable, $pFilterByDocumentJournal = 0, $pJournalId = 0){
		$lCon = $this->m_con;
		$unique = $pTable[0];
		if((int)$pFilterByDocumentJournal && /*$pTable == TAXON_NOMENCLATURE_TABLE_NAME && */ (int)$pJournalId){
			$pQuery = 'SELECT \'' . $unique . '\' || n.id::text as id, n.name as name
				FROM ' . q($pTable) . ' n
				JOIN ' . q($pTable) . ' r ON r.id = CASE WHEN coalesce(n.rootnode, 0) <> 0 THEN n.rootnode ELSE n.id END
				WHERE n.state = 1 AND n.name ILIKE \'%' . q($pTerm) . '%\'
					AND ' . (int)$pJournalId . ' = ANY(r.journal_ids)';
		}else{
			$pQuery = "SELECT '" . $pTable[0] . "' || id::text as id, name FROM " . q($pTable) . ' WHERE state = 1 AND lower(name) ILIKE \'%' . q($pTerm) . '%\'';
		}
		$lCon->Execute($pQuery);
		$lCon->MoveFirst();
		$lSrcValues = array();
		while(!$lCon->Eof()) {
			$lCurrentRow = array();
			foreach ($lCon->mRs as $key => $value) {
				if(!is_int($key))
					$lCurrentRow[$key] = $value;
			}
			$lSrcValues[] = $lCurrentRow;
			$lCon->MoveNext();
		}
		return $lSrcValues;
	}
	
	function GetFieldAutoItems($pTable, $pData){
		$lCon = $this->m_con;
		//if(is_array($pData))
		$unique = $pTable[0];
		$pData = str_replace($unique, '', $pData);
		$lSql = "SELECT '$unique' || id::text as id, name FROM " . q($pTable) . ' WHERE state = 1 AND id IN ' . $pData;
		//~ echo $lSql;
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		$lSrcValues = array();
		while(!$lCon->Eof()) {
			$lCurrentRow = array();
			foreach ($lCon->mRs as $key => $value) {
				if(!is_int($key))
					$lCurrentRow[$key] = $value;
			}
			$lSrcValues[] = $lCurrentRow;
			$lCon->MoveNext();
		}
		return $lSrcValues;
	}
	function returnCategoryName($pCategoryName, $pFieldId){
		$lCon = $this->m_con;
		$lSql = 'SELECT name FROM ' . $pCategoryName . ' WHERE id = ANY(string_to_array(\'' . $pFieldId . '\', \',\')::int[])';
		//~ echo $lSql;
		$lCon->Execute($lSql);

		return $lCon->mRs['name'];
	}
}
?>