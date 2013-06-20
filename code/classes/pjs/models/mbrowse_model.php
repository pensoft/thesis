<?php
/**
 * A model to brows journal stories
 */
class mBrowse_model extends emBase_Model {
	
	function GetStoriesByJournal( $pJournalId ){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT s.guid, s.title, sp.pos, sp.rootnode
					FROM sid1storyprops sp 
					JOIN stories s ON s.guid = sp.guid
					JOIN languages l ON s.lang = l.code
					WHERE sp.journal_id = ' . (int)$pJournalId . ' 
						AND s.state IN (3,4)
						AND l.langid = ' . getlang() . '
						AND s.pubdate < current_timestamp
					ORDER BY sp.pos ASC';
// 		var_dump($lSql);
		$lCon->Execute($lSql);
		while(! $lCon->Eof()){
			$lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
// 		var_dump($lResult);
		return $lResult;
	}
	
	function GetJournalFeatures($pJournalId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = '(SELECT s.guid, s.title, 0 as type, sp.pos
					FROM sid1storyprops sp 
					JOIN stories s ON s.guid = sp.guid
					JOIN languages l ON s.lang = l.code
					WHERE sp.journal_id = ' . (int)$pJournalId . ' AND sp.show_in_sidebar = 1
						AND s.state IN (3,4)
						AND l.langid = ' . getlang() . '
						AND s.pubdate < current_timestamp
					ORDER BY sp.pos ASC)
					UNION 
					(SELECT 0 as guid, \'pjs.special_issues\' title, 1 as type, \'ZZZZZZZZZZZZZZZZZZZZ\' as pos
										FROM pjs.journal_issues ji
										WHERE ji.journal_id = ' . (int)$pJournalId . '
											AND ji.is_regular_issue = TRUE
											AND is_active = TRUE
											AND is_published = FALSE
										LIMIT 1)
					ORDER BY pos ASC';
		//~ UNION
		//~ (SELECT 0 as guid, \'pjs.public_articles\' title, 2 as type, \'ZZZZZZZZZZZZZZZZZZZZZ\' as pos
							//~ FROM pjs.documents d
							//~ JOIN pjs.document_review_rounds dr ON dr.id = d.current_round_id AND dr.decision_id IS NULL
							//~ JOIN pjs.document_types dt ON dt.id = d.document_type_id
							//~ WHERE d.document_review_type_id = ' . DOCUMENT_PUBLIC_PEER_REVIEW . ' AND dr.round_number = ' . REVIEW_ROUND_ONE . ' AND d.journal_id = ' . (int)$pJournalId . ' AND dr.round_type_id = 1
							//~ LIMIT 1)
		//~ echo $lSql;

		$lCon->Execute($lSql);
	
		while(!$lCon->Eof()){
			$lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
// 		var_dump($lResult);
		return $lResult;
	}
	
	function GetStoryChildrens($pStoryId, $pJournalId){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT guid, pos
					FROM sid1storyprops
					WHERE journal_id = ' . (int)$pJournalId . ' 
						AND guid = ' . $pStoryId;
		$lCon->Execute($lSql);
	
		if((int)$lCon->mRs['guid']){
			$lSql = 'SELECT sp.pos, s.guid, s.title
						FROM sid1storyprops sp
						JOIN stories s ON s.guid = sp.guid
						JOIN languages l ON s.lang = l.code
						WHERE sp.pos LIKE \'' . $lCon->mRs['pos'] . '\' || \'%\' AND char_length(sp.pos) = (char_length(\'' . $lCon->mRs['pos'] . '\') + 2)
						AND s.state IN (3,4)
							AND l.langid = ' . getlang() . '
							AND s.pubdate < current_timestamp
						ORDER BY sp.pos ASC';
			$lCon->Execute($lSql);
			
			while(!$lCon->Eof()){
				$lResult[] = $lCon->mRs;
				$lCon->MoveNext();
			}
		}
		
// 		var_dump($lResult);
		return $lResult;
	}
}
?>