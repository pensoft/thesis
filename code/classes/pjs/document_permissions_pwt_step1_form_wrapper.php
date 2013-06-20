<?php

class Document_Permissions_Pwt_Step1_Form_Wrapper extends eForm_Wrapper{
	protected function PreActionProcessing(){
		if($this->m_formController->GetCurrentAction() == 'save'){
			$lPreparationChecklistValue = $this->m_formController->GetFieldValue('preparation_checklist');
			$lPreparationChecklistMetadata = $this->m_formController->GetFieldMetadata('preparation_checklist');
			if(!is_array($lPreparationChecklistValue) || count($lPreparationChecklistValue) != count($lPreparationChecklistMetadata['SrcValues'])){
				$this->m_formController->SetError(getstr('pjs.documentPermissions.youMustAcceptAllTermsAndConditions'), 'preparation_checklist');
			}
			$lTermsAgreementValue = $this->m_formController->GetFieldValue('terms_agreement');
			if(!is_array($lTermsAgreementValue) || !count($lTermsAgreementValue) ){
				$this->m_formController->SetError(getstr('pjs.documentPermissions.youMustAcceptAllTermsAndConditions'), 'terms_agreement');
			}
		}
	}
}

?>