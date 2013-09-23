<?php

class Email_Notifications_Form_Wrapper extends eForm_Wrapper{

	var $m_pageControllerInstance;

	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);
	}

	protected function PreActionProcessing(){}

	protected function PostActionProcessing(){
		if($this->m_formController->GetCurrentAction() == 'send'){
			$this->m_formController->SetFieldValue('success', 0);
			if ($this->m_formModel->m_con->mErrMsg) {
				$this->m_formController->SetError($this->m_currentAction . '<div class="errstr">'.$this->m_formModel->m_con->mErrMsg.'</div>');
			} else {
				if(
					$this->m_formController->GetCurrentAction() == 'send' && 
					!$this->m_formController->GetErrorCount() &&
					(int)$this->m_formController->GetFieldValue('event_id')
				){
					$lRecipients = explode(',', $this->m_formController->GetFieldValue('recipients'));
					if(count($lRecipients)){
						/**
						 * Manage event task (submitting new document)
						 */
						$lTaskObj = new cTask_Manager(array(
							'custom_flag' => 1,
							'recipients' => $lRecipients,
							'template' => $this->m_formController->GetFieldValue('template_body'),
							'subject' => $this->m_formController->GetFieldValue('subject'),
							'replace_sql' => 'SELECT * FROM usr WHERE id = {uid}',
							'event_id' => (int)$this->m_formController->GetFieldValue('event_id'),
						));
						$lTaskObj->Display();
						$this->m_formController->SetFieldValue('success', 1);
					}
				}
				
			}
		}
	}
}

?>