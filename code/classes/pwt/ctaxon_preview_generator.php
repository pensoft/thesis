<?php
class ctaxon_preview_generator extends csimple {
	var $m_taxonName;
	var $m_encodedTaxonName;
	var $m_templ;
	var $m_dontGetData;
	var $m_con;
	var $m_errCnt = 0;
	var $m_errMsg = '';
	var $m_dataGenerator;
	var $m_ncbiPreview;
	var $m_gbifPreview;
	var $m_bhlPreview;

	function __construct($pFieldTempl) {
		$this->m_taxonName = $pFieldTempl ['taxon_name'];
		$this->m_encodedTaxonName = urlencode($this->m_taxonName);		
		$this->m_templ = $pFieldTempl ['templ'];
		$this->m_dontGetData = false;
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->m_dataGenerator = new ctaxon_data_generator(array(
			'taxon_name' => $this->m_taxonName,
		));
		$this->LoadTaxonData();
	}	

	function SetTemplate($pTemplate) {
		$this->m_templ = $pTemplate;
	}

	function LoadTaxonData() {
		$lSql = '
			SELECT i.cached_val
			FROM pjs.articles a
			JOIN pjs.article_cached_items i ON i.id = a.xml_cache_id
			WHERE a.id = ' . (int) $this->m_documentId . '
		';
		$this->m_con->Execute($lSql);
		$this->m_documentXml = $this->m_con->mRs ['cached_val'];
	}
	
	function SetError($pErrMsg) {
		$this->m_errCnt ++;
		$this->m_errMsg = $pErrMsg;
	}


	protected function ImportPreview() {
		if ($this->m_errCnt) {
			return;
		}
		try {
			$lCon = $this->m_con;
			if (! $lCon->Execute('BEGIN TRANSACTION;')) {
				throw new Exception(getstr('pwt.couldNotBeginTransaction'));
			}
			foreach ( $this->m_instancesDetails as $lInstanceId => $lInstanceDetails ) {
				$lPreview = $this->GetInstancePreview($lInstanceId);
				$lInstanceType = $lInstanceDetails ['instance_type'];
				$this->SaveElementPreview($lInstanceId, $lInstanceType, $lPreview);				
			}
			// Whole preview
			$this->SaveElementPreview(INSTANCE_WHOLE_PREVIEW_INSTANCE_ID, INSTANCE_WHOLE_PREVIEW_TYPE, $this->m_wholeArticlePreview);			
			// Author previews
			foreach ( $this->m_authorPreviews as $lAuthorId => $lPreview ) {
				$this->SaveElementPreview($lAuthorId, INSTANCE_AUTHOR_TYPE, $lPreview);				
			}
			$this->SaveElementPreview(INSTANCE_AUTHORS_LIST_INSTANCE_ID, INSTANCE_AUTHORS_LIST_TYPE, $this->m_authorsListPreview);
			
			//Contents list previews
			$this->SaveElementPreview(0, INSTANCE_CONTENTS_LIST_TYPE, $this->m_contentsListPreview);
			//Localities list		
			$this->SaveElementPreview(0, INSTANCE_LOCALITIES_LIST_TYPE, $this->m_localitiesListPreview);
			$this->SaveArticleLocalities();
			
			if (! $lCon->Execute('COMMIT TRANSACTION;')) {
				throw new Exception(getstr('pwt.couldNotBeginTransaction'));
			}
		} catch ( Exception $lException ) {
			$lCon->Execute('ROLLBACK TRANSACTION;');
			$this->SetError($lException->getMessage());
		}
	}	
	
	protected function ExecuteTransactionalQuery($pSql){
		if (! $this->m_con->Execute($pSql)) {
			throw new Exception($this->m_con->GetLastError());
		}
	} 	

	function GetData() {
		if ($this->m_dontGetData) {
			return;
		}
		$this->GeneratePreview();
		$this->ImportPreview();
		$this->m_dontGetData = true;
	}
	
	function GenerateNCBIPreview(){
		$lNCBIData = $this->m_dataGenerator->GetNCBIData();
		if(!$lNCBIData['ncbi_taxon_id']){
			$lPreview = new csimple(array(
				'templs' => array(
					G_DEFAULT => 'article.ncbi_no_data',
				),
				'taxon_name' => $this->m_taxonName,
			));
			$this->m_ncbiPreview = $lPreview->Display();			
			return;
		}		
		$lLineage = new crs_display_array(array(
			'input_arr' => $lNCBIData['lineage'],
			'taxon_name' => $this->m_taxonName,
			'templs' => array(
				G_HEADER => 'article.ncbi_lineage_head',
				G_FOOTER => 'article.ncbi_lineage_foot',
				G_STARTRS => 'article.ncbi_lineage_start',
				G_ENDRS => 'article.ncbi_lineage_end',
				G_ROWTEMPL => 'article.ncbi_lineage_row',
				G_NODATA => 'article.ncbi_lineage_nodata',
			)
		));
		$lRelatedLinks = new crs_display_array(array(
			'input_arr' => $lNCBIData['related_links'],
			'taxon_name' => $this->m_taxonName,
			'see_all_link' => NCBI_SUBTREE_LINK . '&db=pubmed&term=' . $this->m_taxonName,
			'templs' => array(
				G_HEADER => 'article.ncbi_related_links_head',
				G_FOOTER => 'article.ncbi_related_links_foot',
				G_STARTRS => 'article.ncbi_related_links_start',
				G_ENDRS => 'article.ncbi_related_links_end',
				G_ROWTEMPL => 'article.ncbi_related_links_row',
				G_NODATA => 'article.ncbi_related_links_nodata',
			)
		));
		$lEntrezRecords = new crs_display_array(array(
			'input_arr' => $lNCBIData['entrez_records'],
			'taxon_name' => $this->m_taxonName,
			'taxon_ncbi_id' => $lNCBIData['ncbi_taxon_id'],			
			'templs' => array(
				G_HEADER => 'article.ncbi_entrez_records_head',
				G_FOOTER => 'article.ncbi_entrez_records_foot',
				G_STARTRS => 'article.ncbi_entrez_records_start',
				G_ENDRS => 'article.ncbi_entrez_records_end',
				G_ROWTEMPL => 'article.ncbi_entrez_records_row',
				G_NODATA => 'article.ncbi_entrez_records_nodata',
			)
		));
		$lPreview = new csimple(array(
			'rank' => $lNCBIData['rank'],
			'division' => $lNCBIData['division'],
			'lineage' => $lLineage,
			'related_links' => $lRelatedLinks,
			'entrez_records' => $lEntrezRecords,
			'taxon_name' => $this->m_taxonName,
			'ncbi_link' => NCBI_TAXON_URL . $lNCBIData['ncbi_taxon_id'],
			'templs' => array(
				G_DEFAULT => 'article.ncbi',
			)
		));
		$this->m_ncbiPreview = $lPreview->Display();
		return $this->m_ncbiPreview;
	}

	protected function GeneratePreview() {
		$this->RegisterAllInstances();
		$this->GenerateNCBIPreview();
		$this->GenerateGBIFPreview();
		$this->GenerateBHLPreview();
		$this->ProcessXsl();
		$this->GenerateArticleWholePreview();
		$this->GenerateArticleAuthorPreviews();
		$this->GenerateArticleContentsListPreview();
		$this->GenerateLocalitiesPreview();
	}

	function ReplaceHtmlFields($pStr) {
		return preg_replace("/\{%(.*?)%\}/e", "\$this->HtmlPrepare('\\1')", $pStr);
	}

	function HtmlPrepare($pInstanceId) {
		// trigger_error('REP ' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
		return $this->GetInstancePreview($pInstanceId);
	}

	function Display() {
		if (! $this->m_dontGetData)
			$this->GetData();
			// trigger_error('START DI ' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
		$lRet .= $this->ReplaceHtmlFields($this->m_templ);
		// trigger_error('END DI ' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
		return $lRet;
	}
}

?>