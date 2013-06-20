<?php

class cChange_Review_Type_Controller extends cBase_Controller {
	function __construct() {
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();
		$this->InitViewingModeData();
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
	
		$lReviewTypeId = (int)$this->GetValueFromRequestWithoutChecks('review_type_id');
		$lDocumentId = (int)$this->GetValueFromRequestWithoutChecks('document_id');  
		//~ if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId) || 
			//~ !$this->GetUserId() || !$this->m_models['mEdit_Model']->CheckJournalRights($this->GetUserId(), $lJournalId)){
			//~ header('Location: /index.php');
		//~ }
		$this->m_models['mTask_Model'] = new mTask_Model();
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'edit_Review_TypeForm_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'review_edit_form',
			'use_captcha' => 0,
			'debug' => 1,
			'form_method' => 'POST',
			//'js_validation' => $lJsValidation,
			'form_name' => 'review_edit_form',
			'dont_close_session' => true,
			'fields_metadata' => array(
				'document_id' => array(
					'VType' => 'int',
					'CType' => 'hidden',
					'defValue' => $lDocumentId,
					'AllowNulls' => true,
				),		
				'review_type' => array(
					'CType' => 'select',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.select.review.type'),
						'SrcValues' => 'SELECT drt.id as id, drt.name as name, drt.title
											FROM pjs.document_review_types drt 
											JOIN pjs.journal_sections js ON drt.id =  ANY(js.review_type_id)
											JOIN pjs.documents d ON d.journal_section_id= js.id
											WHERE d.id = ' . $lDocumentId . ' ORDER BY drt.id = ' . $lReviewTypeId . ' DESC',
						'AllowNulls' => false,
						'AddTags' => array(
							'class' => 'review_types',
						),
				),
				'cancel' => array(
					'CType' => 'action',
					'SQL' => '',
					'CheckCaptcha' => 0,
					'DisplayName' => 'Cancel',
					'ActionMask' => ACTION_CHECK,
					'AddTags' => array(
						//~ 'onclick' => 'return false;',
					),
				),
				'save' => array(
					'CType' => 'action',
					//~ 'SQL' => 'SELECT * FROM pjs.spUpdateReviewType(' . $lDocumentId . ', {review_type})',
					'SQL' => '',
					'CheckCaptcha' => 0,
					'DisplayName' => 'Save',
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_FETCH, // | ACTION_REDIRECT,
					//~ 'RedirUrl' => '/email_templates',
					'AddTags' => array(
						'onclick' => 'updateReviewTypeAndClose(' . $lDocumentId . ')',
					),
				),
			)
		);
		$this->AddJournalObjects($lJournalId);
		$this->m_pageView = new pChange_Review_Type_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}
}

?>