<?php

class cDocument_Edit_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();

		$pViewPageObjectsDataArray = array();

		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evSimple_Block_Display',
			'name_in_viewobject' => 'document_show',
			'controller_data' => '',//$this->m_models['stories_model']->GetStoryDetails((int)MAIN_STORY_ID),
		);
		$this->m_pageView = new pDocument_Edit_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));

	}
}

?>