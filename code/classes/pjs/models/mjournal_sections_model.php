<?php

/**
 * A model to implement journal sections functionality
 */
class mJournal_Sections_Model extends emBase_Model {
	function GetJournalSections($pJournalId) {
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT js.*,  string_agg(drt.id::text, \',\') as review_types,
							   string_agg(drt.name, \',\') as review_types_names, 
						max(pt.name) as paper_type
					FROM pjs.journal_sections js
					LEFT JOIN pjs.document_review_types drt ON drt.id = ANY (js.review_type_id)
					LEFT JOIN pwt.papertypes pt ON pt.id = js.pwt_paper_type_id
					WHERE js.journal_id = '  . (int)$pJournalId . '
					GROUP BY js.id
					ORDER BY title';
		
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			 $lResult[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return $lResult;
	}
}
?>