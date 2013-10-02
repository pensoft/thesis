<?php

class cDocument_Creation_Permissions extends cBase_Controller {
	var $m_documentId;
	var $m_documentType;
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
			);
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
					'form_method' => 'get',
					'form_name' => 'document_permissions_form',
					'fields_metadata' => $this->m_formFieldsMetadata
				);

			}
		}

		$this->m_pageView = new pDocument_Creation_Permissions(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
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
							1 => 'The submission has not been previously published, nor is it currently with another journal for consideration, nor will it be submitted in the future to any other journal before the official rejection or withdrawal of the manuscript by ZooKeys (unless an explanation has been provided in Comments to the Editor and a procedure mutually agreed upon).',
							2 => 'Email addresses of all (co-)authors are available and will be provided during submission.',
							4 => 'The submission file is in PDF format with all figures embedded and is no larger than 20 MB. The text is provided as an additional file in DOC, DOCX, RTF or ODF file format with tables included.',
							8 => 'Original high-resolution files of any figures are uploaded separately as additional files, no larger than 20 MB each.',
							16 => 'The text adheres to the stylistic and bibliographic requirements outlined in the Author Guidelines and is checked by a native English speaker. Where available, URLs for the references are provided.'
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
							1 => 'The authors agree to the terms of this Copyright Notice, which will apply to this submission if and when it is published by this journal.'
						)
					),
					'save' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_REDIRECT,
						'SQL' => 'SELECT * FROM spSaveDocumentPermissionsFirstStep(' . $this->m_documentId . ', ' . $this->GetUserId() . '); /*{preparation_checklist}, {terms_agreement} */',
						'CheckCaptcha' => 0,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.save'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_pwt_permissions.php?document_id=' . $this->m_documentId, true),
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
					'agree_to_cover_all_taxes' => array(
						'CType' => 'radio',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.documentCreate.agreeToCoverAllTaxes'),
						'AllowNulls' => true,
						'SrcValues' => array(
							1 => 'I agree to cover open access charges, if my manuscript is accepted for publication'
						),
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'want_15_discount' => array(
						'CType' => 'radio',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.documentCreate.want15Discount'),
						'AllowNulls' => true,
						'SrcValues' => array(
							1 => 'I would appreciate a discount of 15 % because of one of the following reasons:'
						),
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'fifteen_discount_reasons' => array(
						'CType' => 'checkbox',
						'VType' => 'int',
						'TransType' => MANY_TO_SQL_ARRAY,
						'DisplayName' => getstr('pjs.documentCreate.15DiscountReasons'),
						'AllowNulls' => true,
						'SrcValues' => array(
							1 => 'I am an editor in this journal',
							2 => 'I often review manuscripts for ZooKeys (i.e., three or more during the previous and/or present calendar year)'
						),
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'want_10_discount' => array(
						'CType' => 'radio',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.documentCreate.want10Discount'),
						'AllowNulls' => true,
						'SrcValues' => array(
							1 => 'I would appreciate a discount of 10 % because of one of the following reasons:'
						),
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'ten_discount_reasons' => array(
						'CType' => 'checkbox',
						'VType' => 'int',
						'TransType' => MANY_TO_SQL_ARRAY,
						'DisplayName' => getstr('pjs.documentCreate.10DiscountReasons'),
						'AllowNulls' => true,
						'SrcValues' => array(
							1 => 'I am a member of an institution, which subscribes for the print version of this journal',
							2 => 'I work privately and I am not associated with an institution',
							4 => 'I am graduate or PhD student and I am the first author of this manuscript',
							8 => 'I live and work in lower-middle-income country and I am the sole author of this manuscript'
						),
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'want_waiver_discount' => array(
						'CType' => 'radio',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.documentCreate.wantWaiverDiscount'),
						'AllowNulls' => true,
						'SrcValues' => array(
							1 => 'I am applying for a waiver because of one of the following reasons (one waiver per year per author for a total number of 15 printed pages (or first 15 pages of a larger manuscript):'
						),
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'waiver_discount_reasons' => array(
						'CType' => 'checkbox',
						'VType' => 'int',
						'TransType' => MANY_TO_SQL_ARRAY,
						'DisplayName' => getstr('pjs.documentCreate.waiverDiscountReasons'),
						'AllowNulls' => true,
						'SrcValues' => array(
							1 => 'I am retired',
							2 => 'I live and work in a low-income country and I am the sole author of this manuscript'
						),
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'use_special_conditions' => array(
						'CType' => 'radio',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.documentCreate.useSpecialConditions'),
						'AllowNulls' => true,
						'SrcValues' => array(
							1 => 'I use special conditions by agreement'
						),
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'person_to_charge' => array(
						'CType' => 'radio',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.documentCreate.personToCharge'),
						'AllowNulls' => false,
						'SrcValues' => array(
							1 => 'The submitting author',
							2 => 'Different person'
						),
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'person_to_charge_name' => array(
						'CType' => 'textarea',
						'VType' => 'string',
						'DisplayName' => getstr('pjs.documentCreate.personToName'),
						'AllowNulls' => true,
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'comments' => array(
						'CType' => 'textarea',
						'VType' => 'string',
						'DisplayName' => getstr('pjs.documentCreate.comments'),
						'AllowNulls' => true,
						'AddTags' => array(
							'class' => 'inputFld'
						)
					),
					'save' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_REDIRECT,
						'SQL' => 'SELECT * FROM spSaveDocumentPermissionsSecondStep(' . $this->m_documentId . ', {agree_to_cover_all_taxes}, {want_15_discount}, {fifteen_discount_reasons}
							, {want_10_discount}, {ten_discount_reasons}, {want_waiver_discount}, {waiver_discount_reasons}, {use_special_conditions}
							, {person_to_charge}, {person_to_charge_name}, {comments}, ' . $this->GetUserId() . '
						)',
						'CheckCaptcha' => 0,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.save'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_pwt_permissions.php?success=1&document_id=' . $this->m_documentId, true),
						'AddTags' => array(
							'class' => 'inputBtn'
						)
					),
					'back' => array(
						'CType' => 'action',
						'ActionMask' => ACTION_REDIRECT,
						'ControllerMethodName' => 'SaveFormData',
						'DisplayName' => getstr('pjs.documentCreate.back'),
						'RedirUrl' => $rewrite->EncodeUrl('/document_pwt_permissions.php?step=1&document_id=' . $this->m_documentId, true),
						'AddTags' => array(
							'class' => 'inputBtn'
						)
					)
				);
				$this->m_formNameInViewobject = 'form_step_2_pwt';
				$this->m_formWrapperClass = 'Document_Permissions_Pwt_Step2_Form_Wrapper';
		}
	}
}

?>