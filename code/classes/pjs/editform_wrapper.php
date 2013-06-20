<?php

class editForm_Wrapper extends eForm_Wrapper{

	var $m_pageControllerInstance;
	var $m_story_content;
	var $m_storyId;

	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);

	}

	protected function PreActionProcessing(){
		$this->m_story_content = $this->m_formController->GetFieldValue('description');
		$this->m_storyId = $this->m_formController->GetFieldValue('guid');
	}
	
	protected function PostActionProcessing(){
		if( !$this->m_formController->GetErrorCount() ){
			if($this->m_formController->GetCurrentAction() == 'save'){
				$lContent = $this->m_story_content;
				$lStoryId = $this->m_formController->GetFieldValue('guid');

				$fh = fopen(PATH_STORIES . $lStoryId . '.html', 'w');
				fwrite($fh, parseUrls(parseSpecialQuotes($lContent)));
				fclose($fh);
				header('Location: /manage_journal_about_pages.php?journal_id=' . $this->m_pageControllerInstance->m_journal_id);
				exit;
			}elseif($this->m_formController->GetCurrentAction() == 'delete'){
				$lStoryId = $this->m_storyId;
				unlink(PATH_STORIES . $lStoryId . '.html');
				echo 1;
				exit;
			}elseif($this->m_storyId && $this->m_formController->GetCurrentAction() == 'showedit'){
				$this->m_formController->SetFieldValue('description', $this->m_pageControllerInstance->m_story_content);
			}elseif($this->m_formController->GetCurrentAction() == 'moveupdown'){
				echo 1;
				exit;
			}
		}
	}	
}

?>