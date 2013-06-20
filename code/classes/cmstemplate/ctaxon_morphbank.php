<?php
class ctaxon_morphbank extends cbase_cachedata {
	var $m_currentRecord;
	var $m_pageSize;
	var $m_defTempls;
	var $m_page;
	var $m_RecordNumber;
	var $con;
	var $m_dontgetdata;
	var $m_images;

	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		//~ $this->m_pubdata['results_count'] = (int) $this->m_pubdata['results_count'] ? (int) $this->m_pubdata['results_count'] : 10;
		//~ $this->m_pubdata['start_num'] = (int) $this->m_pubdata['start_num'] ? (int) $this->m_pubdata['start_num'] : 1;
		//~ $this->m_taxonid = 0;
		$this->m_pubdata['thumbflag'] = 0;
		$this->m_pubdata['extlink'] = BHL_TAXON_EXTERNAL_LINK . $this->m_pubdata['taxon_name'];
		$this->m_xml_result = 0;
		$this->m_pageSize = (int)$this->m_pubdata["pagesize"];
		$this->m_bulk_xml = '';
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
			$this->m_images = $this->m_pubdata['m_images'];	
		}
	}
	
	function GetData() {		
		$this->CheckVals();
		if ($this->m_state >= 1) {
			$lUrl = BHL_MORPHBANK_LINK . rawurlencode($this->m_pubdata['taxon_name']);
			if((int) $this->m_pubdata['pagesize'] > 0 ){
				$lUrl .= '&limit=' . $this->m_pubdata['pagesize'];
			}
			//~ echo $lUrl;
			$lQueryResult = executeExternalQuery($lUrl, false, "", 5);
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
			//~ var_dump($lQueryResult);
			if($lQueryResult) {
				$lDom = new DOMDocument();
				if($lDom->loadXML($lQueryResult)){
					$lXpath = new DOMXPath($lDom);
					$lXpath->registerNamespace('mb', 'http://www.morphbank.net/mbsvc/');
					$lXpathQuery = '/mb:response/image/thumbUrl';
					$lXPathResult = $lXpath->query($lXpathQuery);	
					//~ var_dump($lXPathResult);
					$this->m_recordCount = $lXPathResult->length;
					for( $i = 0; $i < $lXPathResult->length; ++$i){
						$lImageUrl = $lXPathResult->item($i)->textContent;
						$lImageUrl = str_replace('&imgType=thumb', '&imgType=jpeg', $lImageUrl);
						$this->m_images[$lImageUrl] = array(
							'title' => $lImageUrl,
							'url' => $lImageUrl,
						);						
					}
				}
			}
			$this->m_pubdata['m_images'] = $this->m_images;	
			$this->m_state++;
		}
		$this->m_dontgetdata = true;
	}
	
	/**
		m_images ima format
		ime_na_snimka => array(
			title => ime_na_snimka
			url => url_na_snimka
		)
	*/
	function FetchImageDetails($pImageDetails){		
		foreach($pImageDetails as $lKey => $lVal ){
			$this->m_pubdata[$lKey] = $this->m_currentRecord[$lKey] = $lVal;
		}
	}
	
	function GetRows() {		
		foreach( $this->m_images as $lImageName => $lImageDetails){
			$this->m_pubdata['rownum']++;			
			$this->FetchImageDetails($lImageDetails);			
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