<?php

class cSiteMap_Controller extends cBase_Controller {
	
	var $m_ViewObjects;
	
	function __construct($pFieldTempl) {		
		$this->m_models['msitemap_model'] = new mSiteMap_Model();
		$this->m_ViewObjects['sitemap'] = new evSiteMap_Display(array(
			'controller_data' => $this->m_models['msitemap_model']->GetData(),
		));
		
	}
	
	function Display(){
		return $this->m_ViewObjects['sitemap']->Display();
	}
}

?>