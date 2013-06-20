<?php

/*
	Base controller for searching. 
	
	By default this controllers expects to find mSearc() model, with GetData() function. Projects mSearc() model should extend base emSearch() model.
	Also by default this controller is using pSearch model. If you use the controller with the base evList_Display() view, in pSearch should be templates for it.
	Note the two member variables which specify the view object key in the templates file /m_viewPageMetadataKey/ and name of the view object in the page template file /m_viewPageTemplateKey/
	
	Controller is passing params to the model in this function, using own method getModelParams() : 
		$this->m_models['searchResultsModel']->GetData( $this->getModelParams() ),
	
	Controller is configured by predefining initMembers() function. 
	Model params and their format can be set by predefining getModelParams() function.
*/

class ecSearch extends ecBase_Controller {

	var $m_viewPageTemplateKey ; // placeholder name in page template
	var $m_viewPageMetadataKey ; // key name in page templates 
	var $m_baseObjectsDefinitions ; // the same as m_commonObjectsDefinitions
	var $m_useDefaultViewObj ; 
	var $m_useDefaultPageView ;

	function __construct() {
		global $rewrite;
		parent::__construct();
		
		/*
			Note this model "mSearch" must extend emSearch base class
		*/
		$this->m_models['searchResultsModel'] = new mSearch();
		
		$this->initMembers();
		
		if( $this->m_useDefaultViewObj ) {
			$this->m_baseObjectsDefinitions[$this->m_viewPageTemplateKey] = array(
				'ctype' => 'evList_Display',
				'name_in_viewobject' => $this->m_viewPageMetadataKey, // name in page view object 
				'controller_data' => $this->m_models['searchResultsModel']->GetData( $this->getModelParams() ),
				
				'page_parameter_name' => 'p',
				'maxpages' => 10,
				'usefirstlast' => true,
				'groupstep' => 5,
			);
		
			$this->m_baseObjectsDefinitions[$this->m_viewPageTemplateKey] = new evList_Display($this->m_baseObjectsDefinitions[$this->m_viewPageTemplateKey]);
			$this->m_baseObjectsDefinitions[$this->m_viewPageTemplateKey]->setPageSizeAndPageNum(10, $this->GetValueFromRequestWithoutChecks('p'));
			
		}
		
		if( $this->m_useDefaultPageView ) {
			if( !empty($this->m_commonObjectsDefinitions) )
				$this->m_pageView = new pSearch( array_merge($this->m_commonObjectsDefinitions, $this->m_baseObjectsDefinitions) );
			else
				$this->m_pageView = new pSearch( $this->m_baseObjectsDefinitions );
		}
	}
	
	function initMembers(){
		$this->m_viewPageTemplateKey = 'content';
		$this->m_viewPageMetadataKey = 'csearch';
		$this->m_baseObjectsDefinitions = array(); // additional object definitions here 
		$this->m_useDefaultViewObj = true ; 
		$this->m_useDefaultPageView  = true ;
	}
	
	/*
		Extend this to pass arguments to mSearch model 
	*/
	function getModelParams(){
		return array();
	}


	function Display(){
		return $this->m_pageView->Display();
	}




}

?>