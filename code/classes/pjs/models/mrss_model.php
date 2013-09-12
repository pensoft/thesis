<?php

/**
 * A model class to handle document manipulation
 * @author peterg
 *
 */
class mRSS_Model extends emBase_Model {
	/**
	 * Returns a list of published articles
	 * @param int $pJournalId
	 */
	function GetRSSJournalArticles($pJournalId, $pRssLimit){
		$lSql = '
			SELECT d.*,
				js.title as journal_section_name, dv.id as layout_version_id,
			(SELECT aggr_concat_coma(a.author_name)
				FROM (
					SELECT (du.first_name || \' \' || du.last_name) as author_name 
					FROM pjs.document_users du
					WHERE du.document_id = d.id AND du.role_id = ' . AUTHOR_ROLE . ' AND du.state_id = 1
					ORDER BY du.ord
				) a) as authors_list,
				j.name as journal_name,
				j.short_name as journal_short_name	 
			FROM pjs.documents d
			JOIN journals j ON j.id = d.journal_id
			LEFT JOIN pjs.article_metadata am ON am.document_id = d.id
			LEFT JOIN pjs.journal_sections js ON js.id = d.journal_section_id
			LEFT JOIN pjs.document_versions dv ON dv.document_id = d.id AND dv.version_type_id = ' . DOCUMENT_VERSION_LE_TYPE . '
			WHERE d.journal_id = ' . (int)$pJournalId . '
				AND d.is_published = TRUE
			ORDER BY d.publish_date DESC
			LIMIT ' . $pRssLimit;

		$lResult = array();
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lResult[] = $this->m_con->mRs;
			$this->m_con->MoveNext();
		}
// 		var_dump($lSql);
		return $lResult;
	}
	
	function GetJournalInfo($pJournalId) {
		$lSql = 'SELECT * FROM journals WHERE id = ' . $pJournalId;
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs;
	}
}

?>