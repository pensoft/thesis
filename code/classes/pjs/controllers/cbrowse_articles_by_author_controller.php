<?php

class cBrowse_Articles_By_Author_Controller extends cBase_Controller {
	
	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$pViewPageObjectsDataArray['fullname'] = '';
		$pViewPageObjectsDataArray['affiliation'] = '';
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lAuthorId = (int)$this->GetValueFromRequestWithoutChecks('user_id');
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		/*if(!$lJournalId || 
		   !$this->m_models['mEdit_Model']->CheckUserRight($lAuthorId, $lJournalId, AUTHOR_ROLE)){
			header('Location: /index.php');
		}*/
		   
		if(!$lJournalId){
			header('Location: /index.php');
		}
		
		$this->m_models['mJournal_Documents_Model'] = new mJournal_Documents_Model();
		$this->m_models['mUser_Model'] = new mUser_Model();

		$lJournalArticles = $this->m_models['mJournal_Documents_Model']->GetJournalArticlesByAuthor($lJournalId, (int)$lAuthorId, (int)$this->GetValueFromRequestWithoutChecks('p'));
		
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'browse_articles_list_templs',
			'controller_data' => $lJournalArticles,
			'journal_id' => $lJournalId,
			'default_page_size' => DEFAULT_PAGE_SIZE,
			'page_parameter_name' => 'p',
		);
		
		$lAuthorData = $this->m_models['mUser_Model']->GetUser($lAuthorId);
		
		$pViewPageObjectsDataArray['leftcol'] = new evSimple_Block_Display(array(
			'name_in_viewobject' => 'leftcol',
			'journal_id' => $lJournalId,
			'fullname' => $lAuthorData['fullname'],
			'affiliation' => $lAuthorData['affiliation'],
			'photo_id' => $lAuthorData['photo_id']
		));
		
		$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pBrowse_Articles_By_Author_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>