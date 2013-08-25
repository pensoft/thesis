<?php
// Disable error reporting because it can break the json output
// ini_set('error_reporting', 'off');

class cArticles extends cBase_Controller {
	var $m_articleId;
	var $m_articlesModel;
	function __construct() {
		parent::__construct();
		$pViewPageObjectsDataArray = array();
		$this->m_articlesModel = new mArticles();
		$this->m_articleId = (int)$this->GetValueFromRequestWithoutChecks('id');
		
		
	
		$lResultArr = array(
				'contents' => array(
					'ctype' => 'evSimple_Block_Display',
					'name_in_viewobject' => 'contents',
					'id' => $this->m_articleId,	
					'contents_list' => $this->m_articlesModel->GetContentsListHtml($this->m_articleId),			
				),
		);
// 		var_dump($lResultArr);
		$this->m_pageView = new pArticles(&$lResultArr);
	}
	
};