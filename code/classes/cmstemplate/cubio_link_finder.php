<?php
class cubio_link_finder extends cbase_cachedata {
	var $m_currentRecord;
	var $m_pageSize;
	var $m_defTempls;
	var $m_page;
	var $m_RecordNumber;
	var $con;
	var $m_dontgetdata;
	var $m_link_nodes;
	var $m_link_array;

	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		$this->LoadDefTempls();
		$this->m_link_array = array();
		$this->m_recordCount = 0;
	}
	
	function GetDataC() {
		$enablecache = $this->getCacheFn();
		
		$this->RegisterLoggerObject();
		$this->m_got_data_from_cache = false;		
		
		if ($enablecache && $this->getDataCacheExists() && $this->getDataCacheTimeout()) {
			$lCacheData = unserialize($this->getDataCacheContents());
			$this->m_pubdata = $lCacheData['pubdata'];
			$this->m_link_nodes = $lCacheData['link_nodes'];
			$this->m_link_array = $lCacheData['link_array'];
			$this->m_recordCount = $lCacheData['recordCount'];
			
			$this->m_got_data_from_cache = true;
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_GOT_DATA_FROM_CACHE_EVENT, $this->m_got_data_from_cache);
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_PARSING_DATA_EVENT);
			$this->m_state = 2;
			return;
		}
		
		$this->GetData();
		
		$this->RegisterLoggerObjectEvent(PROFILE_LOG_GOT_DATA_FROM_CACHE_EVENT, $this->m_got_data_from_cache);
		$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_PARSING_DATA_EVENT);
		
		if ($enablecache) {
			$lCacheData = array(
				'pubdata' => $this->m_pubdata,
				'link_nodes' => $this->m_link_nodes,
				'link_array' => $this->m_link_array,
				'recordCount' => $this->m_recordCount,
			);
			$this->saveDataCacheContents(serialize($lCacheData));
		}
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
	
	function GetData() {
		$this->CheckVals();
		if ($this->m_state >= 1) {	
			
			$lUrl = UBIO_TAXONFINDER_URL . urlencode($this->m_pubdata['taxon_name']) . '&includeLinks=1';
			$lQueryResult = executeExternalQuery($lUrl);
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
			$lMapFound = false;
			//~ var_dump($lQueryResult);
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
								$this->m_link_array[$lTitle] = $lLink;
								$this->m_recordCount++;
							}	
						}
					}
				}
			}			
			$this->m_state++;			
		}
		$this->m_dontgetdata = true;
	}
	
	function GetSourceLink($pSourceName){	
		return $this->m_link_array[$pSourceName];
	}	
	
	function GetRows() {		
		foreach( $this->m_link_array as $lTitle => $lLink){
			$this->m_pubdata['rownum']++;
			$this->m_pubdata['title'] = $lTitle;
			$this->m_pubdata['link'] = $lLink;
			
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