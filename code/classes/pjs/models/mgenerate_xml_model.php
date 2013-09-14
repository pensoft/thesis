<?php

/**
 * A model class to handle document manipulation
 * @author peterg
 *
 */
class mGenerate_XML_Model extends emBase_Model {
	/**
	 * Returns a list of published articles
	 * @param int $pJournalId
	 */
	function GetNLMXML($pDocumentId){
		$lSql = '
			SELECT 
				aci.cached_val 
			FROM pjs.articles a
			JOIN pjs.article_cached_items aci ON aci.article_id = a.id AND item_type = ' . NLM_XML_ITEM_TYPE . '
			WHERE a.id = ' . $pDocumentId;

		$lResult = array();
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs['cached_val'];
	}
}

?>