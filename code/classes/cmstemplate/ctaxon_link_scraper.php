<?php
class ctaxon_link_scraper extends cbase_cachedata {
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
			$lTaxonName = $this->m_pubdata['taxon_name'];
			$lSiteName = $this->m_pubdata['site'];
			$lSites = GetLinksArray($lTaxonName, false);
			$lSite = $lSites[$lSiteName];
			if( is_array( $lSite )){
				$lSitesMatchArr = getSitesMatchArray($lTaxonName);
				$lCurrentSiteMatches = $lSitesMatchArr[$lSiteName];	
				//Ako url-to za gledane na rezultati e razli4no ot url-to kym koeto redirektvame - pishem go v check_url i gledame nego
				$lSiteUrl = $lSite['check_url'];
				$lPostForm = $lSite['check_postform'];
				$lPostFields = $lSite['check_postfields'];	
				if(!$lSiteUrl){
					$lSiteUrl = $lSite['href'];
					$lPostForm = $lSite['postform'];
					$lPostFields = $lSite['postfields'];					
				}
				$lPostfieldsParam = false;
				if( $lPostForm ){
					$lPostfieldsParam = parseStringPostfields($lPostFields);
				}
				$lSiteResponse = executeExternalQuery($lSiteUrl, $lPostfieldsParam);
				$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
				$lResultFound = false;
				$this->m_pubdata['site_response'] = $lSiteResponse;
									
				if( $lSiteResponse ){
					//~ var_dump($lSiteResponse);
					if( is_array( $lCurrentSiteMatches )){//Masiv s reg expove, koito ako matchnat vsichki - nqma rezultat
						foreach( $lCurrentSiteMatches  as $lSingleRegExpPattern ){
							//~ $lSingleRegExpPattern = str_replace('{taxon_name}', $lTaxonName, $lSingleRegExpPattern);
							$lSingleRegExpPattern = '/' . $lSingleRegExpPattern . '/im';
							//~ if( $lSiteName == 'gymnosperm' )
								//~ var_dump($lSingleRegExpPattern);
								//~ var_dump($lSiteResponse);
							if( !preg_match( $lSingleRegExpPattern, $lSiteResponse)){//Ima match
								$lResultFound = true;
								//~ var_dump($lSingleRegExpPattern);
								break;
							}
						}
					}
				}
				//~ var_dump($lResultFound);
				$this->m_pubdata['result_found'] = $lResultFound;
				$this->m_recordCount = (int)$lResultFound;
				//~ $lFileName = PATH_STORIES . $lSiteName . '_' . $lTaxonName . '_' . $lResultFound . '.html';				
				//~ $lFileHandle = fopen($lFileName, 'w');
				//~ fwrite($lFileHandle, $lSiteUrl . $lSiteResponse);
				//~ fclose($lFileHandle);
				
			}					
			$this->m_state++;			
		}
		$this->m_dontgetdata = true;
	}
	
	function SiteContainsResults(){
		return $this->m_pubdata['result_found'];
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
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
				
		return $lRet;
	}
	
}
?>