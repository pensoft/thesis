<?php

class ExpertisesForm_Wrapper extends eForm_Wrapper{
	/**
	 * A reference to the page controller
	 * @var cRegister_Controller
	 */
	var $m_pageControllerInstance;
	var $m_journalId;
	
	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);

	}

	protected function PreActionProcessing(){
		$this->m_journalId = $this->m_formController->GetFieldValue('journal_id');
	}
	
	protected function PostActionProcessing(){
		if(!$this->m_formController->GetErrorCount() && $this->m_formController->GetCurrentAction() == 'save'){
			echo '<script type="text/javascript">window.parent.location="/manage_journal_users.php?journal_id=' . $this->m_journalId . '";</script>';
			exit;
		}
		
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_subject_cats', 'subject_categories', 'subject_selected_vals', $this->m_formController);
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_taxon_cats', 'taxon_categories', 'taxon_selected_vals', $this->m_formController);
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_geographical_cats', 'geographical_categories', 'geographical_selected_vals', $this->m_formController);
	}
}

?>