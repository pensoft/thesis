<?php

class edit_Review_TypeForm_Wrapper extends eForm_Wrapper{

	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);
	}

	protected function PreActionProcessing(){
		
	}
	
	protected function PostActionProcessing(){
		$lDocumentId = (int)$this->m_formController->GetFieldValue('document_id');
		if($this->m_formController->GetCurrentAction() == 'save'){
			//~ header('Location: /view_document.php?id=' . $lDocumentId . '&view_role=' . JOURNAL_EDITOR_ROLE);
		}
	}
}

?>