<?php
// Disable error reporting because it can break the json output
// ini_set('error_reporting', 'off');

class cArticle_Preview extends cBase_Controller {
	var $m_articleId;
	var $m_articlesModel;
	function __construct() {
		parent::__construct();
		$pViewPageObjectsDataArray = array();
		$this->m_articlesModel = new mArticles();
		$this->m_articleId = (int)$this->GetValueFromRequestWithoutChecks('id');
		
		
	
		$lResultArr = array(
				'contents' => $this->m_articlesModel->GetArticleHtml($this->m_articleId),
		);
// 		var_dump($lResultArr);
		$this->m_pageView = new pArticle_Preview(&$lResultArr);
	}
	
};