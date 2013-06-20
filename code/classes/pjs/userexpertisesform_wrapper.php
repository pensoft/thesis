<?php

class UserExpertisesForm_Wrapper extends eForm_Wrapper{
	/**
	 * A reference to the page controller
	 * @var cRegister_Controller
	 */
	var $m_pageControllerInstance;

	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);
	}

	protected function PreActionProcessing(){
	}
	
	protected function PostActionProcessing(){
		global $user;
		if( !$this->m_formController->GetErrorCount() && $this->m_formController->GetCurrentAction() == 'save'){
			$lUrlToRedirect = array();
			
			$lDocumentsModel = new mDocuments_Model();
			$lRes = array();
			$lRes = $lDocumentsModel->AddRemoveSE(
				(int)$this->m_formController->GetFieldValue('document_id'), 
				(int)$this->m_formController->GetFieldValue('se_uid'), 
				$user->id
			);
			
			// creating tasks
			foreach ($lRes['event_id'] as $key => $value) {
				/**
				 * Manage event task (submitting new document)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$value,
				));
				$lTaskObj->Display();
			}
			
			$lUrlToRedirect['url'] = SITE_URL . '/view_document.php?id=' . $this->m_formController->GetFieldValue('document_id') . '&view_role=' . E_ROLE . '&mode=1&suggested=1' .  '&event_id[]=' . implode("&event_id[]=", $lRes['event_id']) . '&e_redirect=1'; 
			
			echo json_encode($lUrlToRedirect);
			exit;
			
		}
		
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_subject_cats', 'subject_categories', 'subject_selected_vals', $this->m_formController);
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_taxon_cats', 'taxon_categories', 'taxon_selected_vals', $this->m_formController);
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_geographical_cats', 'geographical_categories', 'geographical_selected_vals', $this->m_formController);
		
		if( !$this->m_formController->GetErrorCount() && $this->m_formController->GetCurrentAction() == 'save'){
			// redirect
			
		}
	}
}

?>