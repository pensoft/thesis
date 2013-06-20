<?php
class ctaxon_ncbilineage extends cbase_cachedata {
	var $m_currentRecord;
	var $m_pageSize;
	var $m_defTempls;
	var $m_page;
	var $m_RecordNumber;
	var $con;
	var $m_dontgetdata;
	var $m_xml;
	var $m_lineage_dom;
	var $m_lineage_nodes;
	var $m_lineage_xpath;
	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		$this->LoadDefTempls();
		$this->m_lineage_dom = new DOMDocument();
		$this->m_xml = $this->m_pubdata['xml'];
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
			if($this->m_lineage_dom->loadXML($this->m_xml)){
				$this->m_lineage_xpath = new DOMXPath($this->m_lineage_dom);					
				$this->m_state++;
			}
			
		} else {
			// NOTICE
		}
	}
	
	function GetDataC(){
		parent::GetDataC();
		if( $this->m_got_data_from_cache ){//Slagame gi poneje se polzvat pri display-a			
			$this->m_lineage_nodes = $this->m_pubdata['m_lineage_nodes'];
		}
	}
	
	function GetData() {
		$this->CheckVals();
		if ($this->m_state >= 1) {				
			$lLineageQuery = '/TaxaSet/Taxon/LineageEx/Taxon';
			$this->m_lineage_nodes = $this->m_lineage_xpath->query($lLineageQuery);
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
			$this->m_recordCount = ($this->m_lineage_nodes->length);	
			$this->m_pubdata['m_lineage_nodes'] = $this->m_lineage_nodes;
			$this->m_state++;
		}
		$this->m_dontgetdata = true;
	}
	
	function FetchLineageTaxonProperty($pLineageNode, $pXPathQuery, $pPropertyName){
		$lResult = $this->m_lineage_xpath->query($pXPathQuery, $pLineageNode);
		if($lResult->length){			
			$this->FetchRowPubdataProperty($pPropertyName, $lResult->item(0)->textContent);
		}
	}
	
	function FetchRowPubdataProperty($pPropertyName, $pValue){
		$this->m_pubdata[$pPropertyName] = $this->m_currentRecord[$pPropertyName] = $pValue;
	}

	function FetchLineageTaxonDetails($pLineageNode) {		
		$lPropertyQueries = array(
			'taxon_id' => './TaxId',
			'scientific_name' => './ScientificName',
			'rank' => './Rank',
		);
		foreach($lPropertyQueries as $lPropertyName => $lPropertyQuery){
			$this->FetchLineageTaxonProperty($pLineageNode, $lPropertyQuery, $lPropertyName);				
		}
		$this->FetchRowPubdataProperty('taxon_lineage_href', NCBI_TAXONOMY_LINEAGE_URL . '&id=' . $this->m_pubdata['taxon_id']);
	}
	
	function GetRows() {	
		$this->m_pubdata['records'] = (int) $this->m_recordCount;
		for( $i = 0; $i < $this->m_lineage_nodes->length; ++$i){
			$this->m_pubdata['rownum']++;
			$this->FetchLineageTaxonDetails($this->m_lineage_nodes->item($i));
			if ($this->m_pubdata['templadd'])
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL , $this->m_currentRecord[$this->m_pubdata['templadd']]));
			else 
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
		}		
		return $lRet;
	}
	
	function Display() {
		
		if (!$this->m_dontgetdata)
			$this->GetDataC();
		
		if ($this->m_state < 2) {
			return;
		}			
				
		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if ($this->m_recordCount == 0) {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
		} else {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $this->GetRows();			
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
				
		return $lRet;
	}
	
}
?>