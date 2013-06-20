<?php

class viewVersion_Form_Wrapper extends eForm_Wrapper{

	var $m_pageControllerInstance;
	var $m_versionUID;
	var $m_roundID;

	function __construct($pData){
		$this->m_pageControllerInstance = $pData['page_controller_instance'];
		$this->m_versionUID = (int)$pData['version_uid'];
		$this->m_roundID = (int)$pData['round_id'];
		parent::__construct($pData);
	}

	protected function PreActionProcessing(){

		if(
			($this->m_pageControllerInstance->m_viewingRole == DEDICATED_REVIEWER_ROLE ||
			$this->m_pageControllerInstance->m_reviewer_role == COMMUNITY_REVIEWER_ROLE) &&
			is_null($this->m_formController->GetFieldValue('disclose_name'))
		) {
			$this->m_formController->SetFieldValue('disclose_name', 0);
		}

		if($this->m_pageControllerInstance->m_viewingRole == CE_ROLE) {
			$this->m_formController->SetFieldProp('decision_id', 'CType', 'hidden');
			$this->m_formController->SetFieldProp('decision_id', 'DefValue', ROUND_DECISION_ACCEPT);
		}

		if(
			$this->m_formController->GetCurrentAction() == 'review' &&
			(
				$this->m_pageControllerInstance->m_viewingRole == DEDICATED_REVIEWER_ROLE ||
				$this->m_pageControllerInstance->m_viewingRole == COMMUNITY_REVIEWER_ROLE ||
				$this->m_pageControllerInstance->m_viewingRole == SE_ROLE
			)
		) {
			$this->m_formController->SetFieldProp('decision_id', 'AllowNulls', false);

			// This is only for Community reviewer
			$this->m_formController->SetFieldProp('notes_to_editor', 'AllowNulls', true);
			if($this->m_pageControllerInstance->m_reviewer_role != COMMUNITY_REVIEWER_ROLE) {
				$this->m_formController->SetFieldProp('notes_to_author', 'AllowNulls', false);
			} else {
				$this->m_formController->SetFieldProp('notes_to_author', 'AllowNulls', true);
				//~ $this->m_formController->SetFieldProp('notes_to_editor', 'AllowNulls', true);
			}
			if(
				$this->m_pageControllerInstance->m_viewingRole == DEDICATED_REVIEWER_ROLE &&
				$this->m_pageControllerInstance->m_reviewer_role != COMMUNITY_REVIEWER_ROLE
			) {
				//~ $this->m_formController->SetFieldProp('notes_to_editor', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('notes_to_editor', 'AllowNulls', true);
				$this->m_formController->SetFieldProp('question1', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question2', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question3', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question4', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question5', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question6', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question7', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question8', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question9', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question10', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question11', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question12', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question13', 'AllowNulls', false);
				$this->m_formController->SetFieldProp('question14', 'AllowNulls', false);
			}
		}

	}

	protected function PostActionProcessing(){

		if($this->m_formController->GetCurrentAction() == 'review'){
			if(!$this->m_formController->GetErrorCount()) {
					$lUrl = SITE_URL . 'lib/ajax_srv/document_srv.php';
					$lAction = str_replace('\'', '', $this->m_pageControllerInstance->m_submitAction);
					$lDecision = (int)$this->m_formController->GetFieldValue('decision_id');

					if($this->m_pageControllerInstance->m_viewingRole == JOURNAL_EDITOR_ROLE || $this->m_pageControllerInstance->m_viewingRole == SE_ROLE) {
						if($lDecision == 2 || $lDecision == 5) {
							$lAction = 'save_serej_decision';
						}
					}

					$lPostParams = array(
						'round_user_id' => (int)$this->m_pageControllerInstance->m_round_user_id,
						'decision_id' => $lDecision,
						'document_id' => (int)$this->m_pageControllerInstance->m_documentId,
						'uid' => $this->m_pageControllerInstance->m_reviewer_uid,
						'action' => $lAction,
					);

					$lResult = executeExternalQuery($lUrl, $lPostParams, NULL, NULL, TRUE);
					$lResult = json_decode($lResult);

					/*
					 * creating cash version with changes for reviewer
					 *
					 * */
					if(
						$lResult &&
						$this->m_versionUID &&
						$this->m_roundID &&
						in_array($this->m_pageControllerInstance->m_reviewer_role, array(DEDICATED_REVIEWER_ROLE, PUBLIC_REVIEWER_ROLE, COMMUNITY_REVIEWER_ROLE))
					) {
						$lVersionModel = new mVersions();
// 						$lVersionModel->CreatePwtReviewerVersionWithChanges($this->m_roundID, $this->m_versionUID);
					}

					$this->m_formController->SetFieldValue('close', 1);
					$this->m_formController->SetFieldValue('url_params', $lResult->url_params);
			} else {
				if(
					count($this->m_formController->GetFieldErrorsArr('question2')) ||
					count($this->m_formController->GetFieldErrorsArr('question3')) ||
					count($this->m_formController->GetFieldErrorsArr('question4')) ||
					count($this->m_formController->GetFieldErrorsArr('question5')) ||
					count($this->m_formController->GetFieldErrorsArr('question6')) ||
					count($this->m_formController->GetFieldErrorsArr('question7')) ||
					count($this->m_formController->GetFieldErrorsArr('question8')) ||
					count($this->m_formController->GetFieldErrorsArr('question9')) ||
					count($this->m_formController->GetFieldErrorsArr('question10')) ||
					count($this->m_formController->GetFieldErrorsArr('question11')) ||
					count($this->m_formController->GetFieldErrorsArr('question12')) ||
					count($this->m_formController->GetFieldErrorsArr('question13')) ||
					count($this->m_formController->GetFieldErrorsArr('question14'))

				) {
					if(!count($this->m_formController->GetFieldErrorsArr('question1'))) {
						$this->m_formController->SetError('Empty Field', 'question1');
					}
				}
			}
		}
	}
}
?>