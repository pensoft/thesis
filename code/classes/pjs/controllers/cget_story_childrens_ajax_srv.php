<?php

class cGet_Story_Childrens_Ajax_Srv extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lStoryId   = (int)$this->GetValueFromRequestWithoutChecks('storyid');
	
		if(!$lJournalId || !$lStoryId){
			header('Location: /index.php');
		}
		
		$pViewPageObjectsDataArray['stories_tree'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'tree_list_templs',
			'controller_data' => $this->m_models['mBrowse_Model']->GetStoryChildrens($lStoryId, $lJournalId),
			'showmode' => 1,
			'journal_id' => $lJournalId
		);
		
		$lChildrensList = new pStory_Childrens_Page_View($pViewPageObjectsDataArray);
	
		$this->m_pageView = new epPage_Json_View(array('html' => $lChildrensList->Display()));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>