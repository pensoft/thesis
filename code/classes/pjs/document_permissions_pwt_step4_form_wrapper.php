<?php
class Document_Permissions_Pwt_Step4_Form_Wrapper extends eForm_Wrapper{
	protected function PreActionProcessing(){
		if($this->m_formController->GetCurrentAction() == 'save'){
			global $rewrite;
			$lUser = new ecBase_Controller_User_Data();
			$lReviewProcessType = $this->m_formController->GetFieldValue('review_process_type');
			
			//~ print_r($lReviewProcessType);
			$lDocumentId = $this->m_formController->GetFieldValue('document_id');
			if($lReviewProcessType != 1){
				$this->m_formController->SetFieldProp('save', 'RedirUrl', $rewrite->EncodeUrl('/document_bdj_submission.php?document_id=' . $lDocumentId, true));
				$this->m_formController->SetFieldProp('save', 'SQL', 'SELECT * FROM spSaveZookeysDocumentFourthStep(' . $lDocumentId . ', ' . $lReviewProcessType . ', ' . $lUser->GetUserId() . ')');
			} else {
				$this->m_formController->SetFieldProp('save', 'RedirUrl', $rewrite->EncodeUrl('/document_bdj_submission.php?success=1&document_id=' . $lDocumentId, true));
			}
			//~ print_r($lReviewProcessType);
			//~ print_r('SELECT * FROM spSaveZookeysDocumentFourthStep(' . $lDocumentId . ', ARRAY[' . $lReviewProcessType[0] . '], ' . $lUser->GetUserId() . ')');
			//~ exit;
		}
	}
}

?>