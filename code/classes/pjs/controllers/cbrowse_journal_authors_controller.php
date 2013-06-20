<?php

class cBrowse_Journal_Authors_Controller extends cBase_Controller {

	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		
		$lJournalId    = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lAuthorLetter = $this->GetValueFromRequestWithoutChecks('author_letter');
		$lAffiliation  = $this->GetValueFromRequestWithoutChecks('affiliation');

		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId)){
			header('Location: /index.php');
		}
		
		$this->m_models['mJournal_Authors_Model'] = new mJournal_Authors_Model();
		
		$lControllerData =  $this->m_models['mJournal_Authors_Model']->GetJournalAuthors($lJournalId, $lAuthorLetter, $lAffiliation, (int)$this->GetValueFromRequestWithoutChecks('p'));
		//~ var_dump($lControllerData);
		//~ exit;
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'browse_journal_authors_list_templs',
			'controller_data' => $lControllerData,
			'journal_id' => $lJournalId,
			'author_letter' => $lAuthorLetter,
			'byaffiliation' => $lAffiliation,
			'default_page_size' => DEFAULT_PAGE_SIZE,
			'page_parameter_name' => 'p',
		);
		
		$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		
		$pViewPageObjectsDataArray['leftcol'] = new evSimple_Block_Display(array(
			'name_in_viewobject' => 'leftcol',
			'journal_id' => $lJournalId,
			'author_letter' => $lAuthorLetter,
			'affiliation' => $lAffiliation
		));
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pBrowse_Journal_Authors_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>