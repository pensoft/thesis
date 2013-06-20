<?php
/**
 * Този клас отговаря за заявките от тип VERB_GET_LIST_RECORDS.
 * За повече информация относно функционалността - http://www.openarchives.org/OAI/openarchivesprotocol.html#ListRecords
 *
 */
class coai_single_record extends coai_records {	
	var $m_identifier;
	
	function __construct($pFieldTempl){		
		parent::__construct($pFieldTempl);
		
		$this->m_identifier = $this->m_pubdata['identifier'];
		
		$this->m_pubdata['identifier_label'] = IDENTIFIER_LABEL;//Labela za parametyra vyv verb xml-a
		
		if( !$this->m_identifier ){
			$this->setErr('badArgument', getstr('oai.missingIdentifierParam'));
		}

		//Тук имаме само 1 страница
		$this->m_page = 0;
		
		//За всеки случай махаме другите параметри
		$this->m_until = '';
		$this->m_from = '';
		$this->m_set = '';
		$this->m_resumptionToken = '';
		
		//Добавяме филтъра за ид-то
		$this->m_additionalWhereClause = getArticleIdentifierWhere('a', $this->m_identifier, 1);
	}
	
	protected function setErr($pErrCode, $pErrMsg){
		$this->m_errCode = $pErrCode;
		$this->m_errMsg = $pErrMsg;
		$this->m_hasError =  1;
	}	
	
	
	function Display() {
		$this->GetData();		
		//Ако няма грешка - гледаме дали има резултат. Ако няма - грешка
		if( !$this->m_hasError && !$this->m_recordCount){
			$this->setErr('idDoesNotExist', getstr('oai.idDoesNotExist'));
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