<?php

class Article_Comments_Form_Wrapper extends eForm_Wrapper{

	var $m_pageControllerInstance;
	var $m_versionUID;
	var $m_roundID;
	var $m_poll_questions = array();

	function __construct($pData){
		global $gQuestions;
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);
	}
	
	protected function PreActionProcessing(){
		if($this->m_formController->GetCurrentAction() == 'comment') {
			$this->m_formController->SetFieldProp('message', 'AllowNulls', false);
		}
	}
	
	protected function PostActionProcessing(){
		if(
			$this->m_formController->GetCurrentAction() == 'comment' && 
			!$this->m_formController->GetErrorCount() &&
			(int)$this->m_formController->GetFieldValue('event_id')
		){
			/**
			 * Manage event task (submitting new document)
			 */
			$lTaskObj = new cTask_Manager(array(
				'event_id' => (int)$this->m_formController->GetFieldValue('event_id'),
			));
			$lTaskObj->Display();
		}
	}
}
?>