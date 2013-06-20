<?php
/**
 * Този клас отговаря за заявките от тип VERB_GET_IDENTIFY.
 * Тъй като той връша почти само статична информация - той наследява csimple
 * За повече информация относно функционалността - http://www.openarchives.org/OAI/openarchivesprotocol.html#Identify
 * @author peterg
 *
 */
class coai_identify extends csimple {
	/**
	 * Единственото нединамично нещо което показваме за тези заявки
	 * е най-ранната дата на публикувана статия - нея трябва да я вземем от базата
	 */
	function GetData() {
		//За всеки случай слагаме 1970-01-01
		$this->m_pubdata['min_date'] = date(DATE_PHP_FORMAT, mktime(0, 0, 0, 1, 1, 1970));
		$lCon = new DBCn();
		$lCon->Open();
		$lSql = 'SELECT DATE_FORMAT(i.date_published, ' . DATE_SQL_FORMAT . ') as date  
			FROM J_ARTICLE a
			JOIN J_ISSUE_ARTICLE ai ON ai.article_id = a.article_id
			JOIN J_ISSUES i ON i.issue_id = ai.issue_id
			WHERE ' . getPublishedIssueWhere('i') . ' 
			ORDER BY i.date_published ASC LIMIT 1';
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		
		if( !$lCon->Eof()){			
			$this->m_pubdata['min_date'] = $lCon->mRs['date'];
		}
		
		parent::GetData();
	}

}