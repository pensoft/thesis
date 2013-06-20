<?php
class ctaxon_mediawikiimages extends cbase_cachedata {
	var $m_currentRecord;
	var $m_pageSize;
	var $m_defTempls;
	var $m_page;
	var $con;
	var $m_dontgetdata;
	var $m_category_names;
	var $m_images;

	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		$this->m_pubdata['results_count'] = (int) $this->m_pubdata['results_count'] ? (int) $this->m_pubdata['results_count'] : 10;
		$this->m_pubdata['start_num'] = (int) $this->m_pubdata['start_num'] ? (int) $this->m_pubdata['start_num'] : 1;
		$this->LoadDefTempls();
		$this->m_category_names = array();
		$this->m_images = array();
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
			$this->m_category_names = $this->m_pubdata['m_category_names'];
			$this->m_images = $this->m_pubdata['m_images'];			
			$this->m_RecordNumber = $this->m_pubdata['m_RecordNumber'];			
		}
	}
	
	function GetCategoryNames(){
		$lUrl = WIKIMEDIA_COMMONS_API_URL . '?action=opensearch&format=xml&search=' . rawurlencode($this->m_pubdata['taxon_name']);
		$lQueryResult = executeExternalQuery($lUrl);			
		$this->m_category_names = array();
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
						$this->m_category_names[] = $lCategoryName;
					}
				}
			}
		}		
	}
	
	function GetImageNames(){
		if( !count($this->m_category_names) )
			return;
		$lUrl = WIKIMEDIA_COMMONS_API_URL . '?action=query&prop=images&format=xml&titles=' . rawurlencode(implode('|', $this->m_category_names));
		$lQueryResult = executeExternalQuery($lUrl);
		//~ var_dump($lQueryResult);
		$this->m_images = array();
		if( $lQueryResult ){						
			$lDom = new DOMDocument();	
			if($lDom->loadXML($lQueryResult)){					
				$lXpath = new DOMXPath($lDom);				
				$lXpathQuery = '/api/query/pages/page/images/im/@title';	 							
				$lXPathResult = $lXpath->query($lXpathQuery);
				$lResults = 0;
				for( $i = $this->m_pubdata['start_num'] - 1; $i < $lXPathResult->length && $lResults < $this->m_pubdata['results_count']; ++$i){
					$lResults++;
					$lPhotoName = $lXPathResult->item($i)->textContent;
					$lPhotoName = mb_substr($lPhotoName, mb_strlen('File:'));//Mahame pyrvonachalniq "File:" ot imeto
					
					if( $lPhotoName ){
						$this->m_images[$lPhotoName] = array('title' => $lPhotoName);
					}
				}
			}
		}		
	}
	
	function GetImageSource($pImageName){
		$lUrl = WIKIMEDIA_COMMONS_API_URL . '?action=query&format=xml&list=allimages&ailimit=1&aifrom=' . rawurlencode($pImageName);
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
	
	function GetData() {
		/**
			Apito procedira po sledniq nachin
			1. Tyrsim statii svyrzani s opredelen taxon
			2. Tyrsim imenata na snimkite svyrzani s ve4e namerenite statii
			3. Po imenata na snimkite vzimame url-a im
		*/
		$this->CheckVals();
		if ($this->m_state >= 1) {
			$this->GetCategoryNames();
			$this->GetImageNames();
			
			foreach($this->m_images as $lImageName => &$lImageData){
				$lSrc = $this->GetImageSource($lImageName);				
				$lImageData['url'] = $lSrc;
			}
			/**
				m_images ima format
				ime_na_snimka => array(
					title => ime_na_snimka
					url => url_na_snimka
				)
			*/
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
			$this->m_recordCount = count($this->m_images);
			//~ var_dump($this->m_images);
			$this->m_pubdata['m_category_names'] = $this->m_category_names;
			$this->m_pubdata['m_images'] = $this->m_images;			
			$this->m_pubdata['m_RecordNumber'] = $this->m_RecordNumber;
			$this->m_state++;
		}
		$this->m_dontgetdata = true;
	}

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