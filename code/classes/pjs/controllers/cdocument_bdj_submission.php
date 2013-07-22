<?php

class cDocument_BDJ_Submission extends cBase_Controller {
	var $m_documentId;
	var $m_documentType;
	var $m_journalId;
	var $m_step;
	var $m_errCnt = 0;
	var $m_errMsgs = array();

	var $m_formNameInViewobject = '';
	var $m_formWrapperClass = 'eForm_Wrapper';
	var $m_formFieldsMetadata = array();
	function __construct() {
		parent::__construct();
		$this->RedirectIfNotLogged();
		$this->InitDocumentInfo();
		$pViewPageObjectsDataArray = array();

		if($this->GetValueFromRequestWithoutChecks('success')){
			$pViewPageObjectsDataArray['contents'] = array(
				'ctype' => 'evSimple_Block_Display',
				'name_in_viewobject' => 'success_msg',
				'document_id' => (int)$this->m_documentId,
				'view_role' => (int)AUTHOR_ROLE,
				'event_id' => (int)$this->GetValueFromRequestWithoutChecks('event_id'),
			);
			
			if((int)$this->GetValueFromRequestWithoutChecks('event_id')) {
				/**
				 * Manage event task (submitting new document)
				 */
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)$this->GetValueFromRequestWithoutChecks('event_id'),
				));
				$lTaskObj->Display();
			}
			
		}else{
			if($this->m_errCnt){
				// If there are errors - we display them
				$pViewPageObjectsDataArray['contents'] = new evList_Display(array(
					'controller_data' => $this->m_errMsgs,
					'name_in_viewobject' => 'create_document_errors'
				));
			}else{
				// If there are no errors - we display the form for the specific
				// step
				$this->GetFormFields();
				$pViewPageObjectsDataArray['contents'] = array(
					'ctype' => $this->m_formWrapperClass,
					'name_in_viewobject' => $this->m_formNameInViewobject,
					'controller_instance' => $this,
					'form_method' => 'get',
					'documentid' => (int)$this->m_documentId,
					'form_name' => 'document_permissions_form',
					'fields_metadata' => $this->m_formFieldsMetadata,
				);
				$pViewPageObjectsDataArray['submission_step_title'] = array(
					'ctype' => 'evSimple_Block_Display',
					'name_in_viewobject' => 'submission_step_title',
					'title' => getstr('pjs.documentCreate.ZookeysSubmissionStep' . (int)$this->m_step)
				);

			}
		}
		
		$this->AddJournalObjects($this->m_journalId);
		
		$this->m_pageView = new pDocument_Creation_BDJ_Submission(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	private function InitDocumentInfo() {
		$lDocumentIdData = $this->GetValueFromRequest('document_id', 'GET', 'int', false, false);
		if($lDocumentIdData['err_cnt']){
			$this->Redirect('/index.php');
		}
		$this->m_documentId = $lDocumentIdData['value'];
		$lModel = new mDocuments_Model();
		$lModelResponse = $lModel->GetDocumentInfo($this->m_documentId);
		if($lModelResponse['err_cnt']){
			$this->m_errCnt = $lModelResponse['err_cnt'];
			$this->m_errMsgs = $lModelResponse['err_msgs'];
			return;
		}
		if(! $lModelResponse['document_id']){
			$this->SetError(getstr('pjs.noSuchDocument'));
			return;
		}
		if($lModelResponse['createuid'] != $this->GetUserId()){
			$this->SetError(getstr('pjs.thisDocumentBelongsToAnotherUser'));
			return;
		}
		if($lModelResponse['state_id'] != (int) DOCUMENT_INCOMPLETE_STATE){
			$this->SetError(getstr('pjs.thisDocumentHasPassedThisSteps'));
			return;
		}
		$this->m_documentType = $lModelResponse['document_source_id'];
		$this->m_journalId = $lModelResponse['journal_id'];
		
		$this->m_step = (int) $this->GetValueFromRequestWithoutChecks('step', 'GET');
		if($this->m_step <= 0 || $this->m_step > $lModelResponse['creation_step']){
			$this->m_step = (int) $lModelResponse['creation_step'];
		}
// 		$this->m_step = 2;

	}

	private function SetError($pErrMsg) {
		$this->m_errCnt ++;
		$this->m_errMsgs[] = array(
			'err_msg' => $pErrMsg
		);
	}

	private function GetFormFields() {
		switch ($this->m_step) {
			default :
			case 1 :
				$this->GetFormFieldsStep1();
				break;
			case 2 :
				$this->GetFormFieldsStep2();
				break;
			case 3 :
				$this->GetFormFieldsStep3();
				$lModel = new mDocuments_Model();
				$lResult = $lModel->GetSubmittingDocumentInfo($this->m_documentId);
				//~ echo $lResult['chronological_categories'];
				$this->m_commonObjectsDefinitions['document_info'] = array(
					'ctype' => 'evSimple_Block_Display',
					'name_in_viewobject' => 'form_step_3_pwt_document_data',
					'name'                     	=> $lResult['name'],
					'authors_names' 				=> $lResult['authors_names'],
					'submitting_author_name' 	=> $lResult['submitting_author_name'],
					'abstract' 					=> $lResult['abstract'],
					'keywords' 					=> $lResult['keywords'],
					'taxon_categories' 			=> $lResult['taxon_categories'],
					'geographical_categories' 	=> $lResult['geographical_categories'],
					'subject_categories' 		=> $lResult['subject_categories'],
					'chronological_categories' 	=> $lResult['chronological_categories'],
					'supporting_agencies' 		=> $lResult['supporting_agencies'],
					'supporting_agencies_txts'	=> $lResult['supporting_agencies_txts'],
				);
				break;
			case 4 :
				$this->GetFormFieldsStep4();
				break;
			case 5 :
				$lModel = new mDocuments_Model();
				$lControllerData = $lModel->GetAssignedReviewersListByDocument($this->m_documentId);
				
				$this->m_commonObjectsDefinitions['document_reviewers'] = array(
					'ctype' => 'evList_Display',
					'name_in_viewobject' => 'document_reviewers',
					'controller_data' => $lControllerData,
					'document_id' => $this->m_documentId,
				);
				$this->GetFormFieldsStep5();
				break;

		}
	}

	private function GetFormFieldsStep1() {
		global $rewrite;
		switch ($this->m_documentType) {
			default :
			case (int) PWT_DOCUMENT_TYPE :
				$this->m_formFieldsMetadata = array(
					'document_id' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'DefValue' => $this->m_documentId
					),
					'step' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'DefValue' => $this->m_step
					),
					'preparation_checklist' => array(
						'CType' => 'checkbox',
						'VType' => 'int',
						'TransType' => MANY_TO_BIT,
						'SrcValues' => array(
							1 => 'The submission has not been previously published, nor is it currently with another journal for consideration, nor will it be submitted in the future to any other journal before the official rejection or withdrawal of the manuscript by this journal (unless an explanation has been provided in Comments to the Editor and a procedure mutually agreed upon).',
							2 => 'The text is checked by a native English speaker and adheres to the stylistic and bibliographic requirements outlined in the <a href="http://biodiversitydatajournal.com/about#Authorguidelines" target="_blank">Author guidelines</a>. Please note that neglecting these requirements may lead to rejection of your manuscript prior to peer review.',
							//~ 4 => 'The submission file is in PDF format with all figures embedded and is no larger than 20 MB. The text is provided as an additional file in DOC, DOCX, RTF or ODF file format with tables included.',
							//~ 8 => 'Original high-resolution files of any figures are uploaded separately as additional files, no larger than 20 MB each.',
							//~ 16 => 'The text adheres to the stylistic and bibliographic requirements outlined in the Author Guidelines and is checked by a native English speaker. Where available, URLs for the references are provided.'
						),
						'DisplayName' => getstr('pjs.documentCreate.preparationChecklist'),
						'AllowNulls' => false,
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'terms_agreement' => array(
						'CType' => 'checkbox',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.documentCreate.termsAgreement'),
						'AllowNulls' => false,
						'AddTags' => array(
							'class' => 'inputFld'
						),
						'TransType' => MANY_TO_BIT,
						'SrcValues' => array(
							1 => 'The authors agree to the terms of the Copyright Notice, which will apply to this submission if and when it is published by this journal (comments to the Editor can be added below).'
						)
					),
					'new'  => array(
						'CType' => 'action',
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
						'SQL' => 'SELECT * FROM spSaveDocumentPermissionsFirstStep(0, ' . $this->m_documentId . ', ' . $this->GetUserId() . ', null, null);',
					),
					'save' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_REDIRECT,
						'SQL' => 'SELECT * FROM spSaveDocumentPermissionsFirstStep(1, ' . $this->m_documentId . ', ' . $this->GetUserId() . ', {preparation_checklist}, {terms_agreement});',
						'CheckCaptcha' => 0,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.next'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_bdj_submission.php?document_id=' . $this->m_documentId, true),
						'AddTags' => array(
							'class' => 'inputBtn'
						)
					)
				);
				$this->m_formNameInViewobject = 'form_step_1_pwt';
				$this->m_formWrapperClass = 'Document_Permissions_Pwt_Step1_Form_Wrapper';
		}

	}

	private function GetFormFieldsStep2() {
		global $rewrite;
		switch ($this->m_documentType) {
			default :
			case (int) PWT_DOCUMENT_TYPE :
				$this->m_formFieldsMetadata = array(
					'document_id' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'DefValue' => $this->m_documentId
					),
					'step' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'DefValue' => $this->m_step
					),
					'comments_to_editor' => array(
						'CType' => 'textarea',
						'VType' => 'string',
						'DisplayName' => getstr('pjs.documentCreate.CommentsToEditor'),
						'AllowNulls' => true,
						'AddTags' => array(
							'class' => 'inputFld',
							'style' => 'height:200px;',
						)
					),
					'new' => array(
						'CType' => 'action',
						'SQL' => 'SELECT notes_to_editor as comments_to_editor FROM pjs.documents WHERE id = ' . (int)$this->m_documentId,
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
					),
					'save' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_REDIRECT,
						'SQL' => 'SELECT * FROM spSaveZookeysDocumentSecondStep(' . $this->m_documentId . ', {comments_to_editor}, ' . $this->GetUserId() . '
						)',
						'CheckCaptcha' => 0,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.next'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_bdj_submission.php?document_id=' . (int)$this->m_documentId, true),
						'AddTags' => array(
							'class' => 'inputBtn'
						)
					),
					'back' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_REDIRECT,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.back'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_bdj_submission.php?step=1&document_id=' . (int)$this->m_documentId, true),
						'AddTags' => array(
							'class' => 'inputBtn'
						)
					)
				);
				$this->m_formNameInViewobject = 'form_step_2_pwt';
				$this->m_formWrapperClass = 'Document_Permissions_Pwt_Step2_Form_Wrapper';
		}
	}
	
	private function GetFormFieldsStep3() {
		global $rewrite;
		switch ($this->m_documentType) {
			default :
			case (int) PWT_DOCUMENT_TYPE :
				$this->m_formFieldsMetadata = array(
					'document_id' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'DefValue' => $this->m_documentId
					),
					'step' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'DefValue' => $this->m_step
					),
					'intended_issue' => array (
						'VType' => 'int' ,
						'CType' => 'select' ,
						'DisplayName' => getstr('pjs.documentCreate.IntendedIssue'),
						'SrcValues' => 'SELECT null as id, \'Regular Issue\' as name 
										UNION
										SELECT id as id, name as name FROM pjs.journal_issues WHERE name <> \'\' AND journal_id = ' . $this->m_journalId . '
										ORDER BY id DESC',
						'AddTags' => array(
							
						),
						'AllowNulls' => true,
					),
					'new' => array(
						'CType' => 'action',
						'SQL' => 'SELECT intended_issue_id as intended_issue FROM pjs.documents WHERE id = ' . (int)$this->m_documentId,
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
					),
					'save' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_REDIRECT,
						'SQL' => 'SELECT * FROM spSaveZookeysDocumentThirdStep(' . $this->m_documentId . ', {intended_issue}, ' . $this->GetUserId() . '
						)',
						'CheckCaptcha' => 0,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.next'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_bdj_submission.php?document_id=' . $this->m_documentId, true),
						'AddTags' => array(
							'class' => 'inputBtn'
						)
					),
					'back' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_REDIRECT,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.back'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_bdj_submission.php?step=2&tAction=new&document_id=' . $this->m_documentId, true),
						'AddTags' => array(
							'class' => 'inputBtn'
						)
					)
				);
				$this->m_formNameInViewobject = 'form_step_3_pwt';
				$this->m_formWrapperClass = 'Document_Permissions_Pwt_Step3_Form_Wrapper';
		}
	}
	
	private function GetFormFieldsStep4() {
		global $rewrite;
		switch ($this->m_documentType) {
			default :
			case (int) PWT_DOCUMENT_TYPE :
				$lModel = new mDocuments_Model();
				$lResult = $lModel->GetSubmittingDocumentInfo($this->m_documentId);
				
				$this->m_formFieldsMetadata = array(
					'document_id' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'DefValue' => $this->m_documentId
					),
					'journal_section' => array(
						'CType' => 'text',
						'VType' => 'string',
						'DefValue' => $lResult['journal_section'],
					),
					'event_id' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'AllowNulls' => true,
					),
					'step' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'DefValue' => $this->m_step
					),
					'document_section' => array (
						'VType' => 'int' ,
						'CType' => 'select' ,
						'DisplayName' => getstr('pjs.documentCreate.Section'),
						'SrcValues' => 'SELECT id as id, title as name FROM pjs.journal_sections WHERE pwt_paper_type_id = ' . (int)$lResult['pwt_paper_type_id'] . '
										ORDER BY id DESC',
						'AllowNulls' => false,
					),
					'review_process_type' => array(
						'VType' => 'int',
						'CType' => 'radio',
						'TransType' => MANY_TO_SQL_ARRAY,
						'SrcValues' => 'SELECT drt.id as id, drt.name as name, drt.title
										FROM pjs.document_review_types drt 
										JOIN pjs.journal_sections js ON drt.id =  ANY(js.review_type_id)
										JOIN pjs.documents d ON d.journal_section_id= js.id
										WHERE d.id = ' . (int)$this->m_documentId,
						'DisplayName' => getstr('pjs.documentCreate.ReviewProcesstype'),
						'AllowNulls' => false,
					),
					'new' => array(
						'CType' => 'action',
						'SQL' => 'SELECT document_review_type_id as review_process_type FROM pjs.documents WHERE id = ' . (int)$this->m_documentId,
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
					),
					'save_finish' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
						'SQL' => 'SELECT * FROM spSaveZookeysDocumentFourthStep(' . $this->m_documentId . ', {review_process_type}, ' . $this->GetUserId() . ')/*{event_id}*/',
						'CheckCaptcha' => 0,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.finish'),
						//~ 'RedirUrl' => $rewrite->EncodeUrl('/document_bdj_submission.php?success=1&event_id={event_id}&document_id=' . $this->m_documentId, true),
						'RedirUrl' => '/document_bdj_submission.php?success=1&event_id={event_id}&document_id=' . $this->m_documentId,
						'AddTags' => array(
							'class' => 'inputBtn',
							'id' => 'finish_button',
							'style'=> 'display:none',
						)
					),
					'save_next' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_REDIRECT,
						'SQL' => 'SELECT * FROM spSaveZookeysDocumentFourthStep(' . $this->m_documentId . ', {review_process_type}, ' . $this->GetUserId() . ')',
						'CheckCaptcha' => 0,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.next'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_bdj_submission.php?document_id=' . $this->m_documentId, true),
						'AddTags' => array(
							'class' => 'inputBtn',
							'id' => 'next_button'
						)
					),
					'back' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_REDIRECT,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.back'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_bdj_submission.php?step=3&tAction=new&document_id=' . $this->m_documentId, true),
						'AddTags' => array(
							'class' => 'inputBtn'
						)
					)
				);
				$this->m_formNameInViewobject = 'form_step_4_pwt';
				$this->m_formWrapperClass = 'Document_Permissions_Pwt_Step4_Form_Wrapper';
		}
	}
	
	private function GetFormFieldsStep5() {
		global $rewrite;
		switch ($this->m_documentType) {
			default :
			case (int) PWT_DOCUMENT_TYPE :
				$this->m_formFieldsMetadata = array(
					'document_id' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'DefValue' => $this->m_documentId
					),
					'event_id' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'AllowNulls' => true,
					),
					'journal_id' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'DefValue' => $this->m_journalId
					),
					'step' => array(
						'CType' => 'hidden',
						'VType' => 'int',
						'DefValue' => $this->m_step
					),
					'save' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
						'SQL' => 'SELECT * FROM spSaveZookeysDocumentFifthStep(' . $this->m_documentId . ', ' . $this->GetUserId() . ')/*{event_id}*/',
						'CheckCaptcha' => 0,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.finish'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_bdj_submission.php?success=1&event_id={event_id}&document_id=' . $this->m_documentId, true),
						'AddTags' => array(
							'class' => 'inputBtn'
						)
					),
					'back' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_REDIRECT,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.back'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_bdj_submission.php?step=4&tAction=new&document_id=' . $this->m_documentId, true),
						'AddTags' => array(
							'class' => 'inputBtn'
						)
					)
				);
				$this->m_formNameInViewobject = 'form_step_5_pwt';
				$this->m_formWrapperClass = 'Document_Permissions_Pwt_Step5_Form_Wrapper';
		}
	}
	
	
}

?>