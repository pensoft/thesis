<?php
class ctaxon_entrezrecords extends cbase_cachedata {
	var $m_currentRecord;
	var $m_pageSize;
	var $m_defTempls;
	var $m_page;
	var $m_RecordNumber;
	var $con;
	var $m_dontgetdata;	
	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
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
	
	function GetData() {
		$this->CheckVals();
		if ($this->m_state >= 1) {	
			if($this->m_pubdata['taxon_id']){
				$lAllowedDbs = $this->m_pubdata['allowed_databases'];
				if(is_array( $lAllowedDbs) && count($lAllowedDbs)){
					$lAllowedDbNames = array_keys($lAllowedDbs);
					if(is_array( $lAllowedDbNames) && count($lAllowedDbNames)){
						$lUrl = EUTILS_EGQUERY_SRV . '?term=txid' . $this->m_pubdata['taxon_id'] . '[Organism:exp]';
						//~ var_dump($lUrl);
						$lQueryResult = executeExternalQuery($lUrl);	
						
						$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
						//~ var_dump($lQueryResult);
						if( $lQueryResult ){						
							$lDom = new DOMDocument();	
							if($lDom->loadXML($lQueryResult)){					
								$lXpath = new DOMXPath($lDom);						
								$lXpathQuery = '/Result/eGQueryResult/ResultItem';	 							
								$lXPathResult = $lXpath->query($lXpathQuery);									
								
								$lResultNodes = array();
								if( $lXPathResult->length ){
									for( $i = 0; $i< $lXPathResult->length; ++$i){
										$lCurrentNode = $lXPathResult->item($i);
										$lDbNameQuery = './DbName';	 							
										$lTempResult = $lXpath->query($lDbNameQuery, $lCurrentNode);
										if($lTempResult->length){
											$lDbName = $lTempResult->item(0)->textContent;
											if( in_array($lDbName, $lAllowedDbNames)) {
												$lResultNodes[] = $lCurrentNode;
											}
										}
									}
								
								
								}
								/*
									Трябва да ги пазим обработени като масив,
									а не като xml възли, понеже xml възлите като се десериализират се чупят
								*/
								$this->m_pubdata['resultXmlNodesArr'] = array();
								foreach($lResultNodes as $lCurrentNode){
									$this->m_pubdata['resultXmlNodesArr'][] = $this->parseCurrentNode($lCurrentNode);							
								}
								$this->m_recordCount = count($lResultNodes);
								
							}
						}
					}
				}
			}
			$this->m_state++;			
		}
		$this->m_dontgetdata = true;
	}
	
	function parseCurrentNode($pNode){
		$lResult = array();
		if( $pNode ){				
			foreach($pNode->childNodes as $lChild) {
				if( $lChild->nodeType != 1 )//Obrabotvame samo elementite
					continue;
				$lKey = strtolower($lChild->nodeName);
				$lResult[$lKey] = $lChild->textContent;
			}
		}
		return $lResult;
	}

	function FetchNodeDetails($pNodeArr) {
		$lAllowedDbs = $this->m_pubdata['allowed_databases'];
		if( is_array($pNodeArr) && count($pNodeArr) ){				
			foreach($pNodeArr as $lKey => $lValue) {
				$this->m_pubdata[$lKey] = $this->m_currentRecord[$lKey] = $lValue;
			}
			$lDbName = $this->m_pubdata['dbname'];
			$lDbSpecificName = $lAllowedDbs[$lDbName];
			if( $lDbSpecificName != '' ){
				$this->m_pubdata['menuname'] = $this->m_currentRecord['menuname'] = $lDbSpecificName;	
			}	
		}		
	}
	
	function GetRows() {
		$lNodesArr = $this->m_pubdata['resultXmlNodesArr'];
		if( !is_array($lNodesArr) || !count($lNodesArr) )
			return;				
		foreach( $lNodesArr as $lCurrentNodeArr){			
			$this->FetchNodeDetails($lCurrentNodeArr);			
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