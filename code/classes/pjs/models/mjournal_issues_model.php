<?php
/**
 * A model to implement journal issues functionality
 */
class mJournal_Issues_Model extends emBase_Model {
	
	function GetIssueInfo($pJournalId, $pIssueId, $pIssueNum){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spGetIssueInfo(' . (int)$pJournalId . ', ' . (int)$pIssueId . ', ' . (int)$pIssueNum . ')';
		
		$lCon->Execute($lSql);
		return $lCon->mRs;
	}
	
	function GetJournalIssueDocuments($pJournalId, $pIssueId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT array_to_string(array_agg(coalesce( du.names, \'\')), \', \') as editors_names, 
						ji.id as issue_id, ji.name as issue_name, max(js.title) as journal_section_name, 
						max(js.abr) as journal_section_abbreviation, 
						ji.description as issue_description, ji.is_regular_issue, d.*,
						max(j.name) as journal_name, ji.year, ji.volume
					FROM pjs.journal_issues ji
					JOIN pjs.documents d ON d.issue_id = ji.id AND d.is_published = TRUE
					LEFT JOIN journals j ON j.id = ji.journal_id
					LEFT JOIN pjs.v_getdocumentsandusers du ON du.doc_id = d.id AND du.role = ' . JOURNAL_EDITOR_ROLE . '
					LEFT JOIN pjs.journal_sections js ON js.id = d.journal_section_id
					WHERE ji.journal_id = ' . (int)$pJournalId . '
						AND ji.id = ' . (int)$pIssueId . '
						AND ji.is_published = TRUE
						AND ji.is_active = TRUE
					GROUP BY d.id, ji.id
					ORDER BY d.publish_date DESC';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function GetJournalBackIssues($pJournalId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT ji.*, count(d.id) as count_documents, j.name as journal_name, 
						(CASE WHEN ji.is_published = TRUE THEN 1 ELSE 0 END) as ispublished,
						(CASE WHEN ji.is_current = TRUE THEN 1 ELSE 0 END) as iscurrent
					FROM pjs.journal_issues ji
					LEFT JOIN pjs.documents d ON d.issue_id = ji.id
					JOIN journals j ON j.id = ji.journal_id
					WHERE ji.journal_id = ' . (int)$pJournalId . '
						AND ji.is_published = TRUE
					GROUP BY ji.id, j.name
					ORDER BY ji.date_published ASC';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function GetJournalFutureIssues($pJournalId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT ji.*, count(d.id) as count_documents, j.name as journal_name, (CASE WHEN ji.is_published = TRUE THEN 1 ELSE 0 END) as ispublished
					FROM pjs.journal_issues ji
					LEFT JOIN pjs.documents d ON d.issue_id = ji.id
					JOIN journals j ON j.id = ji.journal_id
					WHERE ji.journal_id = ' . (int)$pJournalId . '
						AND ji.is_published = FALSE
					GROUP BY ji.id, j.name
					ORDER BY ji.date_published DESC';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function GetJournalSpecialIssues($pJournalId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT ji.name as title, ji.description, ji.special_issue_editors
					FROM pjs.journal_issues ji
					WHERE ji.journal_id = ' . (int)$pJournalId . '
						AND ji.is_regular_issue = TRUE
						AND is_active = TRUE
						AND is_published = FALSE';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function AddPicToIssue($pPicId, $pIssueId){
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spAddPicToIssue(' . (int)$pPicId . ', ' . (int)$pIssueId . ') as old_pic_id';
		$lCon->Execute($lSql);
		if((int)$lCon->mRs['old_pic_id']){
			$lFilesModel = new mFiles_Model();
			$lOperResult = $lFilesModel->DeletePic((int)$lCon->mRs['old_pic_id']);
			if(!(int)$lOperResult['err_cnt'])
				DeletePicFiles((int)$lCon->mRs['old_pic_id']);
		}
	}
	
	function GetEditIssueDocuments($pJournalId, $pIssueId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as submitting_author, 
						js.title as journal_section_name, 
						js.abr as journal_section_abbreviation, 
						d.*
					FROM pjs.documents d
					JOIN usr u ON u.id = d.submitting_author_id
					LEFT JOIN pjs.journal_sections js ON js.id = d.journal_section_id
					WHERE d.journal_id = ' . (int)$pJournalId . '
						AND d.issue_id = ' . (int)$pIssueId . '
						
					ORDER BY d.issue_ord ASC';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function RemoveDocumentFromIssue($pJournalId, $pIssueId, $pDocumentId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spDeleteDocumentFromIssue(' . (int)$pJournalId . ', ' . (int)$pIssueId . ', ' . (int)$pDocumentId . ')';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function MoveDocumentUpDown($pJournalId, $pIssueId, $pDocumentId, $pDirection){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spMoveDocumentUpDown(' . (int)$pJournalId . ', ' . (int)$pIssueId . ', ' . (int)$pDocumentId . ', ' . (int)$pDirection . ')';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function SaveDocumentPages($pJournalId, $pIssueId, $pDocumentId, $pDirection){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spMoveDocumentUpDown(' . (int)$pJournalId . ', ' . (int)$pIssueId . ', ' . (int)$pDocumentId . ', ' . (int)$pDirection . ')';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function GetJournalIssues($pJournalId, $pOnlySpecials = 0, $pYear = 0, $pVolume = 0, $pPage){
		$lResult = array();
		$lWhereAnd = '';
		if((int)$pOnlySpecials)
			$lWhereAnd .= ' AND i.is_regular_issue = TRUE ';
		if((int)$pYear)
			$lWhereAnd .= ' AND i.year::int = ' . $pYear . ' ';
			
		if((int)$pVolume)
			$lWhereAnd = ' AND i.volume = ' . (int)$pVolume . ' ';
		$lCon = $this->m_con;
		$lSql = 'SELECT i.id, i.is_regular_issue::int, i.name as issue_title, count(d.id) as count_documents,
						sum( d.number_of_pages) as count_pages, sum( d.number_of_color_pages) as count_color_pages,
						i.previewpicid, i.special_issue_editors, i.price, max(j.name) as journal_name, i.year, i.volume,
						max(array_to_string(sc.name, \',\')) as subject_names, max(array_to_string(sc.cnt, \',\')) subject_cnt, 
						max(array_to_string(tc.name, \',\')) as taxon_names, max(array_to_string(tc.cnt, \',\')) as taxon_cnt,
						max(array_to_string(gc.name, \',\')) as geographical_names, max(array_to_string(gc.cnt, \',\')) as  geographical_cnt
				FROM pjs.journal_issues i
				LEFT JOIN pjs.documents d ON d.issue_id = i.id AND d.is_published = TRUE
				LEFT JOIN journals j ON j.id = i.journal_id
				LEFT JOIN (SELECT array_agg(c.name) as name, array_agg(c.cnt) as cnt, c.issue_id
							FROM ( SELECT d.issue_id, max(s.name) as name, count(*) as cnt
									FROM pjs.documents d
									JOIN subject_categories s on (s.id = ANY(d.subject_categories))
									GROUP BY d.issue_id, s.id
								) c
							GROUP BY c.issue_id
				) sc ON (i.id = sc.issue_id)
				LEFT JOIN (SELECT array_agg(c.name) as name, array_agg(c.cnt) as cnt, c.issue_id
							FROM ( SELECT d.issue_id, max(s.name) as name, count(*) as cnt
									FROM pjs.documents d
									JOIN taxon_categories s on (s.id = ANY(d.taxon_categories))
									GROUP BY d.issue_id, s.id
								) c
							GROUP BY c.issue_id
				) tc ON (i.id = tc.issue_id)
				LEFT JOIN (SELECT array_agg(c.name) as name, array_agg(c.cnt) as cnt, c.issue_id
							FROM ( SELECT d.issue_id, max(s.name) as name, count(*) as cnt
									FROM pjs.documents d
									JOIN geographical_categories s on (s.id = ANY(d.geographical_categories))
									GROUP BY d.issue_id, s.id
								) c
							GROUP BY c.issue_id
				) gc ON (i.id = gc.issue_id)
				WHERE i.is_published = TRUE 
					AND i.is_active = TRUE 
					AND i.journal_id = ' . (int)$pJournalId . '
					' . $lWhereAnd . '
				GROUP BY i.id
				ORDER BY i.date_published DESC';
		
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