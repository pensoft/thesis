<?php
class ctaxon_yahooimages extends cbase_cachedata {
	var $m_recordCount;
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
			
			$lUrl = YAHOO_IMAGES_URL . urlencode($this->m_pubdata['taxon_name']) . '&start=' . $this->m_pubdata['start_num'] . '&results=' . $this->m_pubdata['results_count'];
			$lQueryResult = executeExternalQuery($lUrl);	
			$lMapFound = false;
			//~ var_dump($lQueryResult);
			if( $lQueryResult ){						
				$lDom = new DOMDocument();	
				if($lDom->loadXML($lQueryResult)){					
					$lXpath = new DOMXPath($lDom);
					$lXpath->registerNamespace('def', 'urn:yahoo:srchmi');
					$lXpathQuery = '/def:ResultSet/def:Result';	 							
					$lXPathResult = $lXpath->query($lXpathQuery);									
					$this->m_pubdata['resultXmlNodes'] = $lXPathResult;
					$this->m_recordCount = $lXPathResult->length;	
					
				}
			}
			$this->m_state++;
		}
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
	
	function GetRows() {
		$lNodes = $this->m_pubdata['resultXmlNodes'];
		if( !($lNodes instanceof  DOMNodeList ) || !$lNodes->length )
			return;
		for( $i = 0; $i < $lNodes->length; ++$i){
			$this->m_pubdata['rownum']++;
			$this->FetchNodeDetails($lNodes->item($i));			
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