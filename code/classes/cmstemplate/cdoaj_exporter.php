<?php
/**
	Този клас генерира експорта за DOAAJ. 
	За повече информация относно този експорт да се види https://projects.etaligent.net/issues/610
*/
class cdoaj_exporter extends crs_cms{
	//В експорта влизат само статии с дата на публикуване след тази дата
	var $m_date;
	//В експорта влизат статии само от това списание
	var $m_journal_id;
	var $m_export;
	var $m_err_count;
	var $m_err_msg;
	var $m_dont_get_data;
	function __construct($pFieldTempl) {			
		parent::__construct($pFieldTempl);
		$this->m_date = $pFieldTempl['date'];
		$this->m_journal_id = $pFieldTempl['journal_id'];
		$this->m_err_count = 0;
		$this->m_err_msg = '';
		$this->m_dont_get_data = false;
	}
	
	function GetData() {
		if($this->m_dont_get_data)
			return;
		$this->m_dont_get_data = true;
		if(manageckdate($this->m_date, DATE_TYPE_DATETIME, 0) == false){//Ако датата е грешна
			$this->SetError(getstr('global.wrong_date'));
			return;
		}
		if(!$this->m_journal_id){
			$this->SetError(getstr('global.journal_id_is_required'));
			return;
		}
		
		$lSql = 'SELECT a.doi as identifier, DATE_FORMAT(a.date_published, ' . DATE_SQL_FORMAT . ') as moddate, DATE_FORMAT(a.date_published, ' . DATE_SQL_FORMAT . ') as pubdate, DATE_FORMAT(a.date_published, \'%Y\') as pubyear,
			a.author_id, a.reg_co_authors, a.keywords, a.title, a.abstract as abstract, 
			s.title as section_type, a.start_page as start_page, a.end_page as end_page, i.volume as issue_volume, j.title as journal_title,
			j.URL_TITLE as set_specs, j.URL_TITLE as journal_url_title, a.article_id,
			j.isbn_print, j.isbn_online
			FROM J_ARTICLE a
			JOIN J_ISSUE_ARTICLE ai ON ai.article_id = a.article_id
			JOIN J_ISSUES i ON i.issue_id = ai.issue_id
			JOIN J_SECTIONS s ON s.section_id = a.section_id
			JOIN J_JOURNALS j ON j.journal_id = i.journal_id
			WHERE j.journal_id = ' . $this->m_journal_id . ' AND a.date_published >= STR_TO_DATE(\'' . q($this->m_date) . '\', \'%d/%m/%Y %H:%i\')						
			';
		//~ var_dump($lSql);
		$this->m_pubdata['sqlstr'] = $lSql;
		parent::GetData();
	}
	
	function SetError($pErrMsg){
		$this->m_err_count++;
		$this->m_err_msg .= $pErrMsg;
	}
	
	protected function GetErrorRow(){
		$this->m_pubdata['err_msg'] = $this->m_err_msg;		
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_ERR_TEMPL));
	}
	
	function GetRows() {
		while (!$this->con->Eof()) {
			$this->GetNextRow();			
			
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
				
				
				$lOrdSelect = ', (CASE ';
				$lCount = 1;
				foreach($lAuthorIds as $lCurrentAuthorId){
					$lOrdSelect .= ' WHEN CID = ' . (int)$lCurrentAuthorId . ' THEN ' . $lCount++ . ' ';
				}
				$lOrdSelect .= ' ELSE 0 END ) as custom_order ';
				$lAuthorOrderBy = ' ORDER BY custom_order ASC';
				if(!count($lAuthorIds)){
					$lOrdSelect = '';
					$lAuthorOrderBy = '';
				}
				
				$lAuthorsObject = new crs_cms(array(
					'templs' => $this->m_pubdata['authors_templs'],
					'sqlstr' => 'SELECT * ' . $lOrdSelect . ' FROM CLIENTS WHERE CID IN( ' . implode(',', $lAuthorIds) . ') ' . $lAuthorOrderBy,					
					//~ 'sqlstr' => 'SELECT * FROM CLIENTS WHERE CID IN( ' . implode(',', $lAuthorIds) . ')',					
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
					);
				}
				$lKeywordsObject = new crs_display_array(array(
					'templs' => $this->m_pubdata['keywords_templs'],
					'input_arr' => $lKeywordsInputArr,
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
		
		if ($this->m_err_count) {
			$lRet .= $this->GetErrorRow();			
		}else {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		 
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $this->GetRows();
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
			
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
		}
		
		
		
		return $lRet;
	}
	
}
?>