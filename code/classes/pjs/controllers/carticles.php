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
		$lObjectExistence = $this->m_articlesModel->GetObjectExistenceFields($this->m_articleId);
		$lMetadata = $this->m_articlesModel->GetMetadata($this->m_articleId);
	
		$lResultArr = array(
				'contents' => array(
					'ctype' => 'evSimple_Block_Display',
					'object_existence' => $lObjectExistence,
					'name_in_viewobject' => 'contents',
					'id' => $this->m_articleId,	
					'contents_list' => $this->m_articlesModel->GetContentsListHtml($this->m_articleId),	
					'controller_data' => $lMetadata,		
				),
		);
// 		var_dump($lResultArr);
		$this->m_pageView = new pArticles(&$lResultArr);
	}
	
};