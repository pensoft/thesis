<?php
class mArticles extends emBase_Model {
	function __construct(){
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->m_con->SetFetchReturnType(PGSQL_ASSOC);
	}
	
	/**
	 * Retrieves a preview of an element which has its cache_id stored in the articles table
	 * @param unknown $pColumnName - the name of the column which stores the cache_id of the element
	 * @param unknown $pArticleId
	 */
	protected function GetElementWithColumnInArticlesHtml($pColumnName, $pArticleId){
		$lSql = '
			SELECT i.cached_val
			FROM pjs.articles a
			JOIN pjs.article_cached_items i ON i.id = a.' . $pColumnName . '
			WHERE a.id = ' . (int)$pArticleId . '
		';
// 				var_dump($lSql);
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs['cached_val'];
	}
	
	/**
	 * The whole preview of the article
	 * @param unknown $pArticleId
	 */
	function GetArticleHtml($pArticleId){
		return $this->GetElementWithColumnInArticlesHtml('preview_cache_id', $pArticleId);
	}
	
	/**
	 * Preview of the list of figures for the specified article
	 * @param unknown $pArticleId
	 */
	function GetFiguresListHtml($pArticleId){
		return $this->GetElementWithColumnInArticlesHtml('figures_list_cache_id', $pArticleId);
	}
	
	/**
	 * Preview of the list of tables for the specified article
	 * @param unknown $pArticleId
	 */
	function GetTablesListHtml($pArticleId){
		return $this->GetElementWithColumnInArticlesHtml('tables_list_cache_id', $pArticleId);
	}
	
	/**
	 * Preview of the list of supplementary files for the specified article
	 * @param unknown $pArticleId
	 */
	function GetSupFilesListHtml($pArticleId){
		return $this->GetElementWithColumnInArticlesHtml('sup_files_list_cache_id', $pArticleId);
	}
	
	/**
	 * Preview of the list of references for the specified article
	 * @param unknown $pArticleId
	 */
	function GetReferencesListHtml($pArticleId){
		return $this->GetElementWithColumnInArticlesHtml('references_list_cache_id', $pArticleId);
	}
	
	/**
	 * Preview of the list of all taxa for the specified article
	 * @param unknown $pArticleId
	 */
	function GetTaxonListHtml($pArticleId){
		return $this->GetElementWithColumnInArticlesHtml('taxon_list_cache_id', $pArticleId);
	}
	
	/**
	 * Preview of the authors list 
	 * @param unknown $pArticleId
	 */
	function GetAuthorsListHtml($pArticleId){
		return $this->GetElementWithColumnInArticlesHtml('authors_list_cache_id', $pArticleId);
	}
	
	/**
	 * Preview of the localities list
	 * @param unknown $pArticleId
	 */
	function GetLocalitiesListHtml($pArticleId){
		return $this->GetElementWithColumnInArticlesHtml('localities_list_cache_id', $pArticleId);
	}
	
	/**
	 * Preview of the contents of the article
	 * @param unknown $pArticleId
	 */
	function GetContentsListHtml($pArticleId){
		return $this->GetElementWithColumnInArticlesHtml('contents_list_cache_id', $pArticleId);
	}
	
	/**
	 * Preview of the citation of the article
	 * @param unknown $pArticleId
	 */
	function GetCitationListHtml($pArticleId){
		return $this->GetElementWithColumnInArticlesHtml('citation_list_cache_id', $pArticleId);
	}
	
	/**
	 * Retrieves the preview of an instance element for the specific article
	 * @param unknown $pTableName - the name of the table in which data is being stored for this types of elements
	 * @param unknown $pArticleId - the id of the article
	 * @param unknown $pInstanceId - the instance id of the element
	 */
	protected function GetInstanceElementHtml($pTableName, $pArticleId, $pInstanceId){
		$lSql = '
			SELECT i.cached_val
			FROM pjs.' . $pTableName . ' a
			JOIN pjs.article_cached_items i ON i.id = a.cache_id
			WHERE a.article_id = ' . (int)$pArticleId . ' AND a.instance_id = ' . (int)$pInstanceId . '
		';
		// 		var_dump($lSql);
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs['cached_val'];
	}
	
	/**
	 * Single figure preview
	 * @param unknown $pArticleId
	 * @param unknown $pInstanceId
	 */
	function GetFigureHtml($pArticleId, $pInstanceId){
		return $this->GetInstanceElementHtml('article_figures', $pArticleId, $pInstanceId);
	}
	
	/**
	 * Single table preview
	 * @param unknown $pArticleId
	 * @param unknown $pInstanceId
	 */
	function GetTableHtml($pArticleId, $pInstanceId){
		return $this->GetInstanceElementHtml('article_tables', $pArticleId, $pInstanceId);
	}
	
	/**
	 * Single supplementary file preview
	 * @param unknown $pArticleId
	 * @param unknown $pInstanceId
	 */
	function GetSupFileHtml($pArticleId, $pInstanceId){
		return $this->GetInstanceElementHtml('article_sup_files', $pArticleId, $pInstanceId);
	}
	
	/**
	 * Single taxon preview
	 * @param unknown $pTaxonName
	 */
	function GetTaxonHtml($pTaxonName){
		$lSql = '
			SELECT i.cached_val
			FROM pjs.taxons a
			JOIN pjs.article_cached_items i ON i.id = a.cache_id
			JOIN spGetTaxonId(\'' . q($pTaxonName) . '\') s ON s.id  = a.id			
		';
// 				var_dump($lSql);
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs['cached_val'];
	}
	
	/**
	 * Single references preview
	 * @param unknown $pArticleId
	 * @param unknown $pInstanceId
	 */
	function GetReferenceHtml($pArticleId, $pInstanceId){
		$lSql = '
			SELECT i.cached_val
			FROM pjs.article_references a
			JOIN pjs.references r ON r.id = a.reference_id
			JOIN pjs.article_cached_items i ON i.id = r.cache_id
			WHERE a.article_id = ' . (int)$pArticleId . ' AND a.instance_id = ' . (int)$pInstanceId . '
		';
		// 		var_dump($lSql);
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs['cached_val'];
	}
	
	/**
	 * Single author preview
	 * @param unknown $pArticleId
	 * @param unknown $pAuthorUid
	 */
	function GetAuthorHtml($pArticleId, $pAuthorUid){
		$lSql = '
			SELECT i.cached_val
			FROM pjs.article_authors a			
			JOIN pjs.article_cached_items i ON i.id = a.cache_id
			WHERE a.article_id = ' . (int)$pArticleId . ' AND a.author_uid = ' . (int)$pAuthorUid . '
		';
		// 		var_dump($lSql);
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs['cached_val'];
	}
	
	/**
	 * Returns an array of all the localities in the article in the following format
	 * 	locality_id => array(
	 * 		id => locality_id
	 * 		longitude => locality_longitude,
	 * 		latitude => locality_latitude,
	 * 		instance_ids => array containing the ids of all instances in which the locality is cited
	 * )
	 */
	function GetLocalities($pArticleId){
		$lResult = array();
		$lSql = '
			SELECT l.id, l.longitude, l.latitude, il.instance_id
			FROM pjs.article_localities l
			LEFT JOIN pjs.article_instance_localities il ON il.locality_id = l.id
			WHERE l.article_id = ' . (int)$pArticleId . '
			ORDER BY id ASC
		';		
// 		var_dump($lSql);
		$this->m_con->Execute($lSql);
		while(!$this->m_con->Eof()){
			$lLocalityId = $this->m_con->mRs['id'];
			if(!array_key_exists($lLocalityId, $lResult)){
				$lResult[$lLocalityId] = array(
					'id' => $lLocalityId,
					'longitude' => (float)$this->m_con->mRs['longitude'],
					'latitude' => (float)$this->m_con->mRs['latitude'],
					'instance_ids' => array(),
				);
			}
			$lInstanceId = (int)$this->m_con->mRs['instance_id'];
			if($lInstanceId){
				$lResult[$lLocalityId]['instance_ids'][] = $lInstanceId;
			}
			
			$this->m_con->MoveNext();
		}
		return $lResult;
	}
	
	/**
	 * Returns whether the article has figures/tables/localities/taxa/references/data objects
	 * @param unknown $pArticleId
	 * @return multitype:unknown Ambigous <>
	 */
	function GetObjectExistenceFields($pArticleId){
		$lSql = '
			SELECT has_figures::int as has_figures, has_tables::int as has_tables, has_localities::int as has_localities, has_taxa::int as has_taxa,
				has_data::int as has_data, has_references::int as has_references
			FROM pjs.articles 
			WHERE id = ' . (int)$pArticleId . '
		';
		$this->m_con->Execute($lSql);
		$lRow = $this->m_con->mRs;
		$lResult = array(
			'has_figures' => $lRow['has_figures'],
			'has_tables' => $lRow['has_tables'],
			'has_localities' => $lRow['has_localities'],
			'has_taxa' => $lRow['has_taxa'],
			'has_data' => $lRow['has_data'],
			'has_references' => $lRow['has_references'],
		);
		return $lResult;
		
	}
}

?>