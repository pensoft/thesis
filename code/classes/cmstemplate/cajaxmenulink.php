<?php	
class cajaxmenulink extends cbase_cachedata {
	var $m_dontgetdata;	
	var $m_UseDisplayRowNum;
	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		$this->LoadDefTempls();
		$this->m_pubdata['result_found'] = false;
		$this->m_UseDisplayRowNum = 0;
	}
	
	function setUseDisplayRowNum($pRowNum){
		$this->m_UseDisplayRowNum = $pRowNum;
	}	
	
	function DontGetData($p) {
		$this->m_dontgetdata = $p;
	}	
	
	function LoadDefTempls() {
		if (!defined('D_EMPTY')) {
			define('D_EMPTY', 'global.empty');
		}
		
		$this->m_defTempls = array(G_HEADER => D_EMPTY, G_FOOTER => D_EMPTY, G_STARTRS => D_EMPTY, G_ENDRS => D_EMPTY, G_NODATA => D_EMPTY, G_ROWTEMPL0 => D_EMPTY);
	}	
	
	function CheckVals() {
		if($this->m_state == 0) {			
			$this->m_state++;
		} else {
			// NOTICE
		}
	}
	
	/**
		Това го дефинираме наново, понеже винаги имаме резултат, 
		а реално трябва да гледаме дали резултата съществува т.е. $lCacheData['pubdata']['results_exist'] , а не 
		$lCacheData['recordCount'] 
	*/
	function GetDataCStepTwo($pEnableCache){//Opredelq dali se zimat dannite ot kesha - pri uspeh vryshta true inache false;
		if ($pEnableCache && $this->getDataCacheExists() && $this->getDataCacheTimeout()) {
			$lCacheData = unserialize($this->getDataCacheContents());
			
			/** 
				Ако има намерени резултати, или ако няма намерени резултати
				но кешът е прекалено нов - взимаме данните от кеша.
				
				Ако няма намерени резултати и кешът е по-стар от времето за кеш на обект без резултати
				(времето за кеш разделено на определена константа) - 
				генерираме кеша отново.
			*/
			if( (int) $lCacheData['pubdata']['results_exist']  || $this->getDataCacheNoResultTimeout()){
				$this->m_recordCount = $lCacheData['recordCount'];
				$this->m_pubdata = $lCacheData['pubdata'];
				$this->m_got_data_from_cache = true;
				$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);
				$this->RegisterLoggerObjectEvent(PROFILE_LOG_GOT_DATA_FROM_CACHE_EVENT, $this->m_got_data_from_cache);
				$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_PARSING_DATA_EVENT);	
				$this->m_state = 2;
				//~ echo get_class($this) . 'DATA GOT FROM CACHE <br/>';
				return true;
			}
		}
		return false;
	}
		
	function GetData() {
		$this->CheckVals();
		if ($this->m_state >= 1) {
			$this->m_recordCount = 0;
			$lTaxonName = $this->m_pubdata['taxon_name'];
			$lSiteName = $this->m_pubdata['site_name'];
			$lDefLinks = GetSingleSiteLinkArray($lTaxonName, $lSiteName);
			if( is_array($lDefLinks) ){
				$lLinkData = $lDefLinks;
				$this->m_pubdata['picsrc'] = $lLinkData['picsrc'];
				$this->m_pubdata['title'] = $lLinkData['title'];
				$this->m_pubdata['results_exist'] = true;
				if( $lLinkData['isubio'] ){
					$lUbioLinkFinder = new cubio_link_finder(array(
						'taxon_name' => $lTaxonName,
						'cache' => 'ubio_link_finder',
						'cachetimeout' => CACHE_TIMEOUT_LENGTH,
					));
					$lUbioLinkFinder->GetDataC();
					$lLinkHref = $lUbioLinkFinder->GetSourceLink($lLinkData['ubio_title']);
					if( $lLinkHref != '' ){
						$this->m_pubdata['href'] = $lLinkHref;
						$this->m_pubdata['postform'] = false;
						$this->m_pubdata['result_found'] = true;
						
					}elseif($lLinkData['show_if_not_found']){
						$this->m_pubdata['picsrc'] = $lLinkData['default_picsrc'];
						$this->m_pubdata['href'] = $lLinkData['default_href'];
						$this->m_pubdata['postform'] = $lLinkData['default_postform'];
						$this->m_pubdata['postfields'] = $lLinkData['default_postfields'];
						$this->m_pubdata['result_found'] = true;
						$this->m_pubdata['results_exist'] = false;
					}
				}else{
					if( $lLinkData['dont_check_for_existence'] && ( $lLinkData['results_exist'] || $lLinkData['show_if_not_found'] )){
						$this->m_pubdata['href'] = $lLinkData['href'];
						$this->m_pubdata['postform'] = $lLinkData['postform'];
						$this->m_pubdata['postfields'] = $lLinkData['postfields'];
						$this->m_pubdata['result_found'] = true;
						$this->m_pubdata['results_exist'] = $lLinkData['results_exist'];					
					}else{//Proverqvame dali ima rezultati
						$lCheckIfResultsExist = new ctaxon_link_scraper(array(
							'taxon_name' => $lTaxonName,
							'site' => $lSiteName,
							'cache' => 'taxon_link_scraper',
							'cachetimeout' => CACHE_TIMEOUT_LENGTH,
						));
						$lCheckIfResultsExist->SetParentLoggerObjectId($this->m_logger_object_id);
						$lCheckIfResultsExist->GetDataC();
						$lResultFound = $lCheckIfResultsExist->SiteContainsResults();
						
						if( $lResultFound ){
							$this->m_pubdata['href'] = $lLinkData['href'];
							$this->m_pubdata['postform'] = $lLinkData['postform'];	
							$this->m_pubdata['postfields'] = $lLinkData['postfields'];
							$this->m_pubdata['result_found'] = true;
						}elseif( $lLinkData['show_if_not_found'] ){
							$this->m_pubdata['picsrc'] = $lLinkData['default_picsrc'];
							$this->m_pubdata['href'] = $lLinkData['default_href'];
							$this->m_pubdata['postform'] = $lLinkData['default_postform'];
							$this->m_pubdata['postfields'] = $lLinkData['default_postfields'];
							$this->m_pubdata['result_found'] = true;
							$this->m_pubdata['results_exist'] = false;
						}
					}
				}
			}
			
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);			
			$this->m_recordCount = (int) $this->m_pubdata['result_found'];
			$this->m_dontgetdata = true;
			$this->m_state++;
		}
	}
	
	function GetLinkHref(){		
		return $this->m_pubdata['href'];
	}
	
	function GetRows() {		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL .  (int) $this->m_UseDisplayRowNum));
		
		return $lRet;
	}
	
	function Display() {
		
		if (!$this->m_dontgetdata)
			$this->GetDataC();
				
		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if ($this->m_pubdata['result_found'] == 0) {
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