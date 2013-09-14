<?php

class cGenerate_XML_Controller extends cBase_Controller {
	var $m_Categories_Controller;
	
	function __construct() {
		global $rewrite;
		parent::__construct();
		
		$lGenerateXmlModel = new mGenerate_XML_Model();
		$pViewPageObjectsDataArray = array();
		$lDocumentId = (int)$this->GetValueFromRequestWithoutChecks('document_id');
		
		$lXml = $lGenerateXmlModel->GetNLMXML($lDocumentId);
		$pViewPageObjectsDataArray['contents'] = $lXml;

		$this->m_pageView = new pGenerate_XML_Page_View($pViewPageObjectsDataArray);
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>