<?php

class Browse_Articles_Form_Wrapper extends eForm_Wrapper{
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
		if(!$this->CheckDate($this->m_formController->GetFieldValue('from_date'))){
			$this->m_formController->SetFieldValue('from_date', '');
		}
		if(!$this->CheckDate($this->m_formController->GetFieldValue('to_date'))){
			$this->m_formController->SetFieldValue('to_date', '');
		}
		
	}
	
	protected function PostActionProcessing(){
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_subject_cats', 'subject_categories', 'subject_selected_vals', $this->m_formController);
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_chronical_cats', 'chronological_categories', 'chronological_selected_vals', $this->m_formController);
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_taxon_cats', 'taxon_categories', 'taxon_selected_vals', $this->m_formController);
		$this->m_pageControllerInstance->m_Categories_Controller->SetTreeSelectedVals('alerts_geographical_cats', 'geographical_categories', 'geographical_selected_vals', $this->m_formController);
	}
	
	function wGetFieldValue($pFiledName){
		return $this->m_formController->GetFieldValue($pFiledName);
	}
	
	function CheckDate($pDate){
		$lDateArr = explode('/', $pDate);
		//var_dump($lDateArr);
		return checkdate($lDateArr[1],$lDateArr[0],$lDateArr[2]);
	}
}

?>