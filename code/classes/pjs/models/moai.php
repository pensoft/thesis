<?php
define('OAI_DATE_PHP_FORMAT', 'Y-m-d');
define('OAI_DATE_SQL_FORMAT', 'YYYY-MM-DD');
define('OAI_DATE_TEXT_FORMAT', 'YYYY-MM-DD');
class mOai extends emBase_Model {

	function __construct() {
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->m_con->SetFetchReturnType(PGSQL_ASSOC);
	}
	
	/**
	 * Returns the pubdate of the first published article
	 * @return string
	 */
	function GetFirstArticlePubdate(){
		$lCon = $this->m_con;		
		$lResult = date(OAI_DATE_PHP_FORMAT, mktime(0, 0, 0, 1, 1, 1970));
		$lCon = new DBCn();
		$lCon->Open();
		$lSql = 'SELECT to_char(d.publish_date, \'' . q(OAI_DATE_SQL_FORMAT) . '\') as date
			FROM pjs.articles a
			JOIN pjs.documents d ON d.id = a.id
			ORDER BY d.publish_date ASC LIMIT 1';
// 		var_dump($lSql);
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		
		if( !$lCon->Eof()){
			$lResult = $lCon->mRs['date'];
		}
		return $lResult;
	}
	
	function GetSets($pPageNum, $pPageSize){
		$lCon = $this->m_con;		
		$lSql = 'SELECT name, url_name as spec
				FROM journals
				WHERE state = 1
				';
		$lCon->Execute($lSql);
		$lCon->SetPage($pPageSize, $pPageNum);
		$lResultArr = array();
		while(!$lCon->Eof()){
			$lResultArr[] = $lCon->mRs;
			$lCon->MoveNext();
		}
		return new emResults(array(
			'controller_data' => $lResultArr,
			'pagesize' => $lCon->mPageSize,
			'page_num' => $lCon->mPageNum,
			'record_count' => $lCon->RecordCount(),
		));
	}	
	
	protected function GetRecordsBase($pSet, $pFromDate, $pUntilDate, $pIdentifier, $pPageNum, $pPageSize){
		$lCon = $this->m_con;
		$lSql ='
			SELECT d.doi as identifier, to_char(d.publish_date, \'' . q(OAI_DATE_SQL_FORMAT) . '\') as moddate, to_char(d.publish_date, \'' . q(OAI_DATE_SQL_FORMAT) . '\') as pubdate,
					to_char(d.publish_date, \'YYYY\') as pubyear,
			du.uid as author_id, m.keywords, d.name as title, m.abstract as abstract,
			t.name as section_type, d.start_page as start_page, d.end_page as end_page, i.volume as issue_volume,
					j.name as journal_title,
			j.url_name as set_specs, j.url_name as journal_url_title, a.id as article_id,
			i.number as issue_number, 1 as show_issue_number, \'info:eu-repo/grantAgreement/EC/FP7/\' as relation
			FROM pjs.articles a
			JOIN pjs.documents d ON d.id = a.id
			JOIN pjs.article_metadata m ON m.document_id = d.id
			JOIN pjs.document_types t ON t.id = d.document_type_id
			JOIN public.journals j ON j.id = d.journal_id
			JOIN pjs.document_users du ON du.document_id = d.id AND du.role_id = ' . (int)AUTHOR_ROLE . ' AND co_author = 1
			JOIN pjs.journal_issues i ON i.id = issue_id
			WHERE true
		
		';
		// 		var_dump($lSql);
		
		if($pFromDate) {
			$lSql .= ' AND d.publish_date >= \'' . q($pFromDate) . '\'::date ';
		}
		
		if($pUntilDate) {
			$lSql .= ' AND d.publish_date <= \'' . q($pUntilDate) . '\'::date) ';
		}
		
		if($pSet) {//Ako e podaden set - filtrirame po izdanie
			$lSql .= ' AND j.url_name = \'' . q($pSet) . '\'';			
		}
		
		if($pIdentifier) {//Ako e podaden set - filtrirame po izdanie
			$lSql .= ' AND d.doi = \'' . q($pIdentifier) . '\'';
		}
		
		// 		var_dump($pPageSize, $pPageNum);
		
		//Listvame gi otnachaloto kym kraq za da moje rezultatite po stranicite da ne se promenqt
		$lSql .= ' ORDER BY d.publish_date ASC ';
		$lCon->Execute($lSql);
		$lCon->SetPage($pPageSize, $pPageNum);
		$lResultArr = array();
		while(!$lCon->Eof()){
			$lArticleData = $lCon->mRs;
			$lResultArr[] = $lArticleData;
			$lCon->MoveNext();
		}
		$lResult = new emResults(array(
			'controller_data' => array(),
			'pagesize' => $lCon->mPageSize,
			'page_num' => $lCon->mPageNum,
			'record_count' => $lCon->RecordCount(),
		));
		
		foreach ($lResultArr as $lIdx => $lData){
			$lResultArr[$lIdx]['authors'] = $this->GetArticleAuthors($lData['article_id']);
		}
		
		$lResult->m_Data = $lResultArr;
		
		return $lResult;
	}
	
	function GetRecords($pSet, $pFromDate, $pUntilDate, $pPageNum, $pPageSize){
		return $this->GetRecordsBase($pSet, $pFromDate, $pUntilDate, '', $pPageNum, $pPageSize);
	}
	
	function GetSingleRecord($pIdentifier){
		return $this->GetRecordsBase('', '', '', $pIdentifier, 0, 0);
	}
	
	function GetArticleAuthors($pArticleId){
		$lSql = '
				SELECT u.*
				FROM pjs.document_users du
				JOIN public.usr u ON u.id = du.uid
				WHERE du.document_id = ' . (int)$pArticleId . ' AND du.role_id = ' . (int)AUTHOR_ROLE . ' 
		';
		return $this->ArrayOfRows($lSql);
	}
	
	function GetMetadataFormats(){
		return array(
			array(
				'schema' => 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
				'namespace' => 'http://www.openarchives.org/OAI/2.0/oai_dc/',
				'prefix' => 'oai_dc',
			),
			array(
				'schema' => 'http://www.loc.gov/standards/mods/v3/mods-3-1.xsd',
				'namespace' => 'http://www.loc.gov/mods/v3',
				'prefix' => 'mods',
			),
		);
	}
	
	function CheckIfMetadataPrefixIsAllowed($pMetadataPrefix){
		if(in_array($pMetadataPrefix, array('oai_dc', 'mods'))){
			return true;
		}
		return false;
	}
}

?>