<?php
class taxon_data_generator extends csimple {
	var $m_taxonName;
	var $m_encodedTaxonName;
	var $m_templ;
	var $m_dontGetData;
	var $m_con;
	var $m_errCnt = 0;
	var $m_errMsg = '';
	var $m_taxonId;
	var $m_relatedLinksCount = 5;
	var $m_ncbiRelatedLinksDb = 'pubmed';

	function __construct($pFieldTempl) {
		$this->m_taxonName = $pFieldTempl ['taxon_name'];
		$this->m_encodedTaxonName = urlencode($this->m_taxonName);
		$this->m_templ = $pFieldTempl ['templ'];
		$this->m_dontGetData = false;
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->LoadTaxonData();
	}

	function SetTemplate($pTemplate) {
		$this->m_templ = $pTemplate;
	}

	function LoadTaxonData() {
		$lSql = '
			SELECT id
			FROM pjs.taxons a
			WHERE lower(translate(a.name::text, \' ,.-*\', \'\'\'\')) = lower(translate(\'' . q($this->m_taxonName) . '\', \' ,.-*\', \'\'\'\'))
		';
		$this->m_con->Execute($lSql);
		$this->m_taxonId = (int) $this->m_con->mRs ['id'];
		if (! $this->m_taxonId) {
			$this->SetError(getstr('pjs.noSuchTaxon'));
		}
	}

	function GetNCBIData() {
		$lCon = $this->m_con;
		$lSql = '
			SELECT *
			FROM pjs.taxon_ncbi_data a
			WHERE taxon_id = ' . (int) $this->m_taxonId . '
			AND lastmoddate > now() - \'' . (int) CACHE_TIMEOUT_LENGTH . ' seconds\'::interval 					
		';
		$lCon->Execute($lSql);
		if (! $lCon->mRs ['id']) {
			return $this->GenerateNCBIData();
		}
		return $this->GetNCBIDataFromDB($lCon->mRs['id']);
	}

	protected function GenerateNCBIData() {
		$lUrl = EUTILS_ESEARCH_SRV . 'term=' . $this->m_encodedTaxonName . '&retmax=1&retmode=xml&tool=' . EUTILS_TOOL_NAME . '&db=' . EUTILS_TAXONOMY_DB;
		$lQueryResult = executeExternalQuery($lUrl);
		$lNCBITaxonId = '';
		$lNCBIRank = '';
		$lNCBIDivision = '';
		$lLineageArr = array ();
		$lRelatedLinks = array ();
		$lEntrezRecords = array ();
		// Get the ncbi id of the taxon first
		if ($lQueryResult) {
			$lDom = new DOMDocument();
			if ($lDom->loadXML($lQueryResult)) {
				$lXpath = new DOMXPath($lDom);
				$lXpathQuery = '/eSearchResult/IdList/Id';
				$lXPathResult = $lXpath->query($lXpathQuery);
				if ($lXPathResult->length) {
					$lNCBITaxonId = $lXPathResult->item(0)->textContent;
				}
			}
		}
		if ($lNCBITaxonId) {
			// Fetch the lineage and the taxon details
			$lLineageResultArr = $this->GetNCBITaxonLineage($lNCBITaxonId);
			$lLineageArr = $lLineageResultArr['lineage'];
			$lNCBIRank = $lLineageResultArr['rank'];
			$lNCBIDivision = $lLineageResultArr['division'];
			$lRelatedLinks = $this->GetNCBITaxonRelatedLinks();
			$lEntrezRecords = $this->GetNCBITaxonEntrezRecords($lNCBITaxonId);
		}
		$lResult = array(
			'ncbi_taxon_id' => $lNCBITaxonId,
			'rank' => $lNCBIRank,
			'division' => $lNCBIDivision,
			'lineage' => $lLineageArr,
			'related_links' => $lRelatedLinks,
			'entrez_records' => $lEntrezRecords,
		);
		return $lResult;
	}

	private function GetNCBITaxonLineage($pTaxonNcbiId) {
		$lUrl = EUTILS_EFETCH_SRV . 'id=' . $pTaxonNcbiId . '&retmode=xml&tool=' . EUTILS_TOOL_NAME . '&db=' . EUTILS_TAXONOMY_DB;
		$lQueryResult = executeExternalQuery($lUrl);
		$lLineageArr = array ();
		$lRank = '';
		$lDivision = '';
		if ($lQueryResult) {
			$lDom = new DOMDocument();
			if ($lDom->loadXML($lQueryResult)) {
				$lXPath = new DOMXPath($lDom);
				$lXpathQuery = '/TaxaSet/Taxon/Rank';
				$lXPathResult = $lXPath->query($lXpathQuery);
				if ($lXPathResult->length) {
					$lRank = $lXPathResult->item(0)->nodeValue;
				}
				$lXpathQuery = '/TaxaSet/Taxon/Division';
				$lXPathResult = $lXPath->query($lXpathQuery);
				if ($lXPathResult->length) {
					$lDivision = $lXPathResult->item(0)->nodeValue;
				}
				$lLineageQuery = '/TaxaSet/Taxon/LineageEx/Taxon';
				$lXPathResult = $lXPath->query($lLineageQuery);
				$lLineageDetailsArr = array (
					'tax_id' => './TaxId',
					'scientific_name' => './ScientificName' 
				);
				foreach ( $lXPathResult as $lLineageNode ) {
					$lLineageResult = array (
						'tax_id' => '',
						'scientific_name' => '' 
					);
					foreach ( $lLineageDetailsArr as $lFieldName => $lFieldXPath ) {
						$lTemp = $lXPath->query($lFieldXPath, $lLineageNode);
						if ($lTemp->length) {
							$lLineageResult [$lFieldName] = $lTemp->item(0)->nodeValue;
						}
					}
					$lLineageArr = $lLineageResult [];
				}
			}
		}
		return array(
			'lineage' => $lLineageArr,
			'rank' => $lRank,
			'division' => $lDivision
		);
	}

	private function GetNCBITaxonRelatedLinks() {
		$lRelatedLinks = array ();
		// Fetch the pubmed links
		$lUrl = EUTILS_ESEARCH_SRV . 'term=' . $this->m_encodedTaxonName . '&retmode=xml&retmax=' . (int) $this->m_relatedLinksCount . '&tool=' . EUTILS_TOOL_NAME . '&db=' . $this->m_ncbiRelatedLinksDb;
		$lQueryResult = executeExternalQuery($lUrl);
		$lDom = new DOMDocument();
		if ($lDom->loadXML($lQueryResult)) {
			$lXpath = new DOMXPath($lDom);
			$lXpathQuery = '/eSearchResult/IdList/Id';
			$lXPathResult = $lXpath->query($lXpathQuery);
			foreach ( $lXPathResult as $lSingleId ) { // Vzimame id-tata i stroim linkove
				$lResourceId = $lSingleId->textContent;
				if ($lResourceId) {
					$lResourceLink = 'PUBMED_LINK_PREFIX' . $lResourceId;
					$lRelatedLinks [$lResourceId] = array (
						'title' => $lResourceId,
						'link' => $lResourceLink,
						'id' => $lResourceId,
						'db_name' => $this->m_ncbiRelatedLinksDb
					);
				}
			}
			$lLinkIds = array_keys($lRelatedLinks);
			if (is_array($lLinkIds) && count($lLinkIds)) { // Stroim title-i za linkovete
				$lTitleUrl = EUTILS_ESUMMARY_SRV . '&db=' . $this->m_ncbiRelatedLinksDb . '&id=' . implode(',', $lLinkIds) . '&retmode=xml';
				$lTitleQueryResult = executeExternalQuery($lTitleUrl);
				if ($lTitleQueryResult) {
					$lTitleDom = new DOMDocument();
					if ($lTitleDom->loadXML($lTitleQueryResult)) {
						$lTitleXpath = new DOMXPath($lTitleDom);
						$lTitleXpathQuery = '/eSummaryResult/DocSum';
						$lElements = $lTitleXpath->query($lTitleXpathQuery);
						foreach ( $lElements as $lSingleElement ) {
							$lIdXpath = './Id';
							$lIdXpathResult = $lTitleXpath->query($lIdXpath, $lSingleElement);
							if ($lIdXpathResult->length) {
								$lCurrentId = $lIdXpathResult->item(0)->textContent;
								$lCurrentTitleXpath = "./Item[@Name='Title']";
								$lCurrentTitleResult = $lTitleXpath->query($lCurrentTitleXpath, $lSingleElement);
								if ($lCurrentTitleResult->length) {
									$lCurrentTitle = $lCurrentTitleResult->item(0)->textContent;
									$lRelatedLinks [$lCurrentId] ['title'] = $lCurrentTitle;
								}
							}
						}
					}
				}
			}
		}
		return $lRelatedLinks;
	}

	private function GetNCBITaxonEntrezRecords($pTaxonNcbiId) {
		$lUrl = EUTILS_EGQUERY_SRV . '?term=txid' . $pTaxonNcbiId . '[Organism:exp]';
		// ~ var_dump($lUrl);
		$lQueryResult = executeExternalQuery($lUrl);
		$lResult = array();
		// ~ var_dump($lQueryResult);
		if ($lQueryResult) {
			$lDbs = $this->GetNCBIEntrezDBs();
			$lDom = new DOMDocument();
			if ($lDom->loadXML($lQueryResult)) {
				$lXpath = new DOMXPath($lDom);
				$lXpathQuery = '/Result/eGQueryResult/ResultItem';
				$lXPathResult = $lXpath->query($lXpathQuery);
				
				$lResultNodes = array ();
				if ($lXPathResult->length) {
					for($i = 0; $i < $lXPathResult->length; ++ $i) {
						$lCurrentNode = $lXPathResult->item($i);
						$lDbNameQuery = './DbName';
						$lTempResult = $lXpath->query($lDbNameQuery, $lCurrentNode);
						if (!$lTempResult->length) {
							continue;							
						}
						$lDbName = $lTempResult->item(0)->textContent;
						$lDbId = $this->GetNCBIEntrezDBId($lDbs, $lDbName);
						if(!$lDbId){
							continue;
						}
						$lResult[$lDbId] = 0;
						$lCountQuery = './Count';
						$lTempResult = $lXpath->query($lDbNameQuery, $lCurrentNode);
						if ($lTempResult->length) {
							$lCount = $lTempResult->item(0)->textContent;
							$lResult[$lDbId] = (int)$lCount;
						}
					}
				}				
			}
		}
		return $lResult;
	}

	private function StoreNCBIData($pData){
		$lCon = $this->m_con;
		$lSql = '
				SELECT * FROM spSaveTaxonNCBIBaseData(' . (int)$this->m_taxonId . ', \'' . q($pData['ncbi_taxon_id']) . '\', \'' . q($pData['rank']) . '\', \'' . q($pData['division']) . '\')
		';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return;
		}
		$lNCBIId = (int)$lCon->mRs['id'];
		foreach ($pData['related_links'] as $lItemId => $lItemData) {
			$lDBName = $lItemData['db_name'];
			$lTitle = $lItemData['title'];
			$lUrl = $lItemData['url'];
			$lSql = 'SELECT * FROM spSaveTaxonNCBIRelatedLink(' . $lNCBIId . ', \'' . q($lItemId) . '\', \'' . q($lDBName) . '\', \'' . q($lTitle) . '\', \'' . q($lUrl) . '\')';
			if(!$lCon->Execute($lSql)){
				$this->SetError($lCon->GetLastError());
				return;
			}			
		}
		foreach ($pData['entrez_records'] as $lDBId => $lItemCount) {
			$lSql = 'SELECT * FROM spSaveTaxonNCBIEntrezRecords(' . $lNCBIId . ', ' . (int)($lDBId) . ', ' . (int)$lItemCount . ')';
			if(!$lCon->Execute($lSql)){
				$this->SetError($lCon->GetLastError());
				return;
			}
		}
	}
	
	private function GetNCBIDataFromDB($pNCBIId){
		$lCon = $this->m_con;
		$lResult = array(
			'ncbi_taxon_id' => '',
			'rank' => '',
			'division' => '',
			'lineage' => array(),
			'related_links' => array(),
			'entrez_records' => array(),
		);
		$lSql = '
			SELECT *
			FROM pjs.taxon_ncbi_data a
			WHERE id = ' . (int) $pNCBIId . '
		';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return $lResult;
		}
		$lResult['ncbi_taxon_id'] = $lCon->mRs['ncbi_id'];
		$lResult['rank'] = $lCon->mRs['rank'];
		$lResult['division'] = $lCon->mRs['division'];
		
		$lSql = '
			SELECT * 
			FROM pjs.taxon_ncbi_lineage l
			WHERE ncbi_data_id = ' . (int) $pNCBIId . '
		';
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lResult['lineage'][$lRow['tax_id']] = array(
				'tax_id' => $lRow['tax_id'],
				'scientific_name' => $lRow['scientific_name']
			);
			$lCon->MoveNext();
		}
		
		$lSql = '
			SELECT *
			FROM pjs.taxon_ncbi_related_links l
			WHERE ncbi_data_id = ' . (int) $pNCBIId . '
		';
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lResult['related_links'][$lRow['item_id']] = array(
				'title' => $lRow['title'],
				'link' => $lRow['url'],
				'id' => $lRow['item_id'],
				'db_name' => $lRow['db_name']
			);
			$lCon->MoveNext();
		}
		
		$lSql = '
			SELECT *
			FROM pjs.taxon_ncbi_entrez_records r			
			WHERE ncbi_data_id = ' . (int) $pNCBIId . '
		';
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lResult['entrez_records'][$lRow['id']] = $lRow['records'];
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	private function GetNCBIEntrezDBs(){
		$lCon = $this->m_con;
		$lResult = array();
		$lSql = '
			SELECT *
			FROM pjs.taxon_ncbi_entrez_databases	
		';
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lResult[$lRow['id']] = $lRow['entrez_name'];
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	private function GetNCBIEntrezDBId(&$pDBs, $pDbName){
		foreach ($pDBs as $lDbId => $lDbName) {
			if($lDbName == trim($pDbName)){
				return $lDbId;
			}
		}
		return 0;
	}
	
	private function LoadSiteData($pSiteId) {
	}

	function SetError($pErrMsg) {
		$this->m_errCnt ++;
		$this->m_errMsg = $pErrMsg;
	}

	protected function ExecuteTransactionalQuery($pSql) {
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

	protected function GeneratePreview() {
		$this->RegisterAllInstances();
		$this->GenerateXsl();
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