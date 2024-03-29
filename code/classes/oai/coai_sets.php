<?php
/**
 * Този клас отговаря за заявките от ит VERB_GET_LIST_SETS.
 * За повече информация относно функционалността - http://www.openarchives.org/OAI/openarchivesprotocol.html#ListSets
 *
 */
class coai_sets extends crs_custom_pageing {	
	var $m_hasError;
	var $m_errCode;
	var $m_errMsg;
	var $m_resumptionToken;
	
	function __construct($pFieldTempl){		
		parent::__construct($pFieldTempl);
		$this->m_hasError = 0;
		$this->m_errCode = '';
		$this->m_errMsg = '';
		$this->m_page = 0;
		
		$this->m_resumptionToken = $this->m_pubdata['resumption_token'];
		$this->m_pubdata['resumption_token_label'] = RESUMPTION_TOKEN_LABEL;//Labela za parametyra vyv verb xml-a			
		//Тук resumption_token играе ролята на page параметър
				
		$this->parseResumptionToken();
	}
	
	protected function setErr($pErrCode, $pErrMsg){
		$this->m_errCode = $pErrCode;
		$this->m_errMsg = $pErrMsg;
		$this->m_hasError =  1;
	}
	
/**
	 * 
	 * В тази функция обработваме подадения ResumptionToken.
	 * Очакваме това да е base64_encode-нат стринг във следния формат
	 * page=PAGE
	 */
	protected function parseResumptionToken(){
		$lToken = trim($this->m_resumptionToken);
		
		if($lToken == '')
			return;		
		
		$lToken = base64_decode($lToken);
		
		//Сплитваме различните параметри
		$lTokenParts = explode('&', $lToken);
		$lTokenParsedParts = array();
		//Разделяме параметрите в масив с ключ име на параметър => стойност
		foreach ($lTokenParts as $lCurrentPart) {			
			$lParsedPart = explode('=', $lCurrentPart);
			if(count($lParsedPart) > 1){
				$lTokenParsedParts[$lParsedPart[0]] = $lParsedPart[1]; 
			}
		}
		$this->m_page = (int)$lTokenParsedParts['page'];

	}
	
	function GetData() {
		$this->m_pubdata['sqlstr'] = '
			SELECT j.title AS name, j.URL_TITLE AS spec, 1 as ord
			FROM J_JOURNALS j
			UNION
			SELECT \'ec_fundedresources\' AS name, \'ec_fundedresources\' AS spec, 0 as ord
			ORDER BY ord
		';
		parent::GetData();
	}
	
	
	
	protected function GetErrorRow(){
		$this->m_pubdata['err_code'] = $this->m_errCode;
		$this->m_pubdata['err_msg'] = $this->m_errMsg;		
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_ERR_TEMPL));
	}
	
	
	function Display() {
		$this->GetData();		
		//Ако няма грешка - гледаме дали поддържаме някакви set-ове, и ако не е така - гърмим с грешка
		if( !$this->m_hasError && !$this->m_recordCount){
			$this->setErr('noSetHierarchy', getstr('oai.noSetHierarchy'));
		}
		
		$this->m_pubdata['nav'] = $this->DisplayPageNav($this->m_page);
		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if ($this->m_hasError) {
			$lRet .= $this->GetErrorRow();			
		} else {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $this->GetRows();
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
		
		if ($this->m_pageSize && $this->m_recordCount && !(int)$this->m_pubdata['hidedefpaging'])
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING));
			
		
		return $lRet;
	}
	
	/**
	 * 
	 * В тази функция връшаме resumptionToken за подадената страница.
	 * Резултата е base64_encode-нат стринг във следния формат
	 * set=SET&from=FROM&until=UNTIL&metadata_prefix=METADATA_PREFIX&page=PAGE
	 * Ако е подаден параметъра $pUrlEncode на резултата се прави urlencode
	 */
	function GetResumptionToken($pPage, $pUrlEncode = false){
		$lResult = 'page=' . $pPage;
		$lResult = base64_encode($lResult);
		if((int)$pUrlEncode)
			$lResult = urlencode($lResult);
		return $lResult;
	}
	
	

}