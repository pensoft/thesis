<?php
/**
 * Този клас отговаря за заявките от тип VERB_GET_LIST_RECORDS и тип VERB_GET_LIST_IDENTIFIERS.
 * За повече информация относно функционалността - http://www.openarchives.org/OAI/openarchivesprotocol.html#ListRecords
 *
 */
class coai_records extends crs_custom_pageing {	
	var $m_hasError;
	var $m_errCode;
	var $m_errMsg;
	var $m_resumptionToken;
	var $m_from;
	var $m_until;
	var $m_set;
	var $m_metadataPrefix;
	var $m_allowedSets;
	var $m_additionalWhereClause;
	
	function __construct($pFieldTempl){		
		parent::__construct($pFieldTempl);
		$this->m_hasError = 0;
		$this->m_errCode = '';
		$this->m_errMsg = '';
		
		$this->m_page = 0;
		
		$this->m_set = $this->m_pubdata['set'];
		$this->m_from = $this->m_pubdata['from'];
		$this->m_until = $this->m_pubdata['until'];
		$this->m_metadataPrefix = $this->m_pubdata['metadata_prefix'];
		$this->m_resumptionToken = $this->m_pubdata['resumption_token'];
		
		$this->parseResumptionToken();
		
		$this->m_pubdata['resumption_token_label'] = RESUMPTION_TOKEN_LABEL;//Labela za parametyra vyv verb xml-a
		$this->m_pubdata['set_label'] = SET_LABEL;//Labela za parametyra vyv verb xml-a
		$this->m_pubdata['from_label'] = FROM_LABEL;//Labela za parametyra vyv verb xml-a
		$this->m_pubdata['until_label'] = UNTIL_LABEL;//Labela za parametyra vyv verb xml-a
		$this->m_pubdata['metadata_prefix_label'] = METADATA_PREFIX_LABEL;//Labela za parametyra vyv verb xml-a
		
		
		
		
		$this->m_additionalWhereClause = '';
		$this->m_pubdata['templadd'] = 'metadata_prefix_search';
		
		if( !$this->m_hasError && !$this->m_metadataPrefix ){
			$this->setErr('badArgument', getstr('oai.missingMetadataPrefixParam'));
		}
	}
	
	/**
	 * 
	 * В тази функция обработваме подадения ResumptionToken.
	 * Очакваме това да е base64_encode-нат стринг във следния формат
	 * set=SET&from=FROM&until=UNTIL&metadata_prefix=METADATA_PREFIX&page=PAGE
	 */
	protected function parseResumptionToken(){
		$lToken = trim($this->m_resumptionToken);
		
		if($lToken == '')
			return;
		global $gAllowedMetadataFormats;
		
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
		$this->m_set = $lTokenParsedParts['set'];
		$this->m_from = $lTokenParsedParts['from'];
		$this->m_until = $lTokenParsedParts['until'];
		$this->m_metadataPrefix = $lTokenParsedParts['metadata_prefix'];
		$this->m_page = (int)$lTokenParsedParts['page'];
		
//		var_dump($this->m_set);
//		var_dump($this->m_from);
//		var_dump($this->m_until);
//		var_dump($this->m_metadataPrefix);
//		var_dump($this->m_page);
		
		if( !$this->m_metadataPrefix ){
			$this->setErr('badResumptionToken', getstr('oai.badResumptionToken'));
			return;
		}
		
		if( !array_key_exists($this->m_metadataPrefix, $gAllowedMetadataFormats)){//Gledame dali poddyrjame tozi format
			$this->setErr('badResumptionToken', getstr('oai.badResumptionToken'));
			return;
		}
	}
	
	protected function setErr($pErrCode, $pErrMsg){
		$this->m_errCode = $pErrCode;
		$this->m_errMsg = $pErrMsg;
		$this->m_hasError =  1;
	}
	
	/**
	 * Първо гледаме дали поддържаме подадения формат,
	 * после гледаме дали поддържаме множества и след това чак гледаме за резултати на заявката
	 */
	function GetData() {
		global $gAllowedMetadataFormats;
		
		if($this->m_hasError)
			return ;
		
		if( !array_key_exists($this->m_metadataPrefix, $gAllowedMetadataFormats)){//Gledame dali poddyrjame tozi format
			$this->setErr('cannotDisseminateFormat', getstr('oai.cannotDisseminateFormat'));
			return;
		}
		
		
		
		$this->m_pubdata['sqlstr'] = '
			SELECT a.doi as identifier, DATE_FORMAT(i.date_published, ' . DATE_SQL_FORMAT . ') as moddate, DATE_FORMAT(i.date_published, ' . DATE_SQL_FORMAT . ') as pubdate, DATE_FORMAT(i.date_published, \'%Y\') as pubyear,
			a.author_id, a.reg_co_authors, a.keywords, a.title, a.abstract as abstract, 
			s.title as section_type, a.start_page as start_page, a.end_page as end_page, i.volume as issue_volume, j.title as journal_title, g.file_id as file_id,
			j.URL_TITLE as set_specs, j.URL_TITLE as journal_url_title, a.article_id, \'' . q($this->m_metadataPrefix) . '\' as metadata_prefix_search,
			i.number as issue_number, i.shownumber as show_issue_number, CONCAT(\'info:eu-repo/grantAgreement/EC/FP7/\', aire.projectid) as relation
			FROM J_ARTICLE a
			JOIN J_ISSUE_ARTICLE ai ON ai.article_id = a.article_id
			JOIN J_ISSUES i ON i.issue_id = ai.issue_id
			JOIN J_SECTIONS s ON s.section_id = a.section_id
			LEFT JOIN openAIRE_articles airea on (a.article_id = airea.article_id)
			LEFT JOIN OpenAIRE aire on (airea.openaire_id = aire.id)
			LEFT JOIN J_JOURNALS j ON j.journal_id = i.journal_id
			LEFT JOIN J_GALLEYS g ON g.article_id = a.article_id AND g.label = \'PDF\'
			WHERE ' . getPublishedIssueWhere('i') . ' ' . $this->m_additionalWhereClause . '  
			 
		';
		
		
		if($this->m_from) {
			$this->m_pubdata['sqlstr'] .= ' AND i.date_published >= CAST(\'' . q($this->m_from) . '\' as date) ';
		}
		
		if($this->m_until) {
			$this->m_pubdata['sqlstr'] .= ' AND i.date_published <= CAST(\'' . q($this->m_until) . '\' as date) ';
		}
		
		if($this->m_set) {//Ako e podaden set - filtrirame po izdanie
			if ($this->m_set == SET_OPENAIRE_NAME) 
				$this->m_pubdata['sqlstr'] .= ' AND aire.id IS NOT NULL';
			else
			
				$this->m_pubdata['sqlstr'] .= ' AND j.URL_TITLE = \'' . q($this->m_set) . '\'';
		}
		
		//Listvame gi otnachaloto kym kraq za da moje rezultatite po stranicite da ne se promenqt
		$this->m_pubdata['sqlstr'] .= ' ORDER BY i.date_published ASC ';
		//~ var_dump($this->m_pubdata['sqlstr']);
		parent::GetData();
	}
	
	
	
	protected function GetErrorRow(){
		$this->m_pubdata['err_code'] = $this->m_errCode;
		$this->m_pubdata['err_msg'] = $this->m_errMsg;		
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_ERR_TEMPL));
	}
	
	function GetRows() {
		while (!$this->con->Eof()) {
			$this->GetNextRow();			
			if((int)$this->m_pubdata['parse_sets']){
				//Mnojestvata kym koito prinadleji statiqta - za sega gi nqma no posle shte gi dobavim
				$lSetSpecs = explode(',', $this->m_pubdata['set_specs']);
				$lSetsInputArr = array();
				foreach ($lSetSpecs as $lCurrentSpec){
					$lSetsInputArr[] = array(						
						'spec' => $lCurrentSpec,
						'metadata_prefix' => $this->m_metadataPrefix
					);
				}
				
				$lSetsObject = new crs_display_array(array(
					'templs' => $this->m_pubdata['sets_templs'],
					'templadd' => 'metadata_prefix',
					'input_arr' => $lSetsInputArr,
				));
				
				$this->m_pubdata['sets'] = $lSetsObject->Display();
			}
			
			if((int)$this->m_pubdata['parse_authors']){
				//Vzimane na avtorite na statiqta
				/*
				 * Tuk shemata e slednata - Id-tata na avtorite sa v poleto reg_co_authors, razdeleni s |
				 * Otdelno imame i id na glavniq avtor koeto e v poleto author_id
				 * Syotvetno na vsqko id syotvetstva zapis v tablicata CLIENTS
				 * Nie trqbva da gi vzemem ot tam
				 */
				//Mahame nachalniq i krainiq |
				$lAuthorsStr = mb_substr($this->m_pubdata['reg_co_authors'], 1, -1);
				
				$lAuthorIds = explode('|', $lAuthorsStr);				
				//Dobavqme i glavniq avtor
				$lAuthorIds[] = (int)$this->m_pubdata['author_id'];
				$lAuthorIds = array_map(parseToInt, $lAuthorIds);//Za vseki sluchai gi parsvame kym int
				$lAuthorsObject = new crs(array(
					'templadd' => 'metadata_prefix',
					'templs' => $this->m_pubdata['authors_templs'],
					'sqlstr' => 'SELECT *, \'' . q($this->m_metadataPrefix) . '\' as metadata_prefix FROM CLIENTS WHERE CID IN( ' . implode(',', $lAuthorIds) . ')',					
				));
				
				$this->m_pubdata['authors'] = $lAuthorsObject->Display();
			}
			
			if((int)$this->m_pubdata['parse_keywords']){
				//Kliuchovite dumi na statiqta - stoqt v poleto keywords, razdeleni sys zapetaika
				$lKeywords = explode(';', $this->m_pubdata['keywords']);
				$lKeywordsInputArr = array();
				foreach ($lKeywords as $lCurrentKeyword){
					$lKeywordsInputArr[] = array(
						'name' => trim($lCurrentKeyword),
						'metadata_prefix' => $this->m_metadataPrefix
					);
				}
				$lKeywordsObject = new crs_display_array(array(
					'templs' => $this->m_pubdata['keywords_templs'],
					'input_arr' => $lKeywordsInputArr,
					'templadd' => 'metadata_prefix',
				));
				
				$this->m_pubdata['keywords'] = $lKeywordsObject->Display();
			}
			
			
			if ($this->m_pubdata['templadd']){
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL , $this->m_currentRecord[$this->m_pubdata['templadd']]));
// 				var_dump($this->m_currentRecord);
			}else{ 
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
			}	
			$this->m_pubdata['rownum']++;			
		}
		return $lRet;
	}
	
	function Display() {
		$this->GetData();		
		//Ако няма грешка - гледаме дали поддържаме някакви set-ове, и ако не е така - гърмим с грешка
		if( !$this->m_hasError && !$this->m_recordCount){
			$this->setErr('noRecordsMatch', getstr('oai.noRecordsMatch'));
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
		$lResult = 'set=' . $this->m_set;
		$lResult .= '&from=' . $this->m_from;
		$lResult .= '&until=' . $this->m_until;
		$lResult .= '&metadata_prefix=' . $this->m_metadataPrefix;
		$lResult .= '&page=' . $pPage;
		$lResult = base64_encode($lResult);
		if((int)$pUrlEncode)
			$lResult = urlencode($lResult);
		return $lResult;
	}
	

}