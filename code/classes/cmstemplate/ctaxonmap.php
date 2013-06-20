<?php
class ctaxonmap extends cbase_cachedata {
	var $m_defTempls;
	var $m_page;
	var $con;
	var $m_dontgetdata;

	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_recordCount = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		$this->LoadDefTempls();
		$this->m_pubdata['number_of_results'] = 0;
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
			$lUrl = TAXON_MAP_SRV . urlencode($this->m_pubdata['taxon_name']);
			$lQueryResult = executeExternalQuery($lUrl);	
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
			$lMapFound = false;
			$lGbifDataArr = GetLinksArray($this->m_pubdata['taxon_name'], false);
			$lGbifDataArr = $lGbifDataArr['gbif'];
			$this->m_pubdata['gbif_link'] = $lGbifDataArr['default_href'];
			$this->m_pubdata['default_postform'] = $lGbifDataArr['postform'];
			if( $lQueryResult ){
				$lDom = new DOMDocument();	
				if($lDom->loadXML($lQueryResult)){
					$lXpath = new DOMXPath($lDom);
					$lXpathQuery = '/taxa/taxon/mapHTML';	 		
					$lXPathResult = $lXpath->query($lXpathQuery);
					if( $lXPathResult->length ){
						$lMapIframe = $lXPathResult->item(0);
						if( $lMapIframe ){										
							$this->m_pubdata['result'] = $lMapIframe->textContent;	
							$this->CorrectIframeLinks();
							$this->m_pubdata['number_of_results'] = 1;
							$this->m_recordCount = 1;							
						}
					}
					
					$lXpathQuery = '/taxa/taxon/id';	 		
					$lXPathResult = $lXpath->query($lXpathQuery);
					if( $lXPathResult->length ){
						$this->m_pubdata['gbif_id'] = $lXPathResult->item(0)->textContent;						
						$this->m_pubdata['gbif_link'] = GBIF_TAXON_LINK . $this->m_pubdata['gbif_id'];
						$this->m_pubdata['postform'] = false;
					}
				}
			}
			$this->m_state++;			
			$this->m_dontgetdata = true;
		}
	}
	
	function CorrectIframeLinks(){
		
		$lDom = new DOMDocument();	
		if($lDom->loadXML($this->m_pubdata['result'])){
			$lXpath = new DOMXPath($lDom);
			$lXpathQuery = '/iframe';	 		
			$lXPathResult = $lXpath->query($lXpathQuery);
			if( $lXPathResult->length ){
				$lIframe = $lXPathResult->item(0);
				$lIframeSrc = $lIframe->getAttribute('src');
				$lLinkPrefix = addslashes(ParseTaxonExternalLink($this->m_pubdata['taxon_name']));
				$this->m_pubdata['result'] = '<iframe name="gbifIframe" width="730" scrolling="no" height="410" frameborder="0" vspace="1" hspace="1" src="' . IFRAME_PROXY_URL . '?url=' . rawurlencode($lIframeSrc) . '"  onload="correctIframeLinks(this, \'' . $lLinkPrefix . '\')"></iframe>';
			}
		}
		//~ $this->m_pubdata['result'] = str_replace('<iframe', '<iframe onload="correctIframeLinks(this, \'' . $lLinkPrefix . '\')" ', $this->m_pubdata['result']);
	}

	function Display() {
		
		if (!$this->m_dontgetdata)
			$this->GetDataC();
		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if ($this->m_recordCount == 0) {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
		} else {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));			
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
				
		return $lRet;
	}
	
}
?>