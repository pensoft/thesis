<?php

class cJournals_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		//~ var_dump(unserialize($_SESSION['suser']));
		
		$lShowLoginWarning = (int)$this->GetValueFromRequestWithoutChecks('show_login_warning');
		$lRedirUrl = $this->GetValueFromRequestWithoutChecks('redirurl');
		$lDocumentId = (int)$this->GetValueFromRequestWithoutChecks('document_id');
		$lViewRole = (int)$this->GetValueFromRequestWithoutChecks('view_role');
		$lAutologinHash = $this->GetValueFromRequestWithoutChecks('u_autolog_hash');
		
		if($lJournalId){
			// echo '/login.php?redirurl=' . urlencode($lRedirUrl) . '&force_logout=1&u_autolog_hash=' . $lAutologinHash;
		// exit;
			$this->m_models['mJournal_Documents_Model'] = new mJournal_Documents_Model();
			$lViewPageObjectsDataArray['contents'] = array(
				'ctype' => 'evList_Display',
				'name_in_viewobject' => 'journal_page',
				'controller_data' => $this->m_models['mJournal_Documents_Model']->GetJournalHomeArticles($lJournalId),
				'show_login_warning' => (int)$lShowLoginWarning,
				'login_warning_text' => q(getstr('pjs.login_warning_text')),
				'redirurl' => '/login.php?redirurl=' . urlencode($lRedirUrl) . '&force_logout=1&u_autolog_hash=' . $lAutologinHash . '&document_id=' . $lDocumentId . '&view_role=' . $lViewRole,
			);
			
			$lViewPageObjectsDataArray['journal_features'] = array(
				'ctype' => 'evList_Display',
				'name_in_viewobject' => 'journal_features_templates',
				'controller_data' => $this->m_models['mBrowse_Model']->GetJournalFeatures($lJournalId),
				'journal_id' => $lJournalId,
			);
			$lViewPageObjectsDataArray['pagetitle'] = 'Biodiversity Data Journal';
			$lViewPageObjectsDataArray['journal_id'] = $lJournalId;
			
			$this->AddJournalObjects($lJournalId);
			
			$this->m_pageView = new pSingle_Journal_Page_View(array_merge($this->m_commonObjectsDefinitions, $lViewPageObjectsDataArray));
		}else{
			$this->m_models['mJournal'] = new mJournal();
			
			$lViewPageObjectsDataArray['contents'] = array(
				'ctype' => 'evList_Display',
				'name_in_viewobject' => 'journal_list',
				'controller_data' => $this->m_models['mJournal']->GetJournals(),
			);
			
			$this->m_pageView = new pJournal_Page_View(array_merge($this->m_commonObjectsDefinitions, $lViewPageObjectsDataArray));
		}
	}
	function head_JS_files(){
		return array(	'js/jquery', 
						'js/jquery_ui', 
						'js/jquery.tinyscrollbar.min',
						'js/jquery.dynatree.min', 
						'js/jquery.simplemodal', 
						'js/jquery_form', 
						'js/jquery.tokeninput', 
						'js/jquery.dragsort', 
						'js/ajaxupload.3.5', 
						'js/def', 
						//'ckeditor/ckeditor', 
						//'ckeditor/adapters/jquery', 
						);
	}

}

?>