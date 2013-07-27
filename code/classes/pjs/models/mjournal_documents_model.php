<?php

/**
 * A model to implement journal documents functionality
 */
class mJournal_Documents_Model extends emBase_Model {
	function GetJournalDocuments($pJournalId) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT du.authors, du.submitter_name, du.submitter_email,
					se.abr, se.title as section_title, du.doc_id as id, du.title, ' . JOURNAL_EDITOR_ROLE . ' as role_id
					FROM pjs.v_getdocumentsandauthors  du
					LEFT JOIN pjs.journal_sections se ON se.id = du.doc_type
					WHERE du.journal = ' . (int)$pJournalId . '
					ORDER BY du.doc_id';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function GetJournalArticles($pJournalId, $pPage, $pSectionTypesArr, $pTaxon, $pSubject, $pChronological, $pGeographical, $pFromDate, $pToDate) {
		$pTaxon = substr($pTaxon, 1);
		$pSubject = substr($pSubject, 1);
		$pChronological = substr($pChronological, 1);
		$pGeographical = substr($pGeographical, 1);
		
		$lResult = array();
		$lAnd = '';
		
		if(strlen($pTaxon) > 0){
			$lAnd .= ' AND d.taxon_categories && ARRAY[' . q($pTaxon) . '] ';
		}
		if(strlen($pSubject) > 0){
			$lAnd .= ' AND d.subject_categories && ARRAY[' . q($pSubject) . '] ';
		}
		if(strlen($pChronological) > 0){
			$lAnd .= ' AND d.chronological_categories && ARRAY[' . q($pChronological) . '] ';
		}
		if(strlen($pGeographical) > 0){
			$lAnd .= ' AND d.geographical_categories && ARRAY[' . q($pGeographical) . '] ';
		}
		
		if(is_array($pSectionTypesArr) && count($pSectionTypesArr) > 0){
			$lAnd .= ' AND d.journal_section_id IN (' . q(implode(",", $pSectionTypesArr)) . ') ';
		}
		
		if(strlen($pFromDate) > 0){
			$lAnd .= ' AND d.publish_date > \'' . $pFromDate . '\'::timestamp ';
		}
		if(strlen($pToDate) > 0){
			$lAnd .= ' AND d.publish_date < \'' . $pToDate . '\'::timestamp ';
		}
			
		$lCon = $this->m_con;
		$lSql = 'SELECT d.*, du.authors as document_authors, js.title as journal_section_name, du.doi as doi
					FROM pjs.documents d
					JOIN pjs.v_getdocumentsandauthors du ON du.doc_id = d.id
					LEFT JOIN pjs.journal_sections js ON js.id = d.journal_section_id
					WHERE d.journal_id = ' . (int)$pJournalId . '
						AND d.is_published = TRUE
						' . $lAnd . '
					ORDER BY d.publish_date DESC';
		//~ var_dump($lSql);
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
	
	function GetJournalArticlesByAuthor($pJournalId, $pAuthorId){
		$lResult = array();
		$lAnd = '';
			
		$lCon = $this->m_con;
		$lSql = 'SELECT d.*, du.authors as document_authors, js.title as journal_section_name, du.doi as doi
					FROM pjs.documents d
					JOIN pjs.document_users dou ON dou.document_id = d.id AND dou.uid = ' . (int)$pAuthorId . ' AND role_id = ' . AUTHOR_ROLE . '
					JOIN pjs.v_getdocumentsandauthors du ON du.doc_id = d.id
					LEFT JOIN pjs.journal_sections js ON js.id = d.journal_section_id
					WHERE d.journal_id = ' . (int)$pJournalId . '
						AND d.is_published = TRUE
					ORDER BY d.publish_date DESC';
		//var_dump($lSql);
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
	
	function CheckJournalDocument($pJournalId, $lDocumentId) {
		$lCon = $this->m_con;
		$lSql = 'SELECT count(id) as document_exist_and_in_journal
					FROM pjs.documents
					WHERE journal_id = ' . (int)$pJournalId . '
						AND id = ' . (int)$lDocumentId;
		$lCon->Execute($lSql);
		
		return (int)$lCon->mRs['document_exist_and_in_journal'];
	}
	
	function DeleteDocument($pDocumentId) {
		$lSql = 'SELECT * FROM pjs."spDeleteDocument"(' . (int)$pDocumentId . ')';
		return $this->ArrayOfRows($lSql);
	}
	
	function GetJournalHomeArticles($pJournalId) {
		$lResult = array();
		$lAnd = '';
			
		$lCon = $this->m_con;
		$lSql = 'SELECT d.*, du.authors as document_authors, js.title as journal_section_name, du.doi as doi
					FROM pjs.documents d
					JOIN pjs.v_getdocumentsandauthors du ON du.doc_id = d.id
					LEFT JOIN pjs.journal_sections js ON js.id = d.journal_section_id
					WHERE d.journal_id = ' . (int)$pJournalId . '
						AND d.is_published = TRUE
					ORDER BY d.publish_date DESC
					LIMIT 20';
		//var_dump($lSql);
		$lCon->Execute($lSql);
		
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		
		return $lResult;
	}
	function getPublicReviewArticles($pJournalId, $pPage){
		$lResult = array();

		$lCon = $this->m_con;
		$lSql = 'SELECT d.id as document_id, dr.decision_id, dt.name as type, d.name, d.doi, d.create_date as publish_date, d.start_page, d.end_page, dv.id as version_id 
					FROM pjs.documents d
					JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id AND dr.decision_id IS NULL
					JOIN pjs.document_types dt ON dt.id = d.document_type_id
					JOIN pjs.document_versions dv ON dv.document_id = d.id AND dv.version_type_id = ' . (int) DOCUMENT_VERSION_PUBLIC_REVIEWER_TYPE . '
					WHERE d.document_review_type_id = ' . DOCUMENT_PUBLIC_PEER_REVIEW . ' AND dr.round_number = ' . REVIEW_ROUND_ONE . ' AND d.journal_id = ' . $pJournalId . ' AND dr.round_type_id = 1';
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