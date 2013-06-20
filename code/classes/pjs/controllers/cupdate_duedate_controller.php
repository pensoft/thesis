<?php

class cUpdate_Duedate_Controller extends cBase_Controller {
	var $m_action;
	function __construct() {
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();
		$this->InitViewingModeData();
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		$this->m_action = $this->GetValueFromRequestWithoutChecks('action');
		$lRoundId = $this->GetValueFromRequestWithoutChecks('roundid');
		$lRoundUserId = $this->GetValueFromRequestWithoutChecks('rounduserid');
		$lCurrentDueDate = $this->GetValueFromRequestWithoutChecks('roundduedate');
		$lCurrentDueDate = explode(' ', $lCurrentDueDate);
		$lCurrentDueDate = $lCurrentDueDate[0];
		$lCurrentDueDate = explode('/', $lCurrentDueDate);
		$lCurrentDueDate = array_reverse($lCurrentDueDate);
		$lCurrentDueDate = $lCurrentDueDate[0] . '/' . $lCurrentDueDate[1] . '/' . $lCurrentDueDate[2];  
		//~ if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId) || 
			//~ !$this->GetUserId() || !$this->m_models['mEdit_Model']->CheckJournalRights($this->GetUserId(), $lJournalId)){
			//~ header('Location: /index.php');
		//~ }
		$this->m_models['mTask_Model'] = new mTask_Model();
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'editEmailTemplateForm_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'duedate_edit_form',
			'use_captcha' => 0,
			//~ 'debug' => 1,
			'form_method' => 'POST',
			//'js_validation' => $lJsValidation,
			'form_name' => 'edit_due_date_form',
			'dont_close_session' => true,
			'fields_metadata' => array(
				'id' => array(
					'VType' => 'int',
					'CType' => 'hidden',
					'AllowNulls' => true,
				),
				'parent_id' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'AllowNulls' => true,
				),			
				'duedate' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.due_date'),
					'AllowNulls' => true,
					'DefValue' => $lCurrentDueDate,
					'AddTags' => array(
						'class' => 'template_subject',
						'id' => 'dueDate',
						'style' => 'width: 70%',
					),
				),
				'showedit' => array(
					'CType' => 'action',
					//~ 'SQL' => 'SELECT * FROM pjs.spmanageemailtemplates(0, ' . $lTemplateId . ', null, null, null, null, null, null, null, null)',
					'DisplayName' => getstr('pjs.save.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
				'cancel' => array(
					'CType' => 'action',
					'SQL' => '',
					'CheckCaptcha' => 0,
					'DisplayName' => 'Cancel',
					'ActionMask' => ACTION_CHECK,
					'AddTags' => array(
						//~ 'onclick' => 'popupClosingAndReloadParent()',
					),
				),
				'save' => array(
					'CType' => 'action',
					//~ 'SQL' => 'SELECT * FROM usr',
					'CheckCaptcha' => 0,
					'DisplayName' => 'Save',
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_FETCH, // | ACTION_REDIRECT,
					//~ 'RedirUrl' => '/email_templates',
					'AddTags' => array(
						'onclick' => 'updateDueDateandClose(\'' . $this->m_action . '\',' . $lRoundId . ', ' . $lRoundUserId . '); return false;',
					),
				),
			)
		);
		$this->AddJournalObjects($lJournalId);
		$this->m_pageView = new pDueDate_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}
}

?>