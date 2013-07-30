<?php

class TasksPopUpForm_Wrapper extends eForm_Wrapper{
	/**
	 * A reference to the page controller
	 * @var cRegister_Controller
	 */
	var $m_pageControllerInstance;
	
	var $m_taskDetailId;

	var $m_eventIds;
	
	var $m_action;

	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		$this->m_taskDetailId = $pData['task_detail_id'];
		$this->m_eventIds = $pData['event_ids'];
		$this->m_action = $pData['action'];
		parent::__construct($pData);
	}

	protected function PreActionProcessing(){
		if($this->m_action) {
			$this->m_formController->SetCurrentAction($this->m_action);
		}
		
		$this->m_formController->SetFieldValue('event_ids', implode(',', $this->m_eventIds));
		
		if($this->m_taskDetailId) {
			$this->m_formController->SetFieldValue('task_detail_id', $this->m_taskDetailId);
		}
		
	}
	
	protected function PostActionProcessing(){
		
		if($this->m_action == 'sendthis') {
			$lTaskModel = new mTask_model();
			$lTaskModel->SendJustOneEmail((int)$this->m_formController->GetFieldValue('task_detail_id'), $this->m_formController->GetFieldValue('template_notes'));
		}
		
		if($this->m_formController->GetFieldValue('state_id') == TASK_DETAIL_SKIP_STATE_ID) {
			//var_dump($expression)
			foreach ($this->m_formController->m_fieldsMetadata as $key => $value) {
				$this->m_formController->SetFieldProp($key, 'AddTags', array_merge($value['AddTags'], array('disabled' => 'disabled')));
			}
		}
	}
}

?>