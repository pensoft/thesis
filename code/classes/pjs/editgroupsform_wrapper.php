<?php

class editGroupsForm_Wrapper extends eForm_Wrapper{

	var $m_pageControllerInstance;
	var $m_story_content;
	var $m_storyId;

	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);

	}

	protected function PreActionProcessing(){
		
	}
	
	protected function PostActionProcessing(){
		if($this->m_formController->GetCurrentAction() == 'delete'){
			if ($this->m_formModel->m_con->mErrMsg)
				header('Location:'.$_SERVER['HTTP_REFERER'].'&error='.$this->m_formModel->m_con->mErrMsg);
		}
		if($this->m_formController->GetCurrentAction() == 'save'){
			if ($this->m_formModel->m_con->mErrMsg)
				$this->m_formController->SetError($this->m_currentAction . '<div class="errstr">'.$this->m_formModel->m_con->mErrMsg.'</div>');
		}
	}
}

?>