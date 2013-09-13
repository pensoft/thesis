<?php
class mArticles extends emBase_Model {

	function __construct() {
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->m_con->SetFetchReturnType(PGSQL_ASSOC);
	}

	/**
	 * Retrieves a preview of an element which has its cache_id stored in the articles table
	 * 
	 * @param unknown $pColumnName
	 *        	- the name of the column which stores the cache_id of the element
	 * @param unknown $pArticleId        	
	 */
	protected function GetElementWithColumnInArticlesHtml($pColumnName, $pArticleId) {
		$lSql = '
			SELECT i.cached_val
			FROM pjs.articles a
			JOIN pjs.article_cached_items i ON i.id = a.' . $pColumnName . '
			WHERE a.id = ' . (int) $pArticleId . '
		';
		// var_dump($lSql);
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs ['cached_val'];
	}

	/**
	 * The whole preview of the article
	 * 
	 * @param unknown $pArticleId        	
	 */
	function GetArticleHtml($pArticleId, $pIpAddress = '') {
		$this->RegisterArticleMetricDetail($pArticleId, AOF_METRIC_TYPE_HTML, AOF_METRIC_DETAIL_TYPE_VIEW, $pIpAddress);
		return $this->GetElementWithColumnInArticlesHtml('preview_cache_id', $pArticleId);
	}

	/**
	 * Preview of the list of figures for the specified article
	 * 
	 * @param unknown $pArticleId        	
	 */
	function GetFiguresListHtml($pArticleId) {
		return $this->GetElementWithColumnInArticlesHtml('figures_list_cache_id', $pArticleId);
	}

	/**
	 * Preview of the list of tables for the specified article
	 * 
	 * @param unknown $pArticleId        	
	 */
	function GetTablesListHtml($pArticleId) {
		return $this->GetElementWithColumnInArticlesHtml('tables_list_cache_id', $pArticleId);
	}

	/**
	 * Preview of the list of supplementary files for the specified article
	 * 
	 * @param unknown $pArticleId        	
	 */
	function GetSupFilesListHtml($pArticleId) {
		return $this->GetElementWithColumnInArticlesHtml('sup_files_list_cache_id', $pArticleId);
	}

	/**
	 * Preview of the list of references for the specified article
	 * 
	 * @param unknown $pArticleId        	
	 */
	function GetReferencesListHtml($pArticleId) {
		return $this->GetElementWithColumnInArticlesHtml('references_list_cache_id', $pArticleId);
	}

	/**
	 * Preview of the list of all taxa for the specified article
	 * 
	 * @param unknown $pArticleId        	
	 */
	function GetTaxonListHtml($pArticleId) {
		return $this->GetElementWithColumnInArticlesHtml('taxon_list_cache_id', $pArticleId);
	}

	/**
	 * Preview of the authors list
	 * 
	 * @param unknown $pArticleId        	
	 */
	function GetAuthorsListHtml($pArticleId) {
		return $this->GetElementWithColumnInArticlesHtml('authors_list_cache_id', $pArticleId);
	}

	/**
	 * Preview of the localities list
	 * 
	 * @param unknown $pArticleId        	
	 */
	function GetLocalitiesListHtml($pArticleId) {
		return $this->GetElementWithColumnInArticlesHtml('localities_list_cache_id', $pArticleId);
	}

	/**
	 * Preview of the contents of the article
	 * 
	 * @param unknown $pArticleId        	
	 */
	function GetContentsListHtml($pArticleId) {
		return $this->GetElementWithColumnInArticlesHtml('contents_list_cache_id', $pArticleId);
	}

	/**
	 * Preview of the citation of the article
	 * 
	 * @param unknown $pArticleId        	
	 */
	function GetCitationListHtml($pArticleId) {
		return $this->GetElementWithColumnInArticlesHtml('citation_list_cache_id', $pArticleId);
	}

	/**
	 * Retrieves the preview of an instance element for the specific article
	 * 
	 * @param unknown $pTableName
	 *        	- the name of the table in which data is being stored for this types of elements
	 * @param unknown $pArticleId
	 *        	- the id of the article
	 * @param unknown $pInstanceId
	 *        	- the instance id of the element
	 */
	protected function GetInstanceElementHtml($pTableName, $pArticleId, $pInstanceId, $pRegisterMetricDetail = false, $pMetricDetailType = AOF_METRIC_DETAIL_TYPE_VIEW, $pItemType = AOF_METRIC_TYPE_FIGURE, $pIpAddress = '') {
		$lSql = '
			SELECT i.cached_val, a.id as item_id
			FROM pjs.' . $pTableName . ' a
			JOIN pjs.article_cached_items i ON i.id = a.cache_id
			WHERE a.article_id = ' . (int) $pArticleId . ' AND a.instance_id = ' . (int) $pInstanceId . '
		';
		// var_dump($lSql);
		$this->m_con->Execute($lSql);
		$lResult = $this->m_con->mRs ['cached_val'];
		if ($pRegisterMetricDetail) {
			$this->RegisterArticleMetricDetail($this->m_con->mRs ['item_id'], $pItemType, $pMetricDetailType, $pIpAddress);
		}
		
		return $lResult;
	}

	/**
	 * Single figure preview
	 * 
	 * @param unknown $pArticleId        	
	 * @param unknown $pInstanceId        	
	 */
	function GetFigureHtml($pArticleId, $pInstanceId, $pIpAddress = '') {
		return $this->GetInstanceElementHtml('article_figures', $pArticleId, $pInstanceId, true, AOF_METRIC_DETAIL_TYPE_VIEW, AOF_METRIC_TYPE_FIGURE, $pIpAddress);
	}

	/**
	 * Single table preview
	 * 
	 * @param unknown $pArticleId        	
	 * @param unknown $pInstanceId        	
	 */
	function GetTableHtml($pArticleId, $pInstanceId, $pIpAddress = '') {
		return $this->GetInstanceElementHtml('article_tables', $pArticleId, $pInstanceId, true, AOF_METRIC_DETAIL_TYPE_VIEW, AOF_METRIC_TYPE_TABLE, $pIpAddress);
	}

	/**
	 * Single supplementary file preview
	 * 
	 * @param unknown $pArticleId        	
	 * @param unknown $pInstanceId        	
	 */
	function GetSupFileHtml($pArticleId, $pInstanceId, $pIpAddress = '') {
		return $this->GetInstanceElementHtml('article_sup_files', $pArticleId, $pInstanceId, true, AOF_METRIC_DETAIL_TYPE_VIEW, AOF_METRIC_TYPE_SUP_FILE, $pIpAddress);
	}

	/**
	 * Single taxon preview
	 * 
	 * @param unknown $pTaxonName        	
	 */
	function GetTaxonHtml($pTaxonName) {
		$lSql = '
			SELECT i.cached_val
			FROM pjs.taxons a
			JOIN pjs.article_cached_items i ON i.id = a.cache_id
			JOIN spGetTaxonId(\'' . q($pTaxonName) . '\') s ON s.id  = a.id			
		';
		// var_dump($lSql);
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs ['cached_val'];
	}

	/**
	 * Single references preview
	 * 
	 * @param unknown $pArticleId        	
	 * @param unknown $pInstanceId        	
	 */
	function GetReferenceHtml($pArticleId, $pInstanceId) {
		$lSql = '
			SELECT i.cached_val
			FROM pjs.article_references a
			JOIN pjs.references r ON r.id = a.reference_id
			JOIN pjs.article_cached_items i ON i.id = r.cache_id
			WHERE a.article_id = ' . (int) $pArticleId . ' AND a.instance_id = ' . (int) $pInstanceId . '
		';
		// var_dump($lSql);
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs ['cached_val'];
	}

	/**
	 * Single author preview
	 * 
	 * @param unknown $pArticleId        	
	 * @param unknown $pAuthorUid        	
	 */
	function GetAuthorHtml($pArticleId, $pAuthorUid) {
		$lSql = '
			SELECT i.cached_val
			FROM pjs.article_authors a			
			JOIN pjs.article_cached_items i ON i.id = a.cache_id
			WHERE a.article_id = ' . (int) $pArticleId . ' AND a.author_uid = ' . (int) $pAuthorUid . '
		';
		// var_dump($lSql);
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs ['cached_val'];
	}

	/**
	 * Returns an array of all the localities in the article in the following format
	 * locality_id => array(
	 * id => locality_id
	 * longitude => locality_longitude,
	 * latitude => locality_latitude,
	 * instance_ids => array containing the ids of all instances in which the locality is cited
	 * )
	 */
	function GetLocalities($pArticleId) {
		$lResult = array ();
		$lSql = '
			SELECT l.id, l.longitude, l.latitude, il.instance_id
			FROM pjs.article_localities l
			LEFT JOIN pjs.article_instance_localities il ON il.locality_id = l.id
			WHERE l.article_id = ' . (int) $pArticleId . '
			ORDER BY id ASC
		';
		// var_dump($lSql);
		$this->m_con->Execute($lSql);
		while ( ! $this->m_con->Eof() ) {
			$lLocalityId = $this->m_con->mRs ['id'];
			if (! array_key_exists($lLocalityId, $lResult)) {
				$lResult [$lLocalityId] = array (
					'id' => $lLocalityId,
					'longitude' => (float) $this->m_con->mRs ['longitude'],
					'latitude' => (float) $this->m_con->mRs ['latitude'],
					'instance_ids' => array () 
				);
			}
			$lInstanceId = (int) $this->m_con->mRs ['instance_id'];
			if ($lInstanceId) {
				$lResult [$lLocalityId] ['instance_ids'] [] = $lInstanceId;
			}
			
			$this->m_con->MoveNext();
		}
		return $lResult;
	}

	/**
	 * Returns whether the article has figures/tables/localities/taxa/references/data objects
	 * 
	 * @param unknown $pArticleId        	
	 * @return multitype:unknown Ambigous <>
	 */
	function GetObjectExistenceFields($pArticleId) {
		$lSql = '
			SELECT has_figures::int as has_figures, has_tables::int as has_tables, has_localities::int as has_localities, has_taxa::int as has_taxa,
				has_data::int as has_data, has_references::int as has_references
			FROM pjs.articles 
			WHERE id = ' . (int) $pArticleId . '
		';
		$this->m_con->Execute($lSql);
		$lRow = $this->m_con->mRs;
		$lResult = array (
			'has_figures' => $lRow ['has_figures'],
			'has_tables' => $lRow ['has_tables'],
			'has_localities' => $lRow ['has_localities'],
			'has_taxa' => $lRow ['has_taxa'],
			'has_data' => $lRow ['has_data'],
			'has_references' => $lRow ['has_references'] 
		);
		return $lResult;
	}

	function GetMetadata($pArticleId) {
		$lSql = 'SELECT to_char(d.approve_date, \'DD-MM-YYYY\') as approve_date, 
					to_char(d.publish_date, \'DD-MM-YYYY\') as publish_date, 
					to_char(d.create_date, \'DD-MM-YYYY\') as create_date,
				EXTRACT(year FROM d.publish_date) as pubyear,
				j.name as journal_name, d.doi, d.start_page, d.end_page,
				d.name as article_title,
				i."number" as issue_number,
				d.id as article_id
		FROM pjs.documents d
		LEFT JOIN pjs.journal_issues i ON i.id = d.issue_id
		LEFT JOIN public.journals j ON j.id = i.journal_id
		WHERE d.id = ' . $pArticleId . '	
	';
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs;
	}

	/**
	 * Registers a view/download event for the specified item
	 * 
	 * @param unknown $pItemId
	 *        	- the id of the item (in case of PDF/HTML/XML - the id of the article)
	 * @param unknown $pItemType
	 *        	- the type of the item (figure/table/pdf ...)
	 * @param unknown $pDetailType
	 *        	- view / dl
	 * @param unknown $pIpAddress
	 *        	- the ip address of the user who caused the event
	 */
	function RegisterArticleMetricDetail($pItemId, $pItemType, $pDetailType, $pIpAddress = '') {
		if ($pIpAddress == '') {
			$pIpAddress = $_SERVER ['REMOTE_ADDR'];
		}
		$lSql = 'SELECT * 
				FROM spRegisterArticleMetricDetail(' . (int) $pItemId . ', ' . (int) $pItemType . ', ' . (int) $pDetailType . ', \'' . q($pIpAddress) . '\');
		';
// 		var_dump($lSql);
// 		exit;
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs ['id'];
	}
	
	protected function GetArticleItemTableNameByMetricType($pItemType){
		$lTableName = '';
		switch ($pItemType) {
			default :
			case (int) AOF_METRIC_TYPE_FIGURE :
				$lTableName = 'pjs.article_figures';
				break;
			case (int) AOF_METRIC_TYPE_TABLE :
				$lTableName = 'pjs.article_tables';
				break;
			case (int) AOF_METRIC_TYPE_SUP_FILE :
				$lTableName = 'pjs.article_sup_files';
				break;
		}
		return $lTableName;
	}
	
	function GetItemIdFromInstanceIdAndItemType($pInstanceId, $pItemType) {
		$lTableName = $this->GetArticleItemTableNameByMetricType($pItemType);
		$lSql = '
			SELECT id 
			FROM ' . $lTableName . '
			WHERE instance_id = ' . (int)$pInstanceId . '	
		';
// 		var_dump($lSql);
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs['id'];
	}
	
	function GetSupplFileOriginalName($pId){
		$lSql = '
			SELECT file_name
			FROM pjs.article_sup_files
			WHERE id = ' . (int)$pId . '
		';
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs['file_name'];
	}
	
	function GetFigurePicId($pInstanceId){
		$lSql = '
			SELECT pic_id
			FROM pjs.article_figures
			WHERE id = ' . (int)$pInstanceId . '
		';
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs['pic_id'];
	}
	
	protected function GetArticleSpecialMetricDetails($pArticleId, $pMetricType){
		$lSql = '
			SELECT view_cnt, view_unique_cnt, download_cnt, download_unique_cnt
			FROM pjs.article_metrics
			WHERE item_id = ' . (int)$pArticleId . ' AND item_type = ' . (int)$pMetricType . '
		';
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs;
	}
	
	function GetArticleHtmlMetricDetails($pArticleId){		
		return $this->GetArticleSpecialMetricDetails($pArticleId, AOF_METRIC_TYPE_HTML);
	}
	
	function GetArticleXmlMetricDetails($pArticleId){
		return $this->GetArticleSpecialMetricDetails($pArticleId, AOF_METRIC_TYPE_XML);
	}
	
	function GetArticlePdfMetricDetails($pArticleId){
		return $this->GetArticleSpecialMetricDetails($pArticleId, AOF_METRIC_TYPE_PDF);
	}
	
	protected function GetArticleItemsMetrics($pArticleId, $pItemType){
		$lTableName = $this->GetArticleItemTableNameByMetricType($pItemType);		
		$lSql = '
				SELECT f.instance_id, f.display_label, m.view_cnt, m.view_unique_cnt, m.download_cnt, m.download_unique_cnt
				FROM pjs.article_metrics m
				JOIN ' . $lTableName . ' f ON f.id = m.item_id 
				WHERE f.article_id = ' . (int)$pArticleId . ' AND m.item_type = ' . (int)$pItemType . '
						AND (m.view_cnt > 0 OR m.download_cnt > 0) 
				ORDER BY m.view_cnt DESC, m.view_unique_cnt DESC, m.download_cnt DESC, m.download_unique_cnt DESC
		';
// 		var_dump($lSql);
		return $this->ArrayOfRows($lSql);
	}
	
	function GetArticleFiguresMetrics($pArticleId){
		return $this->GetArticleItemsMetrics($pArticleId, (int)AOF_METRIC_TYPE_FIGURE);
	}
	
	function GetArticleTablesMetrics($pArticleId){
		return $this->GetArticleItemsMetrics($pArticleId, (int)AOF_METRIC_TYPE_TABLE);
	}
	
	function GetArticleSupplFilesMetrics($pArticleId){
		return $this->GetArticleItemsMetrics($pArticleId, (int)AOF_METRIC_TYPE_SUP_FILE);
	}
	
	function GetPlatePartItemIds($pPlateItemId){
		$lSql = '
				SELECT d.id
				FROM pjs.article_figures d
				JOIN pjs.article_figures p ON p.instance_id = d.plate_instance_id
				WHERE p.id = ' . (int)$pPlateItemId . '
		';
		return $this->ArrayOfRows($lSql);
	}
}

?>