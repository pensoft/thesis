<?php

class edit_Issue_Form_Wrapper extends eForm_Wrapper{

	var $m_pageControllerInstance;
	var $m_previewpicid;

	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		parent::__construct($pData);

	}

	protected function PreActionProcessing(){
		if($this->m_formController->GetCurrentAction() == 'showedit'){
			$this->m_formController->SetFieldProp('is_active', 'DefValue', 1);
		}
		$this->m_previewpicid = $this->m_formController->GetFieldValue('previewpicid');
	}

	protected function PostActionProcessing(){
		if( !$this->m_formController->GetErrorCount() ){
			if($this->m_formController->GetCurrentAction() == 'save'){
				$lPreviewPicData = $this->m_formController->GetFieldValue('previewpic');
				$lPreviewPicDesc = $this->m_formController->GetFieldValue('cover_caption');
				$lIssueId        = $this->m_formController->GetFieldValue('issue_id');
				if($lPreviewPicData['name']){
					$lUploadResult = $this->m_formController->UploadPhoto('previewpic', '', $lPreviewPicDesc);
					if($lUploadResult['err_cnt']){
						foreach ($lUploadResult['err_msgs'] as $lCurrentError){
							$this->m_formController->SetError($lCurrentError['err_msg'], 'previewpic');
						}
					}else{
						//The pic has been updated correctly - we should update the issue data in the db and delete old image
						$this->m_pageControllerInstance->AddPictureToIssue($lUploadResult['photo_id'], $lIssueId);
					}
				}

				header('Location: /manage_journal_issues.php?journal_id= ' .
					(int)$this->m_pageControllerInstance->m_journalId .
					'&back_issue=' . (int)$this->m_pageControllerInstance->m_backIssue);
				exit;
			}

			if($this->m_formController->GetCurrentAction() == 'delete'){
				$this->m_previewpicid = $this->m_formController->GetFieldValue('previewpicid');
				$lFilesModel = new mFiles_Model();
				$lOperResult = $lFilesModel->DeletePic($this->m_previewpicid);
				if(!(int)$lOperResult['err_cnt'])
					DeletePicFiles($this->m_previewpicid);
				header('Location: /manage_journal_issues.php?journal_id= ' .
					(int)$this->m_pageControllerInstance->m_journalId .
					'&back_issue=' . (int)$this->m_pageControllerInstance->m_backIssue);
				exit;
			}

			if($this->m_formController->GetCurrentAction() == 'changestate'){
				header('Location: /manage_journal_issues.php?journal_id= ' .
					(int)$this->m_pageControllerInstance->m_journalId .
					'&back_issue=' . (int)$this->m_pageControllerInstance->m_backIssue);
			}

			if($this->m_formController->GetCurrentAction() == 'makecurrent'){
				header('Location: /manage_journal_issues.php?journal_id= ' .
					(int)$this->m_pageControllerInstance->m_journalId .
					'&back_issue=' . (int)$this->m_pageControllerInstance->m_backIssue);
			}
		}
	}
}

?>