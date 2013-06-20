<?php
class ctaxon_lias extends cbase_cachedata {
	var $m_currentRecord;
	var $m_pageSize;
	var $m_defTempls;
	var $m_page;
	var $m_RecordNumber;
	var $con;
	var $m_dontgetdata;
	var $m_bulk_xml;
	var $m_taxon_ids;

	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		//~ $this->m_pubdata['results_count'] = (int) $this->m_pubdata['results_count'] ? (int) $this->m_pubdata['results_count'] : 10;
		//~ $this->m_pubdata['start_num'] = (int) $this->m_pubdata['start_num'] ? (int) $this->m_pubdata['start_num'] : 1;
		//~ $this->m_taxonid = 0;
		$this->m_xml_result = 0;
		$this->m_bulk_xml = '';
		$this->m_pubdata['m_taxons_data'] = array();
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
		if( $this->m_got_data_from_cache ){//Slagame gi poneje se polzvat pri display-a			
			$this->m_bulk_xml = $this->m_pubdata['m_images'];	
			$this->m_taxons_data = $this->m_pubdata['m_taxons_data'];	
		}
	}
	
	function GetData() {
		$this->CheckVals();
		if ($this->m_state >= 1) {
			$lUrl = LIAS_WEBSERVICE_URL . rawurlencode( $this->m_pubdata['taxon_name']);
			//~ echo $lUrl;
			$lQueryResult = executeExternalQuery($lUrl, false, '', 15);
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
			//~ var_dump($lQueryResult);
			$this->m_bulk_xml = $lQueryResult;
			if($lQueryResult) {
				$lDom = new DOMDocument();
				if($lDom->loadXML($this->m_bulk_xml)){
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
						
						$this->m_taxons_data[$lId] = $lName;
						
					}
					$this->m_recordCount = $lXPathResult->length;
				}
			}
			$this->m_state++;
			$this->m_pubdata['m_bulk_xml'] = $this->m_bulk_xml;
			$this->m_pubdata['m_taxons_data'] = $this->m_taxons_data;
		}
		$this->m_dontgetdata = true;
	}
	
	function GetRows() {		
		foreach( $this->m_taxons_data as $lId => $lName){
			$this->m_pubdata['rownum']++;			
			$this->m_pubdata['id'] = $lId;
			$this->m_pubdata['name'] = $lName;
			
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