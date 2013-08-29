<?php
class ctaxon_data_generator extends csimple {
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
			FROM spGetTaxonId(\'' . q($this->m_taxonName) . '\');
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
// 		var_dump($lQueryResult);
// 		exit;
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
		$this->StoreNCBIData($lResult);
		return $lResult;
	}

	protected function GetNCBITaxonLineage($pTaxonNcbiId) {
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
					$lLineageResult['link'] = NCBI_TAXONOMY_LINEAGE_URL . '&id=' . $lLineageArr['tax_id'];
					$lLineageArr[] = $lLineageResult;
				}
			}
		}
		return array(
			'lineage' => $lLineageArr,
			'rank' => $lRank,
			'division' => $lDivision
		);
	}

	protected function GetNCBITaxonRelatedLinks() {
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
					$lResourceLink = PUBMED_LINK_PREFIX . $lResourceId;
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

	protected function GetNCBITaxonEntrezRecords($pTaxonNcbiId) {
		$lUrl = EUTILS_EGQUERY_SRV . '?term=txid' . $pTaxonNcbiId . '[Organism:exp]';
// 		var_dump($lUrl);
		$lQueryResult = executeExternalQuery($lUrl);
		$lResult = array ();
		
		if ($lQueryResult) {
			$lDbs = $this->GetNCBIEntrezDBs();
// 			var_dump($lDbs);
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
						if (! $lTempResult->length) {
							continue;							
						}
						$lDbName = $lTempResult->item(0)->textContent;
						$lDbId = $this->GetNCBIEntrezDBId($lDbs, $lDbName);		
// 						var_dump($lDbName);				
						if(!$lDbId){
							continue;
						}
						
						$lResult[$lDbId] = array(
							'records' => 0,
							'db_name' => $lDbName,
							'db_display_name' => $lDbs[$lDbId]['db_display_name']
						);
						$lCountQuery = './Count';
						$lTempResult = $lXpath->query($lCountQuery, $lCurrentNode);
						if ($lTempResult->length) {
							$lCount = $lTempResult->item(0)->textContent;
							$lResult[$lDbId]['records'] = (int)$lCount;							
						}
					}
				}				
			}
		}
		return $lResult;
	}

	protected function StoreNCBIData($pData){
// 		var_dump($pData);
// 		exit;
		$lCon = $this->m_con;
		$lSql = '
				SELECT * FROM spSaveTaxonNCBIBaseData(' . (int)$this->m_taxonId . ', \'' . q($pData['ncbi_taxon_id']) . '\', \'' . q($pData['rank']) . '\', \'' . q($pData['division']) . '\')
		';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return;
		}
		$lNCBIId = (int)$lCon->mRs['id'];
		$lResultFound = $pData['ncbi_taxon_id'] ? 1 : 0;
		$this->StoreSiteData(NCBI_SITE_ID, $lResultFound, NCBI_TAXON_URL . $pData['ncbi_taxon_id']);		
		 
		
		foreach ($pData['lineage'] as $lLineageData) {
			$lTaxId = $lLineageData['tax_id'];
			$lScientificName= $lLineageData['scientific_name'];			
			$lSql = 'SELECT * FROM spSaveTaxonNCBILineage(' . $lNCBIId . ', \'' . q($lTaxId) . '\', \'' . q($lScientificName) . '\')';
			if(!$lCon->Execute($lSql)){
				$this->SetError($lCon->GetLastError());
				return;
			}
		}
		foreach ($pData['related_links'] as $lItemId => $lItemData) {
			$lDBName = $lItemData['db_name'];
			$lTitle = $lItemData['title'];
			$lUrl = $lItemData['link'];
			$lSql = 'SELECT * FROM spSaveTaxonNCBIRelatedLink(' . $lNCBIId . ', \'' . q($lItemId) . '\', \'' . q($lDBName) . '\', \'' . q($lTitle) . '\', \'' . q($lUrl) . '\')';			
			if(!$lCon->Execute($lSql)){
				$this->SetError($lCon->GetLastError());
				return;
			}			
		}
		foreach ($pData['entrez_records'] as $lDBId => $lDBData) {
			$lSql = 'SELECT * FROM spSaveTaxonNCBIEntrezRecords(' . $lNCBIId . ', ' . (int)($lDBId) . ', ' . (int)$lDBData['records'] . ')';
			if(!$lCon->Execute($lSql)){
				$this->SetError($lCon->GetLastError());
				return;
			}
		}
	}
	
	protected function GetNCBIDataFromDB($pNCBIId){
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
// 		var_dump($lSql);
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lResult['lineage'][$lRow['tax_id']] = array(
				'tax_id' => $lRow['tax_id'],
				'scientific_name' => $lRow['scientific_name'],
				'link' => NCBI_TAXONOMY_LINEAGE_URL . '&id=' . $lRow['tax_id'],
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
			SELECT r.*, d.display_name as db_display_name, d.entrez_name
			FROM pjs.taxon_ncbi_entrez_records r
			JOIN pjs.taxon_ncbi_entrez_databases d ON d.id = r.db_id
			WHERE r.ncbi_data_id = ' . (int) $pNCBIId . ' and d.id not in (3, 4) 
		';
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lResult['entrez_records'][$lRow['db_id']] = array(
				'records' => (int)$lRow['records'],
				'db_display_name' => $lRow['db_display_name'],
				'db_name' => $lRow['entrez_name'],
			);
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	protected function GetNCBIEntrezDBs(){
		$lCon = $this->m_con;
		$lResult = array();
		$lSql = '
			SELECT *
			FROM pjs.taxon_ncbi_entrez_databases	
		';
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lResult[$lRow['id']] = array(
				'entrez_name' => $lRow['entrez_name'],
				'db_display_name' => $lRow['display_name'],
			);
			$lCon->MoveNext();
		}		
		return $lResult;
	}
	
	protected function GetNCBIEntrezDBId(&$pDBs, $pDbName){
		foreach ($pDBs as $lDbId => $lDBData) {
			if($lDBData['entrez_name'] == trim($pDbName)){
				return $lDbId;
			}
		}
		return 0;
	}
	
	function GetGBIFData() {
		$lCon = $this->m_con;
		$lSql = '
			SELECT *
			FROM pjs.taxon_gbif_data a
			WHERE taxon_id = ' . (int) $this->m_taxonId . '
			AND lastmoddate > now() - \'' . (int) CACHE_TIMEOUT_LENGTH . ' seconds\'::interval
		';
		$lCon->Execute($lSql);
		if (! $lCon->mRs ['id']) {
			return $this->GenerateGBIFData();
		}
		return $this->GetGBIFDataFromDB($lCon->mRs['id']);
	}
	
	protected function GenerateGBIFData(){
		$lUrl = TAXON_MAP_SRV . $this->m_encodedTaxonName;
		$lQueryResult = executeExternalQuery($lUrl);		
		$lIframeSrc = '';
		$lTaxonId = '';
		if( $lQueryResult ){
			$lDom = new DOMDocument();
			if($lDom->loadXML($lQueryResult)){
				$lXpath = new DOMXPath($lDom);
				$lXpathQuery = '/taxa/taxon/mapHTML';
				$lXPathResult = $lXpath->query($lXpathQuery);
				if( $lXPathResult->length ){
					$lMapIframe = $lXPathResult->item(0);
					if( $lMapIframe ){						
						$lIframeSrc = $this->GetGBIFMapIframeSrc($lMapIframe->textContent);						
					}
				}
				$lXpathQuery = '/taxa/taxon/id';
				$lXPathResult = $lXpath->query($lXpathQuery);
				if( $lXPathResult->length ){
					$lTaxonId = $lXPathResult->item(0)->textContent;					
				}
			}
		}
		$lResult = array(
			'map_iframe_src' => $lIframeSrc,
			'gbif_taxon_id' => $lTaxonId,
		);
		$this->StoreGBIFData($lResult);
		return $lResult;
	}
	
	protected function GetGBIFMapIframeSrc($pIframeHTML){		
		
		$lDom = new DOMDocument();
		if($lDom->loadXML($pIframeHTML)){
			$lXpath = new DOMXPath($lDom);
			$lXpathQuery = '/iframe';
			$lXPathResult = $lXpath->query($lXpathQuery);
			if( $lXPathResult->length ){
				$lIframe = $lXPathResult->item(0);
				$lIframeSrc = $lIframe->getAttribute('src');
				return $lIframeSrc;
			}
		}		
	}
	
	protected function StoreGBIFData($pData){
		$lCon = $this->m_con;
		$lSql = '
				SELECT * FROM spSaveTaxonGBIFBaseData(' . (int)$this->m_taxonId . ', \'' . q($pData['map_iframe_src']) . '\', \'' . q($pData['gbif_taxon_id']) . '\')
		';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return;
		}	
		$lResultFound = $pData['gbif_taxon_id'] ? 1 : 0;		
		$this->StoreSiteData(GBIF_SITE_ID, $lResultFound, GBIF_TAXON_LINK . $pData['gbif_taxon_id']);
	}
	
	protected function GetGBIFDataFromDB($pGBIFId){
		$lCon = $this->m_con;
		$lResult = array(
			'map_iframe_src' => '',
			'gbif_taxon_id' => '',
		);
		$lSql = '
			SELECT *
			FROM pjs.taxon_gbif_data a
			WHERE id = ' . (int) $pGBIFId . '
		';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return $lResult;
		}
		$lResult['map_iframe_src'] = $lCon->mRs['map_iframe_src'];
		$lResult['gbif_taxon_id'] = $lCon->mRs['gbif_taxon_id'];
		
		return $lResult;
	}
	
	function GetBHLData() {
		$lCon = $this->m_con;
		$lSql = '
			SELECT *
			FROM pjs.taxon_bhl_data a
			WHERE taxon_id = ' . (int) $this->m_taxonId . '
			AND lastmoddate > now() - \'' . (int) CACHE_TIMEOUT_LENGTH . ' seconds\'::interval
		';
		$lCon->Execute($lSql);
		if (! $lCon->mRs ['id']) {
			return $this->GenerateBHLData();
		}
		return $this->GetBHLDataFromDB($lCon->mRs['id']);
	} 
	
	protected function GenerateBHLData() {
		$lUrl = BHL_TAXON_LINK . $this->m_encodedTaxonName;
		$lResultTakenSuccessfully = false;
		$lTitlesCount = 0;
		$lTitles = array();
		$lQueryResult = executeExternalQuery($lUrl, false, '', 10);
		
		$lTaxonId = '';
		if ($lQueryResult) {
			$lDom = new DOMDocument();
			if ($lDom->loadXML($lQueryResult)) {
				$lXpath = new DOMXPath($lDom);
				$lStatusQuery = '/Response/Status';
				$lStatusResult = $lXpath->query($lStatusQuery);
				$lStatus = trim($lStatusResult->item(0)->textContent);
				if ($lStatus == 'ok') {
					$lResultTakenSuccessfully = true;
					$lTitlesQuery = '/Response/Result/Titles/Title';
					$lTitleNodes = $lXpath->query($lTitlesQuery);
					$lTitlesCount = $lTitleNodes->length;
					foreach ($lTitleNodes as $lCurrentTitle){
						$lTitles[] = $this->GenerateBHLTitle($lCurrentTitle, $lXpath);						
					}					
				}				
			}
		}
		$lResult = array (
			'result_taken_successfully' => $lResultTakenSuccessfully,
			'titles' => $lTitles 
		);
// 		var_dump($lResult);
// 		exit;
		$this->StoreBHLData($lResult);
		return $lResult;
	}
	
	protected function GenerateBHLTitle($pTitleNode){
		$lTitle = array(
			'items' => array(),
			'title_url' => '',
			'title' => '',
		);
		$lTitle['title'] = $pTitleNode->getElementsByTagName('ShortTitle')->item(0)->nodeValue;
		$lTitle['title_url'] = $pTitleNode->getElementsByTagName('TitleUrl')->item(0)->nodeValue;
		foreach($pTitleNode->getElementsByTagName('Item') as $lItem) {			
			$lTitle['items'][] = $this->GenerateBHLItem($lItem);	
		}
		return $lTitle;
	}
	
	protected function GenerateBHLItem($pItemNode){
		$lItem = array(
			'volume' => '',
			'pages_count' => 0,
			'pages' => array(),
		);
		$lItem['volume'] = $pItemNode->getElementsByTagName('Volume')->item(0)->nodeValue;		
		$lPages = $pItemNode->getElementsByTagName('Pages')->item(0)->getElementsByTagName('Page');
		$lItem['pages_count'] = $lPages->length;
		foreach($lPages as $lPage) {
			$lItem['pages'][] = $this->GenerateBHLPage($lPage);	
		}
		return $lItem;
	}
	
	protected function GenerateBHLPage($pPageNode){
		$pPage = array(
			'number' => 0,
			'url' => '',
			'thumbnail_url' => '',
			'fullsize_image_url' => '',
		);		
		$lPage['number'] = $pPageNode->getElementsByTagName('PageNumbers')->item(0)->getElementsByTagName('PageNumber')->item(0)->getElementsByTagName('Number')->item(0)->nodeValue;
		$lPage['url'] = $pPageNode->getElementsByTagName('PageUrl')->item(0)->nodeValue;
		$lPage['thumbnail_url'] = $pPageNode->getElementsByTagName('ThumbnailUrl')->item(0)->nodeValue;
		$lPage['fullsize_image_url'] = $pPageNode->getElementsByTagName('FullSizeImageUrl')->item(0)->nodeValue;
		return $lPage;
	}
	
	protected function StoreBHLData($pData){
		$lCon = $this->m_con;
		$lSql = '
				SELECT * FROM spSaveTaxonBHLBaseData(' . (int)$this->m_taxonId . ', ' . (int)$pData['result_taken_successfully'] . ')
		';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return;
		}
		$lBHLId = (int)$lCon->mRs['id'];
		foreach ($pData['titles'] as $lTitleData) {
			$lTitle = $lTitleData['title'];
			$lTitleUrl = $lTitleData['title_url'];			
			$lSql = 'SELECT * FROM spSaveTaxonBHLTitle(' . $lBHLId . ', \'' . q($lTitle) . '\', \'' . q($lTitleUrl) . '\')';
			if(!$lCon->Execute($lSql)){
				$this->SetError($lCon->GetLastError());
				return;
			}
			$lTitleId = $lCon->mRs['id']; 
			foreach ($lTitleData['items'] as $lItemData) {
				$lVolume = $lItemData['volume'];
				$lPagesCount = (int)$lItemData['pages_count'];			
				$lSql = 'SELECT * FROM spSaveTaxonBHLItem(' . $lTitleId . ', \'' . q($lVolume) . '\', ' . (int)($lPagesCount) . ')';
				if(!$lCon->Execute($lSql)){
					$this->SetError($lCon->GetLastError());
					return;
				}
				$lItemId = $lCon->mRs['id']; 
				foreach ($lItemData['pages'] as $lPageData) {
					$lUrl = $lPageData['url'];
					$lThumbnaulUrl = $lPageData['thumbnail_url'];
					$lFullsizeImageUrl = $lPageData['fullsize_image_url'];
					$lNumber = (int)$lPageData['number'];
					$lSql = 'SELECT * FROM spSaveTaxonBHLPage(' . $lItemId . ', \'' . q($lUrl) . '\', \'' . q($lThumbnaulUrl) . '\', \'' . q($lFullsizeImageUrl) . '\', ' . $lNumber . ')';
					if(!$lCon->Execute($lSql)){
						$this->SetError($lCon->GetLastError());
						return;
					}
					$lPageId = $lCon->mRs['id'];
				}
			}
		}		
	}
	
	protected function GetBHLDataFromDB($pBHLId){
		$lCon = $this->m_con;
		$lResult = array(
			'result_taken_successfully' => '',
			'titles' => array(),
		);
		$lSql = '
			SELECT *, result_taken_successfully::int as successful_taken_result
			FROM pjs.taxon_bhl_data a
			WHERE id = ' . (int) $pBHLId . '
		';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return $lResult;
		}
		$lResult['result_taken_successfully'] = $lCon->mRs['successful_taken_result'];
		$lSql = '
			SELECT *
			FROM  pjs.taxon_bhl_titles a
			WHERE taxon_bhl_data_id = ' . (int) $pBHLId . '
		';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return $lResult;
		}	
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lTitleId = $lRow['id'];
			$lTitleArray = array(
				'items' => array(),
				'title_url' => '',
				'title' => '',
			);
			$lTitleArray['title_url'] = $lRow['title_url'];
			$lTitleArray['title'] = $lRow['title'];
			
			$lResult['titles'][$lTitleId] = $lTitleArray;
			$lCon->MoveNext();
		}	
		
		$lSql = '
			SELECT i.*
			FROM  pjs.taxon_bhl_titles a
			JOIN pjs.taxon_bhl_title_items i ON i.title_id = a.id
			WHERE taxon_bhl_data_id = ' . (int) $pBHLId . '
		';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return $lResult;
		}
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lItemId = $lRow['id'];
			$lTitleId = $lRow['title_id'];
			$lItemArray = array(
				'volume' => '',
				'pages_count' => 0,
				'pages' => array(),
			);
			$lItemArray['volume'] = $lRow['volume'];
			$lItemArray['pages_count'] = (int)$lRow['pages_count'];
				
			$lResult['titles'][$lTitleId]['items'][$lItemId] = $lItemArray;
			$lCon->MoveNext();
		}
		
		$lSql = '
			SELECT p.*, a.id as title_id
			FROM  pjs.taxon_bhl_titles a
			JOIN pjs.taxon_bhl_title_items i ON i.title_id = a.id
			JOIN pjs.taxon_bhl_title_item_pages p ON p.item_id = i.id
			WHERE taxon_bhl_data_id = ' . (int) $pBHLId . '
		';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return $lResult;
		}
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lPageId = $lRow['id'];
			$lItemId = $lRow['item_id'];
			$lTitleId = $lRow['title_id'];
			$lPageArray = array(
				'number' => 0,
				'url' => '',
				'thumbnail_url' => '',
				'fullsize_image_url' => '',
			);
			$lPageArray['number'] = (int)$lRow['number'];
			$lPageArray['url'] = $lRow['url'];
			$lPageArray['thumbnail_url'] = $lRow['thumbnail_url'];
			$lPageArray['fullsize_image_url'] = $lRow['fullsize_image_url'];
		
			$lResult['titles'][$lTitleId]['items'][$lItemId]['pages'][$lPageId] = $lPageArray;
			$lCon->MoveNext();
		}
		
		return $lResult;
	}
	
	function GetWikimediaData() {
		$lCon = $this->m_con;
		$lSql = '
			SELECT *
			FROM pjs.taxon_wikimedia_categories a
			WHERE taxon_id = ' . (int) $this->m_taxonId . '
			AND lastmoddate > now() - \'' . (int) CACHE_TIMEOUT_LENGTH . ' seconds\'::interval
		';
		$lCon->Execute($lSql);
		if (! $lCon->mRs ['id']) {
			return $this->GenerateWikimediaData();
		}
		return $this->GetWikimediaDataFromDB();
	}
	
	protected function GenerateWikimediaData(){
		$lCategories = $this->GetWikiCategories();
		$lCategoryNames = array_keys($lCategories);
		$lCategoriesImages = $this->GetWikiCategoriesImages($lCategoryNames);
		
		$this->StoreWikimediaData($lCategoriesImages);
		return $lCategoriesImages;
	}
	
	protected function GetWikiCategories(){
		$lUrl = WIKIMEDIA_COMMONS_API_URL . '?action=opensearch&format=xml&search=' . $this->m_encodedTaxonName;
		$lQueryResult = executeExternalQuery($lUrl);
		$lCategoryNames = array();
		if( $lQueryResult ){
			$lDom = new DOMDocument();
			if($lDom->loadXML($lQueryResult)){
				$lXpath = new DOMXPath($lDom);
				$lXpath->registerNamespace('def', 'http://opensearch.org/searchsuggest2');
				$lXpathQuery = '/def:SearchSuggestion/def:Section/def:Item/def:Text';
				$lXPathResult = $lXpath->query($lXpathQuery);
				for( $i = 0; $i < $lXPathResult->length; ++$i){
					$lCategoryName = $lXPathResult->item($i)->textContent;
					if( $lCategoryName ){
						$lCategoryNames[$lCategoryName] = array(
							'name' => $lCategoryName,
							'images' => array()
						);
					}
				}
			}
		}
		return $lCategoryNames;
	}
	
	protected function GetWikiCategoriesImages($pCategoryNames){
		$lUrl = WIKIMEDIA_COMMONS_API_URL . '?action=query&prop=images&format=xml&titles=' . rawurlencode(implode('|', $pCategoryNames));
		$lQueryResult = executeExternalQuery($lUrl);
		//~ var_dump($lQueryResult);
		$lResult = array();
		$lCategories = array();		
		if( $lQueryResult ){
			$lDom = new DOMDocument();
			if($lDom->loadXML($lQueryResult)){
				$lXpath = new DOMXPath($lDom);
				$lXpathQuery = '/api/query/pages/page/images/im';
				$lXPathResult = $lXpath->query($lXpathQuery);
				$lResults = 0;
				foreach ($lXPathResult as $lImage){
					$lPhotoName = $lImage->getAttribute('title');
					$lPhotoName = mb_substr($lPhotoName, mb_strlen('File:'));//Mahame pyrvonachalniq "File:" ot imeto
					$lImageSrc = $this->GetWikiImageSrc($lPhotoName);
					if($lImageSrc && $lPhotoName && $lImage->parentNode && $lImage->parentNode->parentNode){
						$lCategoryName = $lImage->parentNode->parentNode->getAttribute('title');
						if($lCategoryName){
							if(!array_key_exists($lCategoryName, $lCategories)){
								$lCategories[$lCategoryName] = array(
									'name' => $lCategoryName,
									'images' => array()
								);
							}
							
							$lCategories[$lCategoryName]['images'][$lPhotoName] = array(
								'name' => $lPhotoName,								
								'src' => $lImageSrc,
							);
						}
					}					
				}		
			}
		}		
		return $lCategories;
	}
	
	protected function GetWikiImageSrc($pImgName){
		$lUrl = WIKIMEDIA_COMMONS_API_URL . '?action=query&format=xml&list=allimages&ailimit=1&aifrom=' . rawurlencode($pImgName);
		$lQueryResult = executeExternalQuery($lUrl);
		//~ var_dump($pImageName, $lQueryResult);
		//~ exit;
		if( $lQueryResult ){
			$lDom = new DOMDocument();
			if($lDom->loadXML($lQueryResult)){
				$lXpath = new DOMXPath($lDom);
				$lXpathQuery = '/api/query/allimages/img/@url';
				$lXPathResult = $lXpath->query($lXpathQuery);
				if( $lXPathResult->length )
					return $lXPathResult->item(0)->textContent;
			}
		}
	}
	
	protected function StoreWikimediaData($pData){
		$lCon = $this->m_con;
		$lSql = '
				SELECT * FROM spClearTaxonWikimediaCategories(' . (int)$this->m_taxonId . ')
			';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return;
		}
		$lResultFound = 0;
		foreach ($pData as $lCategoryName => $lCategoryData){
			$lSql = '
				SELECT * FROM spSaveTaxonWikimediaCategory(' . (int)$this->m_taxonId . ', \'' . q($lCategoryName) . '\')
			';
			if(!$lCon->Execute($lSql)){
				$this->SetError($lCon->GetLastError());
				return;
			}
			$lCategoryId = (int)$lCon->mRs['id'];
			foreach ($lCategoryData['images'] as $lPhotoData) {
				$lSql = 'SELECT * FROM spSaveTaxonWikimediaPhoto(' . (int)$lCategoryId . ', \'' . q($lPhotoData['src']) . '\', \'' . q($lPhotoData['name']) . '\');';
				$lResultFound = 1;
				if(!$lCon->Execute($lSql)){
					$this->SetError($lCon->GetLastError());
					return;
				}
			}
		}		
		$this->StoreSiteData(WIKIMEDIA_SITE_ID, $lResultFound);
	}
	
	protected function GetWikimediaDataFromDB(){
		$lCon = $this->m_con;
		$lResult = array();		
		$lSql = '
			SELECT c.category_name, i.*
			FROM pjs.taxon_wikimedia_category_images i
			JOIN pjs.taxon_wikimedia_categories c ON c.id = i.category_id
			WHERE c.taxon_id = ' . (int)$this->m_taxonId . '
			ORDER BY c.id ASC
		';
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lCategoryName = $lRow['category_name'];
			if(!array_key_exists($lCategoryName, $lResult)){
				$lResult[$lCategoryName] = array(
					'name' => $lCategoryName,
					'images' => array()
				);
			}
			$lResult[$lCategoryName]['images'][$lRow['image_name']] = array(
				'name' => $lRow['image_name'],								
				'src' => $lRow['url'],
			);
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function GetLiasData() {
		$lCon = $this->m_con;
		$lSql = '
			SELECT *
			FROM pjs.taxon_lias_data a
			WHERE taxon_id = ' . (int) $this->m_taxonId . '
			AND lastmoddate > now() - \'' . (int) CACHE_TIMEOUT_LENGTH . ' seconds\'::interval
		';
		$lCon->Execute($lSql);
		if (! $lCon->mRs ['id']) {
			return $this->GenerateLiasData();
		}
		return $this->GetLiasDataFromDB($lCon->mRs ['id']);
	}
	
	protected function GenerateLiasData(){
		$lUrl = LIAS_WEBSERVICE_URL . $this->m_encodedTaxonName;
		//~ echo $lUrl;
// 		var_dump($lUrl);
		$lQueryResult = executeExternalQuery($lUrl, false, '', 5);
		$lResult = array();
		$lDetails = array();
// 		var_dump($lQueryResult);
		if($lQueryResult) {
			$lDom = new DOMDocument();
			if($lDom->loadXML($lQueryResult)){
				$lXpath = new DOMXPath($lDom);
				
				$lXpathQuery = '/ns:SearchTaxonNamesResponse/ns:return[ax21:nameID]';
				$lXPathResult = $lXpath->query($lXpathQuery);
				//~ var_dump($lXPathResult->length);
				//~ var_dump($lXPathResult);
				for( $i = 0; $i < $lXPathResult->length; ++$i){
					$lCurrentTaxonNode = $lXPathResult->item($i);
					$lNameXPathQuery = './ax21:acceptedName';
					$lIdXPathQuery = './ax21:nameID';
					
					$lName = '';
					$lNameXPathResult = $lXpath->query($lNameXPathQuery, $lCurrentTaxonNode);
					if( $lNameXPathResult->length ){
						$lName = $lNameXPathResult->item(0)->textContent;
					}
					$lIdXPathResult = $lXpath->query($lIdXPathQuery, $lCurrentTaxonNode);//Tuk vinagi ima rezultata poradi nachalniq XPath expr
					$lId = $lIdXPathResult->item(0)->textContent;
					
					$lDetails[$lId] = array(
						'name' => $lName
					);				
				}				
			}
		}
		$lResult['results'] = count($lDetails);
		$lResult['details'] = $lDetails;
		$this->StoreLiasData($lResult);
		return $lResult;
	}
	
	protected function StoreLiasData($pData){
		$lCon = $this->m_con;
		$lSql = '
				SELECT * FROM spSaveTaxonLiasBaseData(' . (int)$this->m_taxonId . ', ' . (int)$pData['results'] . ')
		';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return;
		}
		$lLiasId = (int)$lCon->mRs['id'];
		foreach ($pData['details'] as $lDetailId => $lDetailData){
			$lSql = '
				SELECT * FROM spSaveTaxonLiasDetail(' . (int)$lLiasId . ', \'' . q($lDetailId) . '\', \'' . q($lDetailData['name']) . '\')
			';
			if(!$lCon->Execute($lSql)){
				$this->SetError($lCon->GetLastError());
				return;
			}
		}
		$lResultFound = count($pData['details']) ? 1 : 0;
		$this->StoreSiteData(LIAS_SITE_ID, $lResultFound);
	}
	
	protected function GetLiasDataFromDB($pLiasDataId){
		$lCon = $this->m_con;
		$lResult = array();
		$lSql = '
			SELECT d.results, de.*
			FROM pjs.taxon_lias_data d
			JOIN pjs.taxon_lias_data_details de ON de.data_id = d.id
			WHERE d.id = ' . (int)$pLiasDataId . '
			ORDER BY de.id ASC
		';
		$lCon->Execute($lSql);		
		$lDetails = array();
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lResult['results'] = (int)$lRow['results'];
			$lDetailId = $lRow['detail_id'];
			$lDetailArr = array(
				'name' => $lRow['detail_name']
			);
			$lDetails[$lDetailId] = $lDetailArr;
			$lCon->MoveNext();
		}
		$lResult['details'] = $lDetails;
		return $lResult;
	}
	
	function GenerateUbioSitesData(){
		$lUrl = UBIO_TAXONFINDER_URL . $this->m_encodedTaxonName . '&includeLinks=1';
		$lQueryResult = executeExternalQuery($lUrl);
		//~ var_dump($lQueryResult);
		$lResult = array();
		if( $lQueryResult ){
			$lDom = new DOMDocument();
			if($lDom->loadXML($lQueryResult)){
				$lXpath = new DOMXPath($lDom);
				$lXpathQuery = '/results/allNames/entity/weblinks/website';
				$this->m_link_nodes = $lXpath->query($lXpathQuery);
				for( $i = 0; $i < $this->m_link_nodes->length; ++$i){
					$lCurrentNode = $this->m_link_nodes->item($i);
					$lTitleXPath = './title';
					$lLinkXPath = './links/link';
					$lTitleResult = $lXpath->query($lTitleXPath, $lCurrentNode);
					if( $lTitleResult ){
						$lTitle = trim($lTitleResult->item(0)->textContent);
						$lLinkResult = $lXpath->query($lLinkXPath, $lCurrentNode);
						if( $lLinkResult->length ){
							$lLink = trim($lLinkResult->item(0)->textContent);
							$lResult[$lTitle] = $lLink;							
						}
					}
				}
			}
		}
		$this->StoreUbioSitesData($lResult);
		return $lResult;
	}
	
	function StoreUbioSitesData($pData){
		$lCon = $this->m_con;
		$lUbioSites = $this->GetUbioSites();
		foreach($pData as $lUbioSiteName => $lLink){
			if(!array_key_exists($lUbioSiteName, $lUbioSites)){//Not one of the sites we track
				continue;
			}
			$lSql = 'SELECT * FROM spSaveTaxonUbioSiteResult(' . (int)$this->m_taxonId . ', ' . $lUbioSites[$lUbioSiteName]['id'] . ', \'' . q($lLink) . '\');';
			if(!$lCon->Execute($lSql)){
				$this->SetError($lCon->GetLastError());
				return;
			}
		}
	}
	
	function GetUbioSites(){
		$lCon = $this->m_con;
		$lSql = '
				SELECT * 
				FROM pjs.taxon_sites
				WHERE is_ubio_site = true
		';
		$lCon->Execute($lSql);
		$lResult = array();
		while(!$lCon->Eof()){
			$lRow = $lCon->mRs;
			$lResult[$lRow['ubio_site_name']] = $lRow;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	function GetSiteData($pSiteId) {
		$lCon = $this->m_con;
		$lSql = '
			SELECT *
			FROM pjs.taxon_sites_results a
			WHERE taxon_id = ' . (int) $this->m_taxonId . ' AND site_id = ' . (int)$pSiteId . '
			AND lastmoddate > now() - \'' . (int) CACHE_TIMEOUT_LENGTH . ' seconds\'::interval
		';
		$lCon->Execute($lSql);
		if (! $lCon->mRs ['id']) {
			return $this->GenerateSiteData($pSiteId);
		}
		return $this->GetSiteDataFromDb($pSiteId);
	}
	
	protected function GenerateSiteData($pSiteId){
		$pSiteIsSpecial = true;
		switch($pSiteId){
			default:
				$pSiteIsSpecial = false;
				break;
			case (int)GBIF_SITE_ID:
				$this->GetGBIFData();
				break;
			case (int)NCBI_SITE_ID:
				$this->GetNCBIData();
				break;
			case (int)LIAS_SITE_ID:
				$this->GetLiasData();
				break;
			case (int)WIKIMEDIA_SITE_ID:
				$this->GetWikimediaData();
				break;
		}
		if($pSiteIsSpecial){
			//For these sites we check for results in their apis - so the data about the site should be 
			//written by the queries to the apis(i.e. the calls to GetGBIFData/GetNCBIData ...)
			return $this->GetSiteDataFromDb($pSiteId);
		}
		$lSiteMetaData = $this->GetSiteMetadata($pSiteId);
		if($lSiteMetaData['is_ubio_site']){
			$lUbioData = $this->GenerateUbioSitesData();
			if(array_key_exists($lSiteMetaData['ubio_site_name'], $lUbioData)){
				//The data about the site should have been written by GenerateUbioSitesData
				return $this->GetSiteDataFromDb($pSiteId);
			}
		}
		
		$lSiteUrl = $lSiteMetaData['taxon_link'];
		$lSiteUrl = $this->ReplaceText($lSiteUrl);
		
		$lPostForm = $lSiteMetaData['use_post_action'];
		$lPostFields = $lSiteMetaData['fields_to_post'];
		
		$lPostfieldsParam = false;
		if( $lPostForm ){
			$lPostfieldsParam = $this->parseStringPostfields($lPostFields);
		}
		$lSiteResponse = executeExternalQuery($lSiteUrl, $lPostfieldsParam);		
		$lResultFound = false;		
			
		if( $lSiteResponse ){
			//~ var_dump($lSiteResponse);
			if( is_array( $lSiteMetaData['match_expressions'] )){//Masiv s reg expove, koito ako matchnat vsichki - nqma rezultat
				foreach( $lSiteMetaData['match_expressions']  as $lSingleRegExpPattern ){	
					$lSingleRegExpPattern = $this->ReplaceText($lSingleRegExpPattern);				
					$lSingleRegExpPattern = '/' . $lSingleRegExpPattern . '/im';
					if( !preg_match( $lSingleRegExpPattern, $lSiteResponse)){//Ima match
						$lResultFound = true;		
						break;
					}
				}
			}
		}
		$this->StoreSiteData($pSiteId, $lResultFound);
		return $this->GetSiteDataFromDb($pSiteId);		
	}
	
	protected function StoreSiteData($pSiteId, $pResultFound, $pSpecificLink = ''){
		$lCon = $this->m_con;
		$lSql = 'SELECT * FROM spSaveTaxonSiteResult(' . (int)$this->m_taxonId . ', ' . (int)$pSiteId . ', ' . (int)$pResultFound . ', \'' . q($pSpecificLink) . '\');';
		if(!$lCon->Execute($lSql)){
			$this->SetError($lCon->GetLastError());
			return;
		}
	}
	
	protected function GetSiteDataFromDb($pSiteId){
		$lSiteMetadata = $this->GetSiteMetadata($pSiteId);
// 		var_dump($pSiteId);
		$lCon = $this->m_con;
		$lSql = '
				SELECT has_results::int as has_results, specific_link_url
				FROM pjs.taxon_sites_results r
				WHERE taxon_id = ' . (int)$this->m_taxonId . ' AND site_id = ' . (int)$pSiteId . '
		';
		$lCon->Execute($lSql);
		$lHasResults = (int)$lCon->mRs['has_results'];
		$lSpecificLinkUrl = $lCon->mRs['specific_link_url'];
		$lTaxonLink = $lHasResults ? ( $lSpecificLinkUrl ? $lSpecificLinkUrl : $lSiteMetadata['taxon_link']) : $lSiteMetadata['taxon_link_no_results'];
		$lFieldsToPost = $lHasResults ? $lSiteMetadata['fields_to_post'] : $lSiteMetadata['fields_to_post_no_results'];
		$lTaxonLink = $this->ReplaceText($lTaxonLink);
		$lFieldsToPost = $this->ReplaceText($lFieldsToPost);
		$lResult = array(
			'has_results' => $lHasResults,
			'picsrc' => $lHasResults ? $lSiteMetadata['picsrc'] : $lSiteMetadata['picsrc_no_results'],
			'display_title' => $lSiteMetadata['display_title'],
			'taxon_link' => $lTaxonLink,
			'show_if_not_found' => $lSiteMetadata['show_if_not_found'],
			'use_post_action' => $lHasResults ? $lSiteMetadata['use_post_action'] : $lSiteMetadata['use_post_action_no_results'],
			'fields_to_post' => $lFieldsToPost,
		);
		return $lResult;
	}
	
	protected function GetSiteMetadata($pSiteId){
		$lCon = $this->m_con;
		$lSql = '
			SELECT  id, name, picsrc, picsrc_no_results, display_title,
				is_ubio_site::int as is_ubio_site, ubio_site_name,
				taxon_link, taxon_link_no_results, 
				show_if_not_found::int as show_if_not_found, use_post_action::int as use_post_action, use_post_action_no_results::int as use_post_action_no_results,
				fields_to_post, fields_to_post_no_results
			FROM pjs.taxon_sites
			WHERE id = ' . (int)$pSiteId . '
		';
		$lCon->Execute($lSql);
		$lResult = $lCon->mRs;
		$lSql = '
			SELECT *
			FROM pjs.taxon_sites_match_expressions
			WHERE site_id = ' . (int)$pSiteId . '
			ORDER BY ord ASC		
		';
		$lResult['match_expressions'] = array();
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lResult['match_expressions'][] = $lCon->mRs['expression'];
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	protected function GetAllSiteIds(){
		$lCon = $this->m_con;
		$lSql = '
			SELECT *
			FROM pjs.taxon_sites a
		';
		$lCon->Execute($lSql);
		$lResult = array();
		while(!$lCon->Eof()){
			$lResult[] = $lCon->mRs['id'];
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	protected function parseStringPostFields($pString){
		$lFakeUrl = 'http://www.etaligent.net?' . $pString;
		$lParsedUrl = parse_url($lFakeUrl);
		parse_str($lParsedUrl['query'], $lResult);
		return $lResult;		
	}
	
	/**
	 * Replace the taxon name / encoded taxon name in the specified text
	 * @param unknown $pText
	 */
	protected function ReplaceText($pText){
		$pText = str_replace('{taxon_name}', $this->m_taxonName, $pText);
		$pText = str_replace('{encoded_taxon_name}', $this->m_encodedTaxonName, $pText);
		$pText = str_replace('{lias_iframe_url}', LIAS_IFRAME_URL, $pText);
		
		return $pText;
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
		$this->GenerateAllData();		
		$this->m_dontGetData = true;
	}

	protected function GenerateAllData() {
		$this->GetNCBIData();
		$this->GetGBIFData();
		$this->GetBHLData();
		$this->GetWikimediaData();
		$this->GetLiasData();
		$lAllSiteIds = $this->GetAllSiteIds();
		foreach ($lAllSiteIds as $lSiteId){
			$this->GetSiteData($lSiteId);
		}
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