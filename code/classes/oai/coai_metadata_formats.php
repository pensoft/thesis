<?php
/**
 * Този клас отговаря за заявките от ит VERB_GET_LIST_METADATA_FORMATS.
 * Тъй като той връша почти само статична информация - той наследява csimple
 * За повече информация относно функционалността - http://www.openarchives.org/OAI/openarchivesprotocol.html#ListMetadataFormats
 *
 */
define('ITEM_MODE', 1);//Listvame informaciq za edin element
define('GLOBAL_MODE', 2);//Listvame globalna informaciq
class coai_metadata_formats extends crs {
	var $m_identifier;
	var $m_hasError;
	var $m_errCode;
	var $m_errMsg;
	var $m_mode;
	var $m_allowedFormats;
	
	function __construct($pFieldTempl){		
		parent::__construct($pFieldTempl);
		$this->m_hasError = 0;
		$this->m_errCode = '';
		$this->m_errMsg = '';
		
		$this->m_mode = GLOBAL_MODE;
		
		$this->m_identifier = $this->m_pubdata['identifier'];
		$this->m_pubdata['identifier_label'] = IDENTIFIER_LABEL;//Labela za parametyra vyv verb xml-a	
		if( $this->m_identifier ){
			$this->m_mode = ITEM_MODE;
		}

		$this->m_allowedFormats = array();
	}
	
	protected function setErr($pErrCode, $pErrMsg){
		$this->m_errCode = $pErrCode;
		$this->m_errMsg = $pErrMsg;
		$this->m_hasError =  1;
	}
	
	function GetData() {
		parent::GetData();
		
		switch($this->m_mode){
			case ITEM_MODE:{
				$this->GetItemData();
				return;
			}
			case GLOBAL_MODE:{
				$this->GetGlobalData();
				return;
			}
		}
	}
	
	/**
	 * Взима форматите за дадена статия. Първо гледаме дали я има статията и 
	 * ако я има тогава връщаме резултатите. В противен случай - гърмим с грешка
	 * 
	 */
	function GetItemData(){
		global $gAllowedMetadataFormats;
		
		$lCon = new DBCn();
		$lCon->Open();
		$lSql = 'SELECT a.article_id  
			FROM J_ARTICLE a
			JOIN J_ISSUE_ARTICLE ai ON ai.article_id = a.article_id
			JOIN J_ISSUES i ON i.issue_id = ai.issue_id
			WHERE ' . getPublishedIssueWhere('i') . ' 
			' . getArticleIdentifierWhere('a', $this->m_identifier, true) . ' 
			LIMIT 1';
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		
		if( $lCon->Eof() || !$lCon->mRs['article_id']){//Ако няма такaва статия, или тази статия не е публикувана -  вдигаме грешка			
			$this->setErr('idDoesNotExist', getstr('oai.idDoesNotExist'));			
			return;
		}
		$this->m_allowedFormats = $gAllowedMetadataFormats;
	}
	
	/**
	 * Връща форматите, които поддържаме за всички статии
	 */
	function GetGlobalData(){
		global $gAllowedMetadataFormats;
		$this->m_allowedFormats = $gAllowedMetadataFormats;
	}
	
	protected function GetErrorRow(){
		$this->m_pubdata['err_code'] = $this->m_errCode;
		$this->m_pubdata['err_msg'] = $this->m_errMsg;		
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_ERR_TEMPL));
	}
	
	function GetRows(){
		$lResult = '';
		foreach ($this->m_allowedFormats as $lFormatPrefix => $lFormatData){
			$this->m_pubdata['prefix'] = $lFormatPrefix;
			$this->m_pubdata['schema'] = $lFormatData['schema'];
			$this->m_pubdata['namespace'] = $lFormatData['namespace'];
			switch($this->m_mode){
				case ITEM_MODE:{
					$lResult .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ITEM_ROW_TEMPL));
					break;
				}
				case GLOBAL_MODE:{
					$lResult .= $this->ReplaceHtmlFields($this->getObjTemplate(G_GLOBAL_ROW_TEMPL));
					break;
				}
			}
		}
		return $lResult;
	}
	
	function Display() {
		$this->GetData();		
		//Ако няма грешка - гледаме дали поддържаме някакви формати, и ако не е така - гърмим с грешка
		if( !$this->m_hasError && 
			(!is_array($this->m_allowedFormats) || !count($this->m_allowedFormats))
		){
			$this->setErr('noMetadataFormats', getstr('oai.noMetadataFormats'));
		}
				
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if ($this->m_hasError) {
			$lRet .= $this->GetErrorRow();			
		} else {			
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $this->GetRows();			
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
		
		return $lRet;
	}
	
	
	

}