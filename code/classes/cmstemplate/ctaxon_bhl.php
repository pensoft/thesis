<?php
class ctaxon_bhl extends cbase_cachedata {
	var $m_currentRecord;
	var $m_pageSize;
	var $m_defTempls;
	var $m_page;
	var $m_RecordNumber;
	var $con;
	var $m_dontgetdata;
	var $m_xml_result;
	var $m_bulk_xml;
	

	function __construct($pFieldTempl) {			
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];			
		//~ $this->m_pubdata['results_count'] = (int) $this->m_pubdata['results_count'] ? (int) $this->m_pubdata['results_count'] : 10;
		//~ $this->m_pubdata['start_num'] = (int) $this->m_pubdata['start_num'] ? (int) $this->m_pubdata['start_num'] : 1;
		//~ $this->m_taxonid = 0;
		$this->m_pubdata['thumbflag'] = 0;
		$this->m_pubdata['extlink'] = BHL_TAXON_EXTERNAL_LINK . $this->m_pubdata['taxon_name'];
		$this->m_pubdata['nodata'] = 0;
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
	
	//~ function GetDataC(){
		//~ parent::GetDataC();
		//~ if( $this->m_got_data_from_cache ){//Slagame gi poneje se polzvat pri display-a			
			//~ $this->m_xml_result = $this->m_pubdata['m_xml_result'];
			//~ $this->m_bulk_xml = $this->m_pubdata['m_bulk_xml'];
		//~ }
	//~ }
	
	function GetDataCStepTwo($pEnableCache){//Predefinirame go poneje rezultata ot XPATH ne moje da se serializira
		if ($pEnableCache && $this->getDataCacheExists() && $this->getDataCacheTimeout()) {
			$lCacheData = unserialize($this->getDataCacheContents());
			
			/** 
				Ако има намерени резултати, или ако няма намерени резултати
				но кешът е прекалено нов - взимаме данните от кеша.
				
				Ако няма намерени резултати и кешът е по-стар от времето за кеш на обект без резултати
				(времето за кеш разделено на определена константа) - 
				генерираме кеша отново.
			*/
			if( (int) $lCacheData['recordCount'] || $this->getDataCacheNoResultTimeout()){
				$this->m_recordCount = $lCacheData['recordCount'];
				$this->m_pubdata = $lCacheData['pubdata'];
				$this->m_bulk_xml = $this->m_pubdata['m_bulk_xml'];
				$this->m_got_data_from_cache = true;
				$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);
				
				
				//~ var_dump($this->m_bulk_xml);
				$lDom = new DOMDocument();
				if($lDom->loadXML($this->m_bulk_xml)){					
					$lXpath = new DOMXPath($lDom);
					$lXpathQuery = '/Response/Status';
					$lXPathResult = $lXpath->query($lXpathQuery);
					if($lXPathResult->length && $this->m_pubdata['xmlresult'] == 'ok'){
						$lXpathQuery = '/Response/Result/Titles/Title';
						$lXPathResult = $lXpath->query($lXpathQuery);
						$this->m_xml_result = $lXPathResult;
						//~ var_dump($lXPathResult);
					}
				}
				
				
				
				
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
		global $gBHLData;
		$this->CheckVals();
		if ($this->m_state >= 1) {
			$lUrl = BHL_TAXON_LINK . str_replace(' ', '+', $this->m_pubdata['taxon_name']);
			$lQueryResult = executeExternalQuery($lUrl, false, "cb_bhl", 30);
			$this->RegisterLoggerObjectEvent(PROFILE_LOG_FINISHED_RETRIEVING_DATA_EVENT);	
			$this->m_bulk_xml = $gBHLData;
			if($this->m_bulk_xml) {
				if (!$lQueryResult) //ako ne sme procheli celia rezultat triabva da go opravim
					$this->CorrectXML();
				//~ if ($_SERVER['REMOTE_ADDR'] == '193.194.140.198') {
					//~ echo $gBHLData . "!!!!" . $this->m_bulk_xml;
				//~ }

				$lDom = new DOMDocument();
				if($lDom->loadXML($this->m_bulk_xml)){					
					$lXpath = new DOMXPath($lDom);
					$lXpathQuery = '/Response/Status';
					$lXPathResult = $lXpath->query($lXpathQuery);
					$this->m_pubdata['xmlresult'] = $lXPathResult->item(0)->textContent;
					if($lXPathResult->length && $this->m_pubdata['xmlresult'] == 'ok'){
						$lXpathQuery = '/Response/Result/Titles/Title';
						$lXPathResult = $lXpath->query($lXpathQuery);
						$this->m_xml_result = $lXPathResult;
						if ((int)$this->m_xml_result->length > 0) {
							$this->m_pubdata['rownum'] = 1;
							//~ $lXpathQuery = '/Response/Result/Titles/Title/Items/Item/Pages/Page';
							//~ $lXPathResult = $lXpath->query($lXpathQuery);
							//~ $this->m_pubdata['numpages'] = $lXPathResult->length;
							$this->m_pubdata['ThumbnailUrl'] = $lXPathResult->item(0)->getElementsByTagName('ThumbnailUrl')->item(0)->nodeValue;
							$this->m_pubdata['FullSizeImageUrl'] = $lXPathResult->item(0)->getElementsByTagName('PageUrl')->item(0)->nodeValue;
						}
					}					
					$this->m_recordCount = $this->m_xml_result->length;
					$this->m_pubdata['numtitles'] = $this->m_recordCount;
				}
			}
			$this->m_pubdata['m_xml_result'] = $this->m_xml_result;
			$this->m_pubdata['m_bulk_xml'] = $this->m_bulk_xml;
			$this->m_state++;			
		}
		$this->m_dontgetdata = true;
	}
	
	//~ function FetchNodeDetails($pNode) {
		//~ if( $pNode ){			
			//~ foreach($pNode->childNodes as $lChild) {
				//~ if( $lChild->nodeType != 1 )//Obrabotvame samo elementite
					//~ continue;
				//~ $lKey = strtolower($lChild->nodeName);				
				//~ $this->m_pubdata[$lKey] = $this->m_currentRecord[$lKey] = $lChild->textContent;
			//~ }			
		//~ }		
	//~ }

	function FetchTaxonDetails($pChild) {
		//~ var_dump($this->m_xml_result);
		if($this->m_xml_result){
			$this->m_pubdata['title'] = $pChild->getElementsByTagName('ShortTitle')->item(0)->nodeValue;
			$this->m_pubdata['titleurl'] = $pChild->getElementsByTagName('TitleUrl')->item(0)->nodeValue;
			$this->m_pubdata['items_pages'] = '';
			$this->m_pubdata['pgcounter'] = 0;
			foreach($pChild->getElementsByTagName('Items') as $lItem) {
				$this->m_pubdata['volume'] = $lItem->getElementsByTagName('Volume')->item(0)->nodeValue;
				$this->m_pubdata['items_pages'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_VOLUME_TEMPL));
				$lPages = $lItem->getElementsByTagName('Pages')->item(0)->getElementsByTagName('Page');
				$this->m_pubdata['pgcount'] = $lPages->length;
				foreach($lPages as $lPage) {
						$this->m_pubdata['pgcounter']++;
						$this->m_pubdata['pg'] = $lPage->getElementsByTagName('PageNumbers')->item(0)->getElementsByTagName('PageNumber')->item(0)->getElementsByTagName('Number')->item(0)->nodeValue;
						$this->m_pubdata['pgurl'] = $lPage->getElementsByTagName('PageUrl')->item(0)->nodeValue;
						$this->m_pubdata['items_pages'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGE_TEMPL));
				}
			}
		}
	}
	
	function CorrectXML() {
		$lPos = strripos($this->m_bulk_xml, "<title>");
		if ($lPos) {
			$lXml = substr($this->m_bulk_xml, 0, $lPos + mb_strlen("<title>"));
			$this->m_bulk_xml = $lXml . "</Title></Titles></Result></Response>";
		}
	}
	
	function GetTaxonDetails() {
		if( !$this->m_xml_result)		
			return;
		
		foreach($this->m_xml_result as $lChild) {
			$this->FetchTaxonDetails($lChild);
			
			if ($this->m_pubdata['templadd'])
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL , $this->m_currentRecord[$this->m_pubdata['templadd']]));
			else 
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
			
			$this->m_pubdata['rownum']++;
			if ($this->m_pubdata['rownum'] >= $this->m_pageSize)
				break;
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
		//~ var_dump($this->m_xml_result);
		if (!(int)$this->m_recordCount) {
			$this->m_pubdata['nodata'] = 1;
			if( $this->m_pubdata['xmlresult'] != 'ok' ){//kogato ne mi e vurnal veren xml
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA_WRONG_XML));
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
			}else{//Prosto nqma rezultati
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
			}
		} else {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $this->GetTaxonDetails();			
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
		return $lRet;
	}
}
?>