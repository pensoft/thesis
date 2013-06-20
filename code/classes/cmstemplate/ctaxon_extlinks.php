<?php
class ctaxon_extlinks extends cbase_cachedata {
	var $m_currentRecord;
	var $m_pageSize;
	var $m_defTempls;
	var $m_page;
	var $m_RecordNumber;
	var $con;
	var $m_dontgetdata;
	var $m_link_details;
	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		$this->m_pubdata['results_count'] = (int) $this->m_pubdata['results_count'] ? (int) $this->m_pubdata['results_count'] : 10;
		$this->m_pubdata['start_num'] = (int) $this->m_pubdata['start_num'] ? (int) $this->m_pubdata['start_num'] : 1;		
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

	function GetDataC(){
		parent::GetDataC();
		if( $this->m_got_data_from_cache ){//Slagame gi poneje se polzvat pri display-a			
			$this->m_link_details = $this->m_pubdata['m_link_details'];			
		}
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
			$lDataBaseName = $this->m_pubdata['database'];
			$this->m_pubdata['see_all_link'] = NCBI_SUBTREE_LINK . '&db=' . $lDataBaseName . '&term=' . $this->m_pubdata['taxon_name'];
			$lUrl = EUTILS_ESEARCH_SRV . 'term=' . str_replace(' ', '+', $this->m_pubdata['taxon_name']) . '&retmode=xml&retmax=' . (int) $this->m_pubdata['results_count'] . '&tool=' . EUTILS_TOOL_NAME . '&db=' . $lDataBaseName;	
			$lQueryResult = executeExternalQuery($lUrl);
			$this->m_link_details = array();
			$lDom = new DOMDocument();
			if($lDom->loadXML($lQueryResult)){
				$lXpath = new DOMXPath($lDom);
				$lXpathQuery = '/eSearchResult/IdList/Id';			
				$lXPathResult = $lXpath->query($lXpathQuery);
				
				foreach( $lXPathResult as $lSingleId){//Vzimame id-tata i stroim linkove
					$lResourceId = $lSingleId->textContent;
					if( $lResourceId ){
						$lResourceLink = getTaxonResourceLink($lDataBaseName, $lResourceId);
						$this->m_link_details[$lResourceId] = array('title' => $lResourceId, 'link' => $lResourceLink);
					}
				}
				
				if( is_array( $this->m_link_details ) && count($this->m_link_details) ){
					$lCurrentDatabaseHasResults = false;
					$lIds = array_keys($this->m_link_details);
					if( is_array($lIds) && count($lIds) ){//Stroim title-i za linkovete
						
						$lTitleUrl = EUTILS_ESUMMARY_SRV . '&db=' . $lDataBaseName . '&id=' . implode(',', $lIds) . '&retmode=xml';						
						$lTitleQueryResult = executeExternalQuery($lTitleUrl);	
						if( $lTitleQueryResult ){
							$lTitleDom = new DOMDocument();								
							if($lTitleDom->loadXML($lTitleQueryResult)){								
								$lTitleXpath = new DOMXPath($lTitleDom);
								$lTitleXpathQuery = '/eSummaryResult/DocSum';
								$lElements = $lTitleXpath->query($lTitleXpathQuery);
								foreach( $lElements as $lSingleElement){
									$lIdXpath = './Id';
									$lIdXpathResult = $lTitleXpath->query($lIdXpath, $lSingleElement);
									if( $lIdXpathResult->length ){
										$lCurrentId = $lIdXpathResult->item(0)->textContent;
										$lCurrentTitleXpath = "./Item[@Name='Title']";
										$lCurrentTitleResult = $lTitleXpath->query($lCurrentTitleXpath, $lSingleElement);
										if( $lCurrentTitleResult->length ){
											$lCurrentTitle = $lCurrentTitleResult->item(0)->textContent;
											$this->m_link_details[$lCurrentId]['title'] = $lCurrentTitle;
											
										}
									}
								}								
							}
						}						
					}
				}
			}
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
			$this->m_recordCount = count($this->m_link_details);
			$this->m_pubdata['m_link_details'] = $this->m_link_details;
			$this->m_state++;			
		}
		$this->m_dontgetdata = true;
	}

	function FetchLinkDetails($pLinkDetails) {
		if( is_array($pLinkDetails) ){	
			foreach( $pLinkDetails as $key => $val ){				
				$this->m_pubdata[$key] = $this->m_currentRecord[$key] = $val;
			}
		}		
	}
	
	function GetRows() {
		foreach($this->m_link_details as $lLinkId => $lLinkDetails){		
			$this->m_pubdata['rownum']++;
			$this->FetchLinkDetails($lLinkDetails);			
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