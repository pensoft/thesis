<?php
class ctaxon_ncbiinfo extends cbase_cachedata {
	var $m_recordCount;
	var $m_currentRecord;
	var $m_pageSize;
	var $m_defTempls;
	var $m_page;
	var $m_RecordNumber;
	var $con;
	var $m_dontgetdata;
	var $m_taxonid;	

	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		$this->m_pubdata['results_count'] = (int) $this->m_pubdata['results_count'] ? (int) $this->m_pubdata['results_count'] : 10;
		$this->m_pubdata['start_num'] = (int) $this->m_pubdata['start_num'] ? (int) $this->m_pubdata['start_num'] : 1;
		$this->m_taxonid = 0;
		$this->m_recordCount = 0;
		$this->LoadDefTempls();
	}
	
	function DontGetData($p) {
		$this->m_dontgetdata = $p;
	}	
	
	function LoadDefTempls() {
		if (!defined('D_EMPTY')) {
			define('D_EMPTY', 'global.empty');
		}
		
		$this->m_defTempls = array(G_HEADER => D_EMPTY, G_FOOTER => D_EMPTY, G_STARTRS => D_EMPTY, G_ENDRS => D_EMPTY, G_NODATA => D_EMPTY, G_ROWTEMPL => D_EMPTY);
	}	
	
	function CheckVals() {
		if($this->m_state == 0) {			
			$this->m_state++;
		} else {
			// NOTICE
		}
	}
	
	function GetDataC(){
		parent::GetDataC();
		if( $this->m_got_data_from_cache ){//Slagame gi poneje navyn se polzvat v nqkoi proverki			
			$this->m_recordCount = $this->m_pubdata['m_recordCount'];
			$this->m_taxonid = $this->m_pubdata['m_taxonid'];			
		}
	}
	
	function GetData() {
		$this->CheckVals();
		if ($this->m_state >= 1) {			
			$lUrl = EUTILS_ESEARCH_SRV . 'term=' . str_replace(' ', '+', $this->m_pubdata['taxon_name']) . '&retmax=1&retmode=xml&tool=' . EUTILS_TOOL_NAME  . '&db=' . EUTILS_TAXONOMY_DB;	
			$lQueryResult = executeExternalQuery($lUrl);				
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
			if( $lQueryResult ){				
				$lDom = new DOMDocument();	
				if($lDom->loadXML($lQueryResult)){					
					$lXpath = new DOMXPath($lDom);					
					$lXpathQuery = '/eSearchResult/IdList/Id';	 							
					$lXPathResult = $lXpath->query($lXpathQuery);
					if( $lXPathResult->length ){
						$this->m_recordCount;
						$this->m_taxonid = $lXPathResult->item(0)->textContent;						
					}					
					$this->m_recordCount = $lXPathResult->length;	
					$this->m_pubdata['m_recordCount'] = $this->m_recordCount;
					$this->m_pubdata['m_taxonid'] = $this->m_taxonid;
				}
			}
			$this->FetchTaxonDetails();
			$this->m_state++;			
		}
		$this->m_dontgetdata = true;
	}
	
	function FetchNodeDetails($pNode) {
		if( $pNode ){			
			foreach($pNode->childNodes as $lChild) {
				if( $lChild->nodeType != 1 )//Obrabotvame samo elementite
					continue;
				$lKey = strtolower($lChild->nodeName);				
				$this->m_pubdata[$lKey] = $this->m_currentRecord[$lKey] = $lChild->textContent;
			}			
		}		
	}

	function FetchTaxonDetails() {
		if( $this->m_taxonid ){
			if( !(int) $this->m_pubdata['call_sub_ajax_queries'] ){
				$lPubMedLinks = new ctaxon_extlinks(array(
					'ctype' => 'ctaxon_extlinks',
					'templs' => $this->m_pubdata['link_templs'],
					'taxon_name' => $this->m_pubdata['taxon_name'],
					'database' => $this->m_pubdata['link_database'],
					'database_title' => $this->m_pubdata['link_database_title'],
					'results_count' => $this->m_pubdata['link_result_count'],
					'cache' => 'taxon_ncbi_pubmed_links',
					'cachetimeout' => CACHE_TIMEOUT_LENGTH,
				));
				$lPubMedLinks->SetParentLoggerObjectId($this->m_logger_object_id);
				$lEntrezRecords = new ctaxon_entrezrecords(array(
					'ctype' => 'ctaxon_entrezrecords',
					'templs' => $this->m_pubdata['entrezrecords_templs'],
					'taxon_id' => $this->m_taxonid,
					'taxon_name' => $this->m_pubdata['taxon_name'],
					'allowed_databases' => $this->m_pubdata['entrez_records_allowed_databases'],//Bazite za koito she izkarvame broikata
					'cache' => 'taxon_ncbi_entrezrecords',
					'cachetimeout' => CACHE_TIMEOUT_LENGTH,
				));
				$lEntrezRecords->SetParentLoggerObjectId($this->m_logger_object_id);
			}else{
				$lPubMedLinks = new csimple(array(
					'ctype' => 'csimple',
					'templs' => $this->m_pubdata['link_ajax_templs'],
					'taxon_name' => $this->m_pubdata['taxon_name'],
					'ajax_link' => AJAX_EXT_LINKS_SRV . '?taxon_name=' . urlencode($this->m_pubdata['taxon_name']) 
						. '&database=' . urlencode($this->m_pubdata['link_database'])
						. '&database_title=' . urlencode($this->m_pubdata['database_title'])
						. '&results_count=' . (int) $this->m_pubdata['link_result_count']
					,
					'cache' => 'taxon_ncbi_pubmed_links_ajax_csimple',
					'cachetimeout' => CACHE_TIMEOUT_LENGTH,
				));
				
				$lEntrezRecordsAjaxLink = AJAX_ENTREZ_RECORDS_SRV . '?taxon_id=' . urlencode( $this->m_taxonid)
					. '&taxon_name=' . urlencode($this->m_pubdata['taxon_name']);
				foreach($this->m_pubdata['entrez_records_allowed_databases'] as $lDBName => $lDBLabel ){
					$lEntrezRecordsAjaxLink .= '&allowed_databases[' . urlencode($lDBName) . ']=' . urlencode($lDBLabel);
				}	
				$lEntrezRecords = new csimple(array(
					'ctype' => 'csimple',
					'templs' => $this->m_pubdata['entrezrecords_ajax_templs'],
					'ajax_link' => $lEntrezRecordsAjaxLink,
					'cache' => 'taxon_ncbi_entrezrecords_ajax_csimple',
					'cachetimeout' => CACHE_TIMEOUT_LENGTH,
				));
			}
			
			
			$this->m_pubdata['pubmed_links'] = $lPubMedLinks;
			$this->m_pubdata['entrez_records'] = $lEntrezRecords;
			$lUrl = EUTILS_EFETCH_SRV . 'id=' . $this->m_taxonid . '&retmode=xml&tool=' . EUTILS_TOOL_NAME  . '&db=' . EUTILS_TAXONOMY_DB;	
			$lQueryResult = executeExternalQuery($lUrl);				
			if( $lQueryResult ){						
				$lDom = new DOMDocument();					
				if($lDom->loadXML($lQueryResult)){					
					$lXPath = new DOMXPath($lDom);					
					$lXpathQuery = '/TaxaSet/Taxon';	 							
					$lXPathResult = $lXPath->query($lXpathQuery);
					if( $lXPathResult->length ){						
						$this->FetchNodeDetails($lXPathResult->item(0));
					}
					/**
						Lineage обектът не го правиме на ajax заявка, понеже той е бърз и само обработва xml възли, а не се връзва към чужди сайтове
					*/
					$this->m_pubdata['lineage_object'] = new ctaxon_ncbilineage(array(
						'templs' => $this->m_pubdata['lineage_templs'],						
						'xml' => $lQueryResult,
						'taxon_name' => $this->m_pubdata['taxon_name'],
						'cache' => 'taxon_ncbi_lineage',
						'cachetimeout' => CACHE_TIMEOUT_LENGTH,
					));
					
				}
			}
		}			
	}
	
	function GetTaxonDetails() {
		if( !$this->m_taxonid )		
			return;
		if ($this->m_pubdata['templadd'])
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL , $this->m_currentRecord[$this->m_pubdata['templadd']]));
		else 
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
		return $lRet;
	}
	
	function Display() {
		
		if (!$this->m_dontgetdata)
			$this->GetDataC();
		
		if ($this->m_state < 2) {
			return;
		}			
				
		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if ( !$this->m_taxonid ) {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
		} else {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $this->GetTaxonDetails();			
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
				
		return $lRet;
	}
	
}
?>