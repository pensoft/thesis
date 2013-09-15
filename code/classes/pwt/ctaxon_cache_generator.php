<?php
class ctaxon_cache_generator {
	var $m_con;
	var $m_errCnt = 0;
	var $m_errMsg = '';
	var $m_articleId;
	var $m_articleDataIsGenerated = false;
	var $m_minId = 0;
	var $m_maxId = 0;
	function __construct($pFieldTempl) {
		//If articleId is passed the previews for all the taxa of the 
		//article will be regenerated
		$this->m_articleId = (int)$pFieldTempl ['article_id'];
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->m_minId = (int)$pFieldTempl['min_id'];
		$this->m_maxId = (int)$pFieldTempl['max_id'];
	}
	
	function SetError($pErrMsg) {
		$this->m_errCnt ++;
		$this->m_errMsg = $pErrMsg;
	}
	
	protected function CheckIfAppropriateTaxonsExist(){
		if($this->m_errCnt || $this->m_articleDataIsGenerated){
			return false;
		}
		$lCon = $this->m_con;
		$lSql = '
			SELECT t.id, t.name
			FROM pjs.taxons t
			LEFT JOIN pjs.article_cached_items c ON c.id = t.cache_id
		';
		if($this->m_articleId){
			$lSql .= '
			JOIN pjs.article_taxons at ON at.taxon_id = t.id AND at.article_id = ' . (int) $this->m_articleId . '
			LIMIT 1
			';
		}else{
			$lSql .= '
				WHERE (c.id IS NULL OR c.lastmoddate < now() - \'' . (int) CACHE_TIMEOUT_LENGTH . ' seconds\'::interval)				
			';
			if($this->m_minId > 0){
				$lSql .= ' AND t.id >= ' . (int) $this->m_minId . ' ';
			}
			if($this->m_maxId > 0){
				$lSql .= ' AND t.id < ' . (int) $this->m_maxId . ' ';
			}
			$lSql .= 'LIMIT 1';
		}
// 		var_dump($lSql);
// 		exit;
		$lCon->Execute($lSql);
		if((int)$lCon->mRs['id']){
			return true;
		}
		return false;
	}
	
	protected function GenerateCache(){
		if($this->m_errCnt){
			return;
		}
		$lCon = $this->m_con;
		$lSql = '
			SELECT t.id, t.name
			FROM pjs.taxons t
			LEFT JOIN pjs.article_cached_items c ON c.id = t.cache_id
		';
		//If article id is passed we will work in 1 big batch
		//instead of several small because keeping track is easier 
		if($this->m_articleId){
			$lSql .= '
			JOIN pjs.article_taxons at ON at.taxon_id = t.id AND at.article_id = ' . (int) $this->m_articleId . '
			ORDER BY t.name ASC
			';
		}else{
			$lSql .= '
				WHERE (c.id IS NULL OR c.lastmoddate < now() - \'' . (int) CACHE_TIMEOUT_LENGTH . ' seconds\'::interval)				
			';
			if($this->m_minId > 0){
				$lSql .= ' AND t.id >= ' . (int) $this->m_minId . ' ';
			}
			if($this->m_maxId > 0){
				$lSql .= ' AND t.id < ' . (int) $this->m_maxId . ' ';
			}
			$lSql .= ' ORDER BY t.id ASC
				LIMIT 30';
		}
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lTaxonName = $lCon->mRs['name'];
			$lTaxonId = $lCon->mRs['id'];
			echo(date("Y-m-d H:i:s") . ' ' . $lTaxonName . ' ' . $lCon->mRs['id'] . " cache generation start \n");
			$lPreviewGenerator = new ctaxon_preview_generator(array(
				'taxon_name' => $lTaxonName,
			));
			$lPreviewGenerator->GetData();
			if($lPreviewGenerator->m_errCnt){
				$this->SetError($lTaxonName . $lPreviewGenerator->m_errMsg);
				$lSql = 'INSERT INTO pjs.taxon_preview_generation_errors(taxon_id, err_msg)
					VALUES (' . (int)$lTaxonId . ', \'' . q($lPreviewGenerator->m_errMsg) . '\')';
				return;
			}
			echo(date("Y-m-d H:i:s") . ' ' . $lTaxonName . ' ' . $lCon->mRs['id'] . " cache generation end \n");			
// 			exit;
			$lCon->MoveNext();
		}
	}

	function GetData() {
		while($this->CheckIfAppropriateTaxonsExist()){
			$this->GenerateCache();
			if($this->m_articleId){
				$this->m_articleDataIsGenerated = true;
			}
		}
	}	
}

?>