<?php

class editEmailTemplateForm_Wrapper extends eForm_Wrapper{

	var $m_pageControllerInstance;
	var $m_DefaultSubject;
	var $m_DefaultBody;

	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);
	}

	protected function PreActionProcessing(){
		$this->m_DefaultSubject = $this->m_formController->GetFieldValue('default_subject');
		$this->m_DefaultBody = $this->m_formController->GetFieldValue('default_body');
		
		// Ако и двата чекбокс-а са със стойност 1 редиректваме без да изпълняваме никакъв action
		if($this->m_formController->GetCurrentAction() == 'save'){
			if (($this->m_DefaultSubject == 1) && ($this->m_DefaultBody == 1)){
				header('Location: /email_templates');
				exit;
			}	
		}
	}
	
	protected function PostActionProcessing(){
		if($this->m_formController->GetCurrentAction() == 'save'){
			if ($this->m_formModel->m_con->mErrMsg)
				$this->m_formController->SetError($this->m_currentAction . '<div class="errstr">'.$this->m_formModel->m_con->mErrMsg.'</div>');
		}
	}
}

?>