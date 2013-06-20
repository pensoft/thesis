<?php

class cProfile_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();

		if(!(int)$this->GetUserId())
			header('Location: /index.php');

		$lUserModel = new mUser_Model();
		$pViewPageObjectsDataArray['pagetitle'] = getstr('pjs.pensoft_account');
		$pViewPageObjectsDataArray['title.suffix'] = getstr('pjs.account.overview');
		$pViewPageObjectsDataArray['content'] = array(
			'ctype' => 'evSimple_Block_Display',
			'name_in_viewobject' => 'profile_page_content',
			'controller_data' => $lUserModel->GetProfileInformation($this->GetUserId()),
		);

		$this->m_pageView = new pProfile_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}

}

?>