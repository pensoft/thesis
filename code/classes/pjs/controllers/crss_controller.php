<?php

class cRSS_Controller extends cBase_Controller {
	var $m_Categories_Controller;
	
	function __construct() {
		global $rewrite;
		parent::__construct();
		
		$lRssModel = new mRSS_Model();
		$pViewPageObjectsDataArray = array();
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		
		$lJournalArticlesData = $lRssModel->GetRSSJournalArticles($lJournalId, RSS_LIMIT);
		$lJournalData = $lRssModel->GetJournalInfo($lJournalId);
		
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'rss_list_templs',
			'controller_data' => $lJournalArticlesData,
			'article_cnt' => RSS_LIMIT,
			'journal_short_name' => $lJournalData['short_name'],
			'journal_name' => $lJournalData['name'],
		);		

		$this->m_pageView = new pRSS_Page_View($pViewPageObjectsDataArray);
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>