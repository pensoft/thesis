<?php

class cIndex_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();

		$pViewPageObjectsDataArray = array();

		$pViewPageObjectsDataArray['contents'] = array(
			/*'ctype' => 'evStory_Display',
			'name_in_viewobject' => 'index_story',
			'controller_data' => '',//$this->m_models['stories_model']->GetStoryDetails((int)MAIN_STORY_ID),
			'photopref' => array(
				1 => 'sg198_',  // top right
				2 => 'sg198_',  // top left
				3 => 'dx500_',  // bottom pic
				4 => 'dx500_',  // big photo
				10 => 'sg198_',  // Tova za galeriq - golqma snimka
				11 => 'sg198_' // Tova za galeriq - thumbnail
		)*/);

		$this->m_pageView = new pIndex_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));

	}
}

?>