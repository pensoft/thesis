<?php
class clinksmenu extends cbase_cachedata {
	var $m_recordCount;
	var $m_currentRecord;
	var $m_pageSize;
	var $m_defTempls;
	var $m_page;
	var $m_RecordNumber;
	var $con;
	var $m_dontgetdata;
	var $m_links_data;

	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		$this->LoadDefTempls();
		$this->m_links_data = array();
	}
	
	function DontGetData($p) {
		$this->m_dontgetdata = $p;
	}	
	
	function LoadDefTempls() {
		if (!defined('D_EMPTY')) {
			define('D_EMPTY', 'global.empty');
		}
		
		$this->m_defTempls = array(G_HEADER => D_EMPTY, G_FOOTER => D_EMPTY, G_STARTRS => D_EMPTY, G_ENDRS => D_EMPTY, G_NODATA => D_EMPTY, G_ROWTEMPL => D_EMPTY, G_ROWTEMPL_AJAX => D_EMPTY);
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
			$this->m_links_data = $this->m_pubdata['m_links_data'];			
		}
	}
	
	function CheckForUbioLink(){
		$lLinks = $this->m_pubdata['def_links'];
		foreach($this->m_pubdata['links'] as $lExtLinkName => $lLinkType){
				if( $lLinkType == (int) AJAX_CLINKS_MENU_LINK )
					continue;
				$lLinkData = $lLinks[$lExtLinkName];
				if( $lLinkData['isubio'] )
					return true;
		}
		return false;
	}
	
	function GetData() {
		$this->CheckVals();
		if ($this->m_state >= 1) {
			$this->m_recordCount = 0;
			//~ $lLinks = GetLinksArray($this->m_pubdata['taxon_name']);
			$lLinks = $this->m_pubdata['def_links'];
					
			$lUbioLinkFinder = new cubio_link_finder(array(
				'taxon_name' => $this->m_pubdata['taxon_name'],
				'cache' => 'ubio_link_finder',
				'cachetimeout' => CACHE_TIMEOUT_LENGTH,
			));
			
			$lUbioLinkFinder->SetParentLoggerObjectId($this->m_logger_object_id);
			
			if( $this->CheckForUbioLink() )
				$lUbioLinkFinder->GetDataC();	
			foreach($this->m_pubdata['links'] as $lExtLinkName => $lLinkType){
				$lLinkData = $lLinks[$lExtLinkName];
				switch( (int) $lLinkType){
					default:
					case (int)STATIC_CLINKS_MENU_LINK:{
						
						if( is_array( $lLinkData )){
							if( $lLinkData['isubio'] ){
								
								$lLinkHref = $lUbioLinkFinder->GetSourceLink($lLinkData['ubio_title']);
								//~ var_dump($lLinkHref);
								//~ echo '<br/>';
								if( $lLinkHref != '' ){
									$this->m_links_data[$lExtLinkName] = array(
										'picsrc' => $lLinkData['picsrc'],
										'title' => $lLinkData['title'],
										'href' => $lLinkHref,
										'postform' => false,
										'results_exist' => true,			
									);
								}elseif($lLinkData['show_if_not_found']){
									$this->m_links_data[$lExtLinkName] = array(
										'picsrc' => $lLinkData['default_picsrc'],
										'title' => $lLinkData['title'],
										'href' => $lLinkData['default_href'],
										'postform' => $lLinkData['default_postform'],	
										'postfields' => $lLinkData['default_postfields'],
										'results_exist' => false,
									);
								}
							}else{
								if( $lLinkData['dont_check_for_existence'] && ( $lLinkData['results_exist'] || $lLinkData['show_if_not_found'] ) ){
									$this->m_links_data[$lExtLinkName] = array(
										'picsrc' => $lLinkData['picsrc'],
										'title' => $lLinkData['title'],
										'href' => $lLinkData['href'],
										'postform' => $lLinkData['postform'],	
										'postfields' => $lLinkData['postfields'],
										'results_exist' => $lLinkData['results_exist'],
									);
								}else{//Proverqvame dali ima rezultati
									$lCheckIfResultsExist = new ctaxon_link_scraper(array(
										'taxon_name' => $this->m_pubdata['taxon_name'],
										'site' => $lExtLinkName,
										'cache' => 'taxon_link_scraper',
										'cachetimeout' => CACHE_TIMEOUT_LENGTH,
									));
									$lCheckIfResultsExist->SetParentLoggerObjectId($this->m_logger_object_id);
									$lCheckIfResultsExist->GetDataC();
									$lResultFound = $lCheckIfResultsExist->SiteContainsResults();
									if( $lResultFound ){
										$this->m_links_data[$lExtLinkName] = array(
											'picsrc' => $lLinkData['picsrc'],
											'title' => $lLinkData['title'],
											'href' => $lLinkData['href'],
											'postform' => $lLinkData['postform'],
											'postfields' => $lLinkData['postfields'],
											'results_exist' => true,
										);
									}elseif( $lLinkData['show_if_not_found'] ){
										
										$this->m_links_data[$lExtLinkName] = array(
											'picsrc' => $lLinkData['default_picsrc'],
											'title' => $lLinkData['title'],
											'href' => $lLinkData['default_href'],
											'postform' => $lLinkData['default_postform'],
											'postfields' => $lLinkData['default_postfields'],
											'results_exist' => false,
										);
									}
								}
							}							
						}
						break;
					}
					case (int)AJAX_CLINKS_MENU_LINK:{
						$this->m_recordCount++;
						$this->m_links_data[$lExtLinkName] = array(
							'sitename' => $lExtLinkName,
							'picsrc' => $lLinkData['picsrc'],
							'title' => $lLinkData['title'],
							'href' => $lLinkData['default_href'],
							'postform' => $lLinkData['postform'],
							'ajax_link' => AJAX_MENU_LINK_SRV . '?taxon_name=' . $this->m_pubdata['taxon_name'] . '&site_name=' . $lExtLinkName . '&type=' . (int) $this->m_pubdata['ajax_link_template_type'],
						);						
						break;
					}
				}
				$this->m_links_data[$lExtLinkName]['link_type'] = $lLinkType;
				
			}
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);
			$this->m_recordCount += count($this->m_links_data);
			$this->m_pubdata['records'] = $this->m_recordCount;
			$this->m_pubdata['m_links_data'] = $this->m_links_data;			
			$this->m_state++;
			$this->m_dontgetdata = true;
		}
	}

	function FetchLinkDetails($pData) {
		if( is_array($pData) ){				
			foreach($pData as $lKey => $lVal) {				
				$this->m_pubdata[$lKey] = $this->m_currentRecord[$lKey] = $lVal;
			}			
		}		
	}
	
	function GetLinkHref($pLinkName){		
		return $this->m_links_data[$pLinkName]['href'];
	}
	
	function GetRows() {		
		foreach( $this->m_links_data as $lLinkName => $lLinkData){
			$this->m_pubdata['rownum']++;
			$this->FetchLinkDetails($lLinkData);
			$lLinkType = $lLinkData['link_type'];
			switch( (int) $lLinkType){
				default:
				case (int)STATIC_CLINKS_MENU_LINK:{					
					if ($this->m_pubdata['templadd'])
						$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL , $this->m_currentRecord[$this->m_pubdata['templadd']]));
					else 
						$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
					break;
				}
				case (int)AJAX_CLINKS_MENU_LINK:{
					if ($this->m_pubdata['templadd'])
						$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL_AJAX , $this->m_currentRecord[$this->m_pubdata['templadd']]));
					else 
						$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL_AJAX));
					break;
				}
			}			
			
						
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